<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionInterest;
use App\Models\ContributionSummary;
use App\Models\FailedInterestApi;
use App\Models\FullWithdrawalLog;
use App\Models\Membership;
use App\Models\PartialWithdrawalLog;
use App\Models\RecentlyUpdated;
use App\Models\Regiment;
use http\Client\Curl\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class InterestCalculationController extends Controller
{
    public function create(){
        $interest = ContributionInterest::where('status', 1)->first();
        $regiments = Regiment::all();

        $yesterday = \Carbon\Carbon::yesterday();
        $today = \Carbon\Carbon::today();
        $recently = RecentlyUpdated::with('regiments','category')
            ->whereBetween('date', [$yesterday, $today])
            ->get();
        return view('interestCalculations.create', compact('interest', 'regiments', 'recently'));

    }
    private $apiBaseUrl = 'http://192.168.1.67:5222';
    private $apiToken = null;
    private function authenticateApi()
    {
        try {
            $response = Http::post($this->apiBaseUrl . '/api/Auth/login', [
                'username' => 'SLArmy',
                'password' => 'SLArmy@2025'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->apiToken = $data['token'] ?? null;

                if ($this->apiToken) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
    private function sendARBatchUpdate(array $payloads)
    {

        $now = now();
        $filename = 'ar_batch_update_' . $now->format('Ymd_His') . '_' . uniqid() . '.log';
        $logPath = storage_path('logs/adjustments/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }
        try {
            $response = Http::timeout(120)
                ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
            ])->post($this->apiBaseUrl . '/api/Transaction/ARBatchUpdate', $payloads);
            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payload[0]['customer'] ?? 'N/A',
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => json_decode($response->body(), true) ?? $response->body(),
            ];

            file_put_contents($logPath, print_r($logData, true));
//            if (!$response->successful()) {
//                foreach ($payloads as $payload) {
//                    FailedInterestApi::create([
//                        'enumber' => explode('-', $payload['customer'])[0],
//                        'yearly_interest' => $payload['credit'],
//                        'year' => now()->year,
//                        'icp_id' => $payload['reference'],
//                        'reason' => 'API response failed: ' . $response->body(),
//                    ]);
//                }
//                return false;
//            }

            return true;

        } catch (\Exception $e) {
            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payload[0]['customer'] ?? 'N/A',
                'success' => false,
                'exception' => $e->getMessage(),
            ];

            file_put_contents($logPath, print_r($logData, true));
//            foreach ($payloads as $payload) {
//                FailedInterestApi::create([
//                    'enumber' => explode('-', $payload['customer'])[0],
//                    'yearly_interest' => $payload['credit'],
//                    'year' => now()->year,
//                    'icp_id' => $payload['reference'],
//                    'reason' => 'Exception: ' . $e->getMessage(),
//                ]);
//            }
            return false;
        }
    }
    public function store(Request $request)
    {
        $regiment = $request->regiment_id;
        $category = $request->category_id;
        $year = $request->year;
        $icpId = $request->icp_id;
        $interestRate = $request->interest_rate;

        $monthRange = $this->getMonthRange($icpId);
        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }

        $contribution_yearly_summary = ContributionSummary::with(['membership:id,enumber,regimental_number'])
            ->where('year', $year)
            ->where('icp_id', $icpId)
            ->whereHas('membership', function ($query) use ($regiment, $category) {
                $query->where('regiment_id', $regiment)
                    ->where('category_id', $category);
            })
            ->get();

        if ($contribution_yearly_summary->isEmpty()) {
            return redirect()->back()->with('info', 'No data found for selected parameters.');
        }

        $membershipIds = $contribution_yearly_summary->pluck('membership_id')->all();

        // Prefetch all contributions in one query grouped by membership_id
        $contributionsByMember = Contribution::where('year', $year)
            ->whereIn('membership_id', $membershipIds)
            ->whereBetween('month', $monthRange)
            ->get()
            ->groupBy('membership_id');

        // Prefetch latest partial withdrawals keyed by membership_id
        $partialWithdrawals = PartialWithdrawalLog::whereIn('membership_id', $membershipIds)
            ->orderByDesc('id')
            ->get()
            ->unique('membership_id')
            ->keyBy('membership_id');

        // Prefetch latest full withdrawals keyed by membership_id
        $fullWithdrawals = FullWithdrawalLog::whereIn('membership_id', $membershipIds)
            ->orderByDesc('id')
            ->get()
            ->unique('membership_id')
            ->keyBy('membership_id');

        $bulkPayloads = [];
        $newSummaries = [];

        DB::transaction(function () use (
            $contribution_yearly_summary,
            $contributionsByMember,
            $partialWithdrawals,
            $fullWithdrawals,
            $interestRate,
            $year,
            $icpId,
            &$bulkPayloads,
            &$newSummaries
        ) {
            foreach ($contribution_yearly_summary as $summary) {
                $member = $summary->membership;
                $memberId = $summary->membership_id;

                // Safely get contributions sum, avoid undefined key error
                $contribution_amount = isset($contributionsByMember[$memberId])
                    ? $contributionsByMember[$memberId]->sum('amount')
                    : 0;

                $summary->contribution_amount += $contribution_amount;
                $summary->yearly_interest = $summary->opening_balance * $interestRate / 100;

                // Safely get withdrawals, avoid undefined key error
                $withdrawal = $partialWithdrawals[$memberId] ?? null;
                $fullWithdrawal = $fullWithdrawals[$memberId] ?? null;

                if ($withdrawal) {
                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
                        + $summary->opening_balance - $withdrawal->withdrawal_amount;

                    $withdrawal->is_batch_to_do = 0;
                    $withdrawal->save();
                } elseif ($fullWithdrawal) {
                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
                        + $summary->opening_balance - $fullWithdrawal->withdrawal_amount;

                    $fullWithdrawal->is_batch_to_do = 0;
                    $fullWithdrawal->save();
                } else {
                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
                        + $summary->opening_balance;
                }

                $summary->transaction_date = now()->format('Y-m-d');
                $summary->save();

                // Determine next ICP id and transaction date using PHP 8 match (or switch alternative)
                $nextIcpId = match ($icpId) {
                    10 => 20,
                    20 => 30,
                    30 => 40,
                    40 => 10,
                    default => $icpId, // fallback
                };

                $nextYear = ($icpId === 40) ? $year + 1 : $year;

                $transactionDate = match ($icpId) {
                    10 => now()->format('Y-06-30'),
                    20 => now()->format('Y-09-30'),
                    30 => now()->format('Y-12-31'),
                    40 => now()->addYear()->format('Y-03-31'),
                    default => now()->format('Y-m-d'),
                };

                // Prepare new summary record for bulk insert
                $newSummaries[] = [
                    'opening_balance' => $summary->closing_balance,
                    'membership_id' => $memberId,
                    'year' => $nextYear,
                    'icp_id' => $nextIcpId,
                    'version' => 0,
                    'transaction_date' => $transactionDate,
                ];

                // Prepare API payload
                $bulkPayloads[] = [
                    "aRbatchId" => 'ARB003',
                    "amount" => $summary->yearly_interest,
//                    "credit" => $summary->yearly_interest,
//                    "debit" => 0,
                    "transactionDate" => now()->toIso8601String(),
                    "customer" => $member->regimental_number . '-' . $member->enumber,
                    "description" => 'Quarterly Interest Payment',
                    "reference" => 'Interest for Q- ' . $summary->icp_id . ' of ' . $summary->year,
                    "comments" => 'Yearly interest credited',
                    "transactioncCodeID" => 'MIE',
                    "taxTypeID" => 1,
                    "gl" => false,
                    "ar" => true,
                ];
            }

            // Bulk insert all new summaries at once
            if (!empty($newSummaries)) {
                ContributionSummary::insertOrIgnore($newSummaries);
            }
        });

        // Call external API outside DB transaction
        $failedInterestApiCount = 0;
        if (!$this->sendARBatchUpdate($bulkPayloads)) {
            $failedInterestApiCount = count($bulkPayloads);
        }

        RecentlyUpdated::create([
            'regiment_id' => $regiment,
            'category_id' => $category,
            'year' => $year,
            'icp_id' => $icpId,
            'count' => count($contribution_yearly_summary),
            'date' => now()->format('Y-m-d'),
        ]);

        if ($failedInterestApiCount > 0) {
            return redirect()->back()->with('warning', "Interest committed with {$failedInterestApiCount} API failures.");
        }

        return redirect()->back()->with('success', 'Interest committed successfully.');
    }


//    public function store(Request $request)
//    {
//        $regiment = $request->regiment_id;
//        $category = $request->category_id;
//        $year = $request->year;
//        $icpId = $request->icp_id;
//        $interestRate = $request->interest_rate;
//
//        $contribution_yearly_summary = ContributionSummary::with(['membership:id,enumber,regimental_number'])
//            ->where('year', $year)
//            ->where('icp_id', $icpId)
//            ->whereHas('membership', function ($query) use ($regiment, $category) {
//                $query->where('regiment_id', $regiment)
//                    ->where('category_id', $category);
//            })
//            ->get();
//
//        try {
//            $bulkPayloads = [];
//
//            foreach ($contribution_yearly_summary as $summary) {
//                $summary->yearly_interest = $summary->opening_balance * $interestRate / 100;
//                $withdrawal = PartialWithdrawalLog::where('membership_id', $summary->membership_id)
//                    ->orderBy('id', 'desc')->first();
//
//                $fullWithdrawal = FullWithdrawalLog::where('membership_id', $summary->membership_id)
//                    ->orderBy('id', 'desc')->first();
//
//                $contribution_amount = 0;
//                $contributions = $summary->membership->contributions()
//                    ->where('year', $year)
//                    ->whereBetween('month', $this->getMonthRange($icpId))
//                    ->get();
//
//                foreach ($contributions as $contribution) {
//                    $contribution_amount += $contribution->amount;
//                }
//
//                $summary->contribution_amount += $contribution_amount;
//
//                if ($withdrawal) {
//                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
//                        + $summary->opening_balance - $withdrawal->withdrawal_amount;
//                    $withdrawal->is_batch_to_do = 0;
//                    $withdrawal->save();
//                } elseif ($fullWithdrawal) {
//                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
//                        + $summary->opening_balance - $fullWithdrawal->withdrawal_amount;
//                    $fullWithdrawal->is_batch_to_do = 0;
//                    $fullWithdrawal->save();
//                } else {
//                    $summary->closing_balance = $summary->contribution_amount + $summary->yearly_interest
//                        + $summary->opening_balance;
//                }
//
//                $summary->transaction_date = now()->format('Y-m-d');
//                $summary->save();
//
//                $newSummary = new ContributionSummary();
//                $newSummary->opening_balance = $summary->closing_balance;
//                $newSummary->membership_id = $summary->membership_id;
//                $newSummary->year = $year + ($icpId == 40 ? 1 : 0);
//                $newSummary->version = 0;
//
//                switch ($icpId) {
//                    case 10:
//                        $newSummary->icp_id = 20;
//                        $newSummary->transaction_date = now()->format('Y-06-30');
//                        break;
//                    case 20:
//                        $newSummary->icp_id = 30;
//                        $newSummary->transaction_date = now()->format('Y-09-30');
//                        break;
//                    case 30:
//                        $newSummary->icp_id = 40;
//                        $newSummary->transaction_date = now()->format('Y-12-31');
//                        break;
//                    case 40:
//                        $newSummary->icp_id = 10;
//                        $newSummary->transaction_date = now()->format((now()->year + 1) . '-03-31');
//                        break;
//                }
//
//                    $newSummary->save();
//
//                $bulkPayloads[] = [
//                    "aRbatchId" => 'ARB003',
//                    "credit" => $summary->yearly_interest,
//                    "debit" => 0,
//                    "transactionDate" => now()->toIso8601String(),
//                    "customer" => $summary->membership->regimental_number . '-' . $summary->membership->enumber,
//                    "description" => 'Quarterly Interest Payment',
//                    "reference" => 'Interest for Q- ' . $summary->icp_id . ' of ' . $summary->year,
//                    "comments" => 'Yearly interest credited',
//                    "transactioncCodeID" => 'MIE',
//                    "taxTypeID" => 1,
//                    "gl" => false,
//                    "ar" => true,
//                ];
//            }
//
//            $failedInterestApiCount = 0;
//
//            if (!$this->sendInterestARBatchUpdate($bulkPayloads)) {
//                $failedInterestApiCount = count($bulkPayloads);
//            }
//
//            RecentlyUpdated::create([
//                'regiment_id' => $regiment,
//                'category_id' => $category,
//                'year' => $year,
//                'icp_id' => $icpId,
//                'count' => count($contribution_yearly_summary),
//                'date' => now()->format('Y-m-d'),
//            ]);
//
//            if ($failedInterestApiCount > 0) {
//                return redirect()->back()->with('warning', "Interest committed with {$failedInterestApiCount} API failures.");
//            } else {
//                return redirect()->back()->with('success', 'Interest committed successfully.');
//            }
//        } catch (QueryException $e) {
//            return redirect()->back()->with('error', 'Try to commit interest for already committed members.');
//        }
//    }

    private function getMonthRange($icpId)
    {
        switch ($icpId) {
            case 10:
                return [1, 2, 3];
            case 20:
                return [4, 5, 6];
            case 30:
                return [7, 8, 9];
            case 40:
                return [10, 11, 12];
            default:
                return [];
        }
    }

    public function editCalculation($id){

        $yearlyContributions = DB::table('contribution_yearly_summary')
            ->join('contribution_interests', function ($join) {
                $join->on('contribution_yearly_summary.year', '=', 'contribution_interests.year')
                    ->on('contribution_yearly_summary.icp_id', '=', 'contribution_interests.icp_id');
            })
            ->select(
                'contribution_yearly_summary.*',
                'contribution_interests.interest_rate'
            )
            ->where('membership_id', $id)->get();

        $membershipId = $id;
        return view('interestCalculations.edit', compact('yearlyContributions', 'membershipId'));

    }
    public function updateCalculation($id, Request $request){
        $validatedData = $request->validate([
            'opening_balance' => 'required',
            'yearly_interest' => 'required',
            'contribution_amount' => 'required',
            'closing_balance' => 'required'
        ]);

        $validatedData['current_user'] = Auth::user()->name;
        $yearlyContribution = ContributionSummary::find($id);
        $yearlyContribution->update($validatedData);
        return redirect()->back()->with('success', 'Contribution summary Updated Successfully');
    }
    public function createYCS($id){

        $membershipId = $id;
        $interestRates = ContributionInterest::all();
//        dd($interestRates);
        return view('interestCalculations.create-ycs', compact('membershipId', 'interestRates'));

    }
    public function storeYCS($id, Request $request){

        $validatedData = $request->validate([
            'year' => 'required',
            'year.*' => 'required|numeric',
            'icp_id' => 'required',
            'icp_id.*' => 'required|numeric',
            'opening_balance' => 'required',
            'opening_balance.*' => 'required|numeric',
            'yearly_interest' => 'required',
            'yearly_interest.*' => 'required|numeric',
            'contribution_amount' => 'required',
            'contribution_amount.*' => 'required|numeric',
            'closing_balance' => 'required',
            'closing_balance.*' => 'required|numeric',
        ]);

        $contributionSummaries = [];
        foreach ($validatedData['opening_balance'] as $key => $openingBalance) {
            // Determine the transaction date based on icp_id
            $year = $validatedData['year'][$key];
            $icp_id = $validatedData['icp_id'][$key];

            switch ($icp_id) {
                case 0:
                case 2:
                case 40:
                    $transactionDate = \Carbon\Carbon::createFromDate($year, 12, 30);
                    break;
                case 1:
                case 20:
                    $transactionDate = \Carbon\Carbon::createFromDate($year, 6, 30);
                    break;
                case 10:
                    $transactionDate = \Carbon\Carbon::createFromDate($year, 3, 30);
                    break;
                case 30:
                    $transactionDate = \Carbon\Carbon::createFromDate($year, 9, 30);
                    break;
                default:
                    $transactionDate = now(); // Fallback if icp_id doesn't match
            }

            $contributionSummaries[] = [
                'membership_id' => $id,
                'currentuser' => Auth::user()->name,
                'version' => 0,
                'year' => $year,
                'icp_id' => $icp_id,
                'opening_balance' => $openingBalance,
                'yearly_interest' => $validatedData['yearly_interest'][$key],
                'contribution_amount' => $validatedData['contribution_amount'][$key],
                'closing_balance' => $validatedData['closing_balance'][$key],
                'transaction_date' => $transactionDate,
            ];
        }

//        dd($contributionSummaries);
        ContributionSummary::insert($contributionSummaries);

        return redirect()->route('memberships.show', $id)->with('success', 'Contribution summary updated successfully');
    }

    public function destroy($id)
    {
        ContributionSummary::find($id)->delete();
        return redirect()->back()
            ->with('success','Interest summary deleted successfully');
    }
}
