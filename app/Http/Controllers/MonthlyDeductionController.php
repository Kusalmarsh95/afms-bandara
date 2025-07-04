<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\ContributionAdditional;
use App\Models\ContributionAssign;
use App\Models\ContributionCorrection;
use App\Models\ContributionFailures;
use App\Models\ContributionHistory;
use App\Models\ContributionSummary;
use App\Models\CorrectionAssign;
use App\Models\FailedAdjustmentApi;
use App\Models\FullWithdrawalApplication;
use App\Models\LoanApplication;
use App\Models\LoanRecoveryPayment;
use App\Models\Membership;
use App\Models\MonthlyDeduction;
use App\Models\PartialWithdrawalApplication;
use App\Models\RecentContribution;
use App\Models\RecentRepayment;
use App\Models\Regiment;
use App\Models\RepaymentBatch;
use App\Models\RepaymentFailures;
use App\Models\User;
use DOMDocument;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use XMLReader;

class MonthlyDeductionController extends Controller
{
    public function index()
    {
        return view('monthlyDeductions.index');
    }
    public function newContributions()
    {
        $contributionsAddition = ContributionAdditional::with('membership')
            ->where('accepted', '!=', 1)
            ->get();
        return view('monthlyDeductions.approval', compact('contributionsAddition'));
    }
    public function corrections()
    {
        $corrections = ContributionCorrection::with('membership')
            ->where('accepted', '!=', 1)
            ->get();
        return view('monthlyDeductions.correction-approval', compact('corrections'));
    }
    public function create($id)
    {
        $membership = Membership::with('ranks')->find($id);
        $users = User::all();
        return view('monthlyDeductions.create',compact('membership', 'users'));
    }
    public function correctionCreate($id)
    {
        $membership = Membership::with('ranks')->find($id);
        $users = User::all();
        $contributonSummary = ContributionSummary::where('membership_id', $id)->get();
//        dd($contributonSummary);
        if ($contributonSummary->count() < 2) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Oops, Your account do not have history of interest calculation records. Please update and try again.');
        } else {
            return view('monthlyDeductions.correction',compact('membership', 'users'));
        }

    }
    public function store(Request $request, $id)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'year' => 'required',
            'month' => 'required',
            'type' => 'required',
            'remark' => 'nullable',
            'reason' => 'nullable',
        ]);
        $validatedData['membership_id'] = $id;
        $validatedData['deposit_date'] = now()->format('Y-m-d');
        $validatedData['accepted'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;;
        $validatedData['approved_by'] = 'Pending';
        $validatedData['created_system'] = 'AFMS';

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);

        $contributionsAddition = ContributionAdditional::create($validatedData);

        $validatedAssign['additional_id'] = $contributionsAddition->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Add a new contribution';
        $validatedAssign['fwd_to_reason'] = 'For Approval';
        ContributionAssign::create($validatedAssign);

        return redirect()->route('memberships.show', $id)
            ->with('success', 'Contribution Send for Approval');
    }
    public function correctionStore(Request $request, $id)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'type' => 'required',
            'remark' => 'required',
        ]);
        $validatedData['membership_id'] = $id;
        $validatedData['transaction_date'] = now()->format('Y-m-d');
        $validatedData['accepted'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;;
        $validatedData['approved_by'] = 'Pending';
        $validatedData['created_system'] = 'AFMS';

        $contributionsCorrection = ContributionCorrection::create($validatedData);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);

        $validatedAssign['correction_id'] = $contributionsCorrection->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = '';
        $validatedAssign['fwd_to_reason'] = 'For Approval';
        CorrectionAssign::create($validatedAssign);

        return redirect()->route('memberships.show', $id)
            ->with('success', 'Contribution Correction Send for Approval');
    }
    public function apporvalView($id)
    {
        $contributionsAddition = ContributionAdditional::with('membership')->find($id);
        $users = User::all();
        return view('monthlyDeductions.show-approval',compact('contributionsAddition', 'users'));
    }
    public function correctionApporval($id)
    {
        $correction = ContributionCorrection::with('membership')->find($id);
        $users = User::all();
        return view('monthlyDeductions.correction-showapproval',compact('correction', 'users'));
    }
    public function approveStore(Request $request, $id)
    {
        $contributionsAddition = ContributionAdditional::with(['membership:id,enumber,regimental_number,name'])
            ->findOrFail($id);
        $membership_id = $contributionsAddition->membership_id;

        $action = $request->input('approval');

        if ($action === 'approve') {
            // Mark contribution as accepted and save approval details
            $contributionsAddition->accepted = 1;
            $contributionsAddition->transaction_date = now()->format('Y-m-d');
            $contributionsAddition->approved_by = Auth::user()->name;
            $contributionsAddition->save();

            // Determine ICP ID based on month
            $year = $request->year;
            $month = $request->month;
            $icpId = 0;
            if ($month >= 1 && $month <= 3) {
                $icpId = 10;
            } elseif ($month >= 4 && $month <= 6) {
                $icpId = 20;
            } elseif ($month >= 7 && $month <= 9) {
                $icpId = 30;
            } else {
                $icpId = 40;
            }

            // Adjust year for ICP 10
            $newYear = $icpId == 10 ? $year + 1 : $year;

            // Update or create contribution summary
            $existingContribution = ContributionSummary::where('membership_id', $membership_id)
                ->where('year', $year)
                ->where('icp_id', $icpId)
                ->first();

            if (!$existingContribution) {
                $newSummary = new ContributionSummary();
                $newSummary->version = 1;
                $newSummary->membership_id = $membership_id;
                $newSummary->icp_id = $icpId;
                $newSummary->year = $newYear;
                $newSummary->contribution_amount = ($contributionsAddition->type == 'Deposit')
                    ? $contributionsAddition->amount
                    : -$contributionsAddition->amount;
                $newSummary->transaction_date = now()->format('Y-m-d');
                $newSummary->save();
            } else {
                if ($contributionsAddition->type == 'Deposit') {
                    $existingContribution->contribution_amount += $contributionsAddition->amount;
                } else {
                    $existingContribution->contribution_amount -= $contributionsAddition->amount;
                }
                $existingContribution->save();
            }

            if (!$this->authenticateApi()) {
                return response()->json(['error' => 'Failed to authenticate with external API'], 500);
            }

            if ($contributionsAddition->type === 'Refund') {
                $reasonToBatchId = [
                    'Unit Deduction' => 'ARB014',
                    'AWOL Deduction' => 'ARB015',
                    'Advance B Recovery' => 'ARB004',
                ];
                $reasonToCodeId = [
                    'Unit Deduction' => 'MIRU',
                    'AWOL Deduction' => 'MIAW',
                    'Advance B Recovery' => 'MIRA',
                ];

                $reason = $contributionsAddition->reason;

                $aRbatchId = $reasonToBatchId[$reason] ?? 'ARB004';
                $transactioncCodeID = $reasonToCodeId[$reason] ?? 'MIRA';

                $payload = [[
                    "aRbatchId" => $aRbatchId,
                    'customer' => $contributionsAddition->membership->regimental_number . '-' . ($contributionsAddition->membership->enumber ?? '000000'),
//                    "credit" => 0,
//                    "debit" => $contributionsAddition->amount,
                    "amount" => $contributionsAddition->amount ?? 0,
                    "transactionDate" => now()->toIso8601String(),
                    "description" => 'Additional Deduction',
                    "reference" => $aRbatchId . ' with ' . $contributionsAddition->amount,
                    "comments" => 'Deduction',
                    "transactioncCodeID" => $transactioncCodeID,
                    "taxTypeID" => 1,
                    "gl" => false,
                    "ar" => true,
                ]];

                $apiSuccess = $this->sendARBatchUpdate($payload);
            } elseif ($contributionsAddition->type === 'Deposit') {
                $cashBookPayload = [[
                    'cashbookId' => 'CB081',
                    'credit' => $contributionsAddition->amount,
                    'debit' => 0,
                    'customer' => $contributionsAddition->membership->regimental_number . '-' . ($contributionsAddition->membership->enumber ?? '000000'),
                    'transactionDate' => now()->toIso8601String(),
                    'description' => $contributionsAddition->reason,
                    'comments' => 'Additional deposit',
                    'reference' => $contributionsAddition->membership->name,
                    'gl' => false,
                    'ar' => true,
                ]];

                $response = $this->sendCashBookUpdate($cashBookPayload);
                $apiSuccess = $response['success'];

//                if (!$apiSuccess) {
//                    FailedAdjustmentApi::create([
//                        'enumber' => $contributionsAddition->membership->enumber,
//                        'amount' => $contributionsAddition->amount,
//                        'reference' => $contributionsAddition->membership->name,
//                        'reason' => 'Cash Book Update failed: ' . $response['message'],
//                    ]);
//                }
            }

            // Optionally track approval routing
             ContributionAssign::create([
                 'additional_id' => $contributionsAddition->id,
                 'fwd_by' => Auth::user()->id,
                 'fwd_to' => '',
                 'fwd_by_reason' => 'Approved',
                 'fwd_to_reason' => '',
             ]);

            if (!$apiSuccess) {
                return redirect()->route('additional-contribution')
                    ->with('error', 'Contribution approved but API update failed.');
            }

            return redirect()->route('additional-contribution')
                ->with('success', 'Contribution approved successfully and API updated.');

        } elseif ($action === 'reject') {
            // Mark contribution as rejected
            $contributionsAddition->accepted = 2;
            $contributionsAddition->save();

            // Validate rejection input
            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);

            $validatedAssign['additional_id'] = $contributionsAddition->id;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Rejected';

            ContributionAssign::create($validatedAssign);

            return redirect()->route('additional-contribution')
                ->with('success', 'Contribution Rejected');
        }

        return redirect()->back()->with('error', 'Invalid action');
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

            if (!$response->successful()) {
                foreach ($payloads as $payload) {
                    FailedAdjustmentApi::create([
                        'enumber' => explode('-', $payload['customer'])[0] ?? 'N/A',
                        'amount' => $payload['debit'],
                        'reference' => $payload['reference'] ?? '',
                        'reason' => 'API response failed: ' . $response->body(),
                    ]);
                }
                return false;
            }

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

            foreach ($payloads as $payload) {
                FailedAdjustmentApi::create([
                    'enumber' => explode('-', $payload['customer'])[0] ?? 'N/A',
                    'amount' => $payload['debit'],
                    'reference' => $payload['reference'] ?? '',
                    'reason' => 'Exception: ' . $e->getMessage(),
                ]);
            }
            return false;
        }
    }

    public function correctionApproveStore(Request $request, $id)
    {
        $correction = ContributionCorrection::with(['membership:id,enumber,regimental_number,name'])
            ->findOrFail($id);
        $membership_id = $correction->membership_id;
        $action = $request->input('approval');
        if ($action === 'approve') {
            if (!$this->authenticateApi()) {
                return response()->json(['error' => 'Failed to authenticate with external API'], 500);
            }

            $existingContribution = ContributionSummary::where('membership_id', $membership_id)
                ->latest('transaction_date')
                ->first();

            if ($existingContribution) {
                if ($correction->type == 'Addition') {
                    $existingContribution->opening_balance += $correction->amount;
                } elseif ($correction->type == 'Deduction'){
                    $existingContribution->opening_balance -= $correction->amount;
                }
                $existingContribution->save();

                $correction->accepted = 1;
                $correction->transaction_date = now()->format('Y-m-d');
                $correction->approved_by = Auth::user()->name;
                $correction->save();

                $payload = [[
                    "aRbatchId" => 'ARB009',
                    'customer' => $correction->membership->regimental_number . '-' . ($correction->membership->enumber ?? '000000'),
                    "amount" => $correction->amount ?? 0,
//                    "debit" => $correction->type === 'Addition' ? $correction->amount : 0,
                    "transactionDate" => now()->toIso8601String(),
                    "description" => $correction->type,
                    "reference" => 'Adjustment with ' . $correction->amount,
                    "comments" => 'Adjustment Entry',
                    "transactioncCodeID" => $correction->type === 'Addition' ? 'Adjustmet Entry' : 'Adjustment Entry-',
                    "taxTypeID" => 1,
                    "gl" => false,
                    "ar" => true,
                ]];

                $apiSuccess = $this->sendARBatchUpdate($payload);

                ContributionAssign::create([
                    'additional_id' => $correction->id,
                    'fwd_by' => Auth::user()->id,
                    'fwd_to' => '',
                    'fwd_by_reason' => 'Approved',
                    'fwd_to_reason' => '',
                ]);

                return redirect()->route('corrections')
                    ->with('success', 'Contribution approved successfully');
            } else {
                return redirect()->back()->with('error', 'Invalid Opening Balance');
            }

        } elseif ($action === 'reject') {
            $correction->accepted = 2;
            $correction->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['additional_id'] = $correction->id;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Rejected';

            ContributionAssign::create($validatedAssign);

            return redirect()->route('corrections')
                ->with('success', 'Contribution Rejected');

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }

    }
    public function uploadView()
    {
        $regiments = Regiment::all();
        $recently = RecentContribution::with('regiments','category')
            ->get();

        return view('monthlyDeductions.upload', compact('regiments','recently'));
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
    private function createMember($memberData)
    {
        if (!$this->apiToken) {
            if (!$this->authenticateApi()) {
                return ['status' => false, 'message' => 'Authentication failed.'];
            }
        }
        $payload = [];
        foreach ($memberData as $item) {
            $payload[] = [
                'code' => (string) $item['regimental_number'] . '-' . (string) $item['e_no'],
                'customerName' => (string) $item['name'],
                'officerRank' => (string) $item['type'],
                'rank' => str_replace(' ', '', (string) $item['rank']),
                'regiment' => (string) $item['regiment'],
                'status' => 'Active',
                'bank' => 1,
                'bankCode' => '',
                'branchCode' => '',
                'accountNumber' => '',
                'accountHolder' => (string) $item['name'],
                'accountType' => 1,
                'idNumber' => '',
                'email' => '',
                'contactNo1' => '',
                'contactNo2' => '',
            ];
        }
        $now = now();
        $filename = 'ar_batch_update_' . $now->format('Ymd_His') . '_' . uniqid() . '.log';
        $logPath = storage_path('logs/adjustments/' . $filename);

        // Ensure directory exists
        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($this->apiBaseUrl . '/api/Transaction/CustomerCreate',$payload);

            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payload[0]['customer'] ?? 'N/A',
                'status_code' => $response->status(),
                'success' => $response->successful(),
                'response_body' => json_decode($response->body(), true) ?? $response->body(),
            ];

            file_put_contents($logPath, print_r($logData, true));
            if ($response->successful()) {
                $responseData = $response->json();
                return [
                    'status' => true,
                    'message' => $responseData['message'] ?? 'Created',
                    'code' => $responseData['code'] ?? null,
                    'name' => $responseData['name'] ?? null,
                ];
            }

            return ['status' => false, 'message' => 'Failed to create member'];
        } catch (\Exception $e) {
            $logData = [
                'timestamp' => $now->toDateTimeString(),
                'endpoint' => '/api/Transaction/ARBatchUpdate',
                'payload' => $payload[0]['customer'] ?? 'N/A',
                'success' => false,
                'exception' => $e->getMessage(),
            ];

            file_put_contents($logPath, print_r($logData, true));
            return ['status' => false, 'message' => $e->getMessage()];

        }
    }
//    public function upload(Request $request)
//    {
//        $request->validate([
//            'xml_file' => 'required|file|mimes:xml',
//            'deposit_year' => 'required|integer',
//            'deposit_month' => 'required|integer',
//        ]);
//
//        if (!$this->authenticateApi()) {
//            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
//        }
//
//        $reqCategory = (string) ($request->input('type') ?? $request->input('regiment_id'));
//
//        $filePath = $request->file('xml_file')->getRealPath();
//        $reader = new XMLReader();
//        $reader->open($filePath);
//
//        $batch = [];
//        $seenENos = [];
//
//        while ($reader->read()) {
//            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'G_REGIMENT') {
//                $node = simplexml_load_string($reader->readOuterXML());
//
//                $fieldKey = property_exists($node, 'REGTLNO') ? 'REGTLNO' :
//                    (property_exists($node, 'OFFRSNO') ? 'OFFRSNO' : null);
//
//                if (!$fieldKey || !isset($node->E_NO)) {
//                    continue; // skip if required data is missing
//                }
//
//                $eNo = (string) $node->E_NO;
//
//                // Skip duplicates
//                if (in_array($eNo, $seenENos)) {
//                    continue;
//                }
//                $seenENos[] = $eNo;
//
//                $category = ($fieldKey === 'REGTLNO') ? 'ORs' : 'Offr';
//
//                $batch[] = [
//                    'regimental_number' => (string) $node->$fieldKey,
//                    'e_no' => $eNo,
//                    'emp_no' => (string) $node->EMP_NO,
//                    'regiment' => (string) $node->REGIMENT,
//                    'unit' => (string) $node->UNIT,
//                    'category' => $reqCategory,
//                    'type' => $category,
//                    'rank' => (string) $node->RANK,
//                    'name' => (string) $node->NAME,
//                ];
//            }
//        }
//
//        $reader->close();
//
//        // Optional: dd($batch);
//        $this->createMember($batch);
//
//        return back();
//    }

    public function upload(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
            'deposit_year' => 'required|integer',
            'deposit_month' => 'required|integer',
        ]);

        // Authenticate with external API
        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }

        $reqCategory = (string) ($request->input('type') ?? $request->input('regiment_id'));
        $depositYear = (int) $request->input('deposit_year');
        $depositMonth = (int) $request->input('deposit_month');

        $filePath = $request->file('xml_file')->getRealPath();
        $reader = new XMLReader();
        $reader->open($filePath);

        $batch = [];
        $seenENos = [];
        $totalInserted = 0;
        $totalUpdated = 0;
        $totalFailed = 0;
        $successMessages = [];
        $category = null;
        $categoryId = null;

        while ($reader->read()) {
            if ($reader->nodeType === XMLReader::ELEMENT && $reader->localName === 'G_REGIMENT') {
                $node = simplexml_load_string($reader->readOuterXML());

                $fieldKey = property_exists($node, 'REGTLNO') ? 'REGTLNO' : (property_exists($node, 'OFFRSNO') ? 'OFFRSNO' : null);
                $eNo = (string) $node->E_NO;

                // Skip duplicate E_NO
                if (in_array($eNo, $seenENos)) {
                    continue;
                }
                $seenENos[] = $eNo;

                if ($fieldKey === 'REGTLNO') {
                    $category = 'ORs';
                    $categoryId = 2;
                } elseif ($fieldKey === 'OFFRSNO') {
                    $category = 'Offr';
                    $categoryId = 1;
                }

                $batch[] = [
                    'regimental_number' => (string) $node->$fieldKey,
                    'e_no' => $eNo,
                    'emp_no' => (string) $node->EMP_NO,
                    'regiment' => (string) $node->REGIMENT,
                    'unit' => (string) $node->UNIT,
                    'category' => $reqCategory,
                    'type' => $category,
                    'rank' => (string) $node->RANK,
                    'name' => (string) $node->NAME,
                    'amount' => (float) $node->AMOUNT,
                    'year' => $depositYear,
                    'month' => $depositMonth,
                ];

            }
        }

        $reader->close();

        if (!empty($batch)) {
            $result = $this->processBatch($batch, $depositYear, $depositMonth, $reqCategory);
            $totalInserted += $result['inserted'];
            $totalUpdated += $result['updated'];
            $totalFailed += $result['failed'];
        }

        $failures = ContributionFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->whereRaw('LOWER(category) = ?', [strtolower($reqCategory)])
            ->get();

        $summary = [
            'inserted' => $totalInserted,
            'updated' => $totalUpdated,
            'failed' => $totalFailed,
        ];

        $fullCount = $totalInserted + $totalUpdated + $totalFailed;
        $successCount = $totalInserted + $totalUpdated;

        RecentContribution::create([
            'regiment' => $reqCategory,
            'category_id' => $categoryId,
            'year' => $depositYear,
            'month' => $depositMonth,
            'pnr_count' => $fullCount,
            'success_count' => $successCount,
        ]);

        return view('monthlyDeductions.upload-report', compact(
            'failures',
            'depositYear',
            'depositMonth',
            'reqCategory',
            'summary',
            'successMessages'
        ));
    }
    private function processBatch(array $records, int $depositYear, int $depositMonth, string $category)
    {
        $eNos = collect($records)
            ->map(fn($r) => $r['e_no'])
            ->unique();

        // Fetch existing members from DB keyed by e_no
        $existingMembers = Membership::whereIn('enumber', $eNos)
            ->select('id', 'enumber', 'name', 'regimental_number')
            ->get()
            ->keyBy('enumber');

        // Fetch existing contributions keyed by membership_id
        $contributions = Contribution::whereIn('membership_id', $existingMembers->pluck('id'))
            ->where('year', $depositYear)
            ->where('month', $depositMonth)
            ->get()
            ->keyBy('membership_id');

        $failureRecords = [];
//        $apiFailures = [];

        // 1. Collect new members that do NOT exist locally
        $newMembersToCreate = [];
        $newMembersMap = []; // for quick lookup after creation

        foreach ($records as $r) {
            if (!isset($existingMembers[$r['e_no']])) {
                $newMembersToCreate[] = $r;
            }
        }

        // 2. Bulk create new members remotely via API
        if (!empty($newMembersToCreate)) {
            $apiResult = $this->createMember($newMembersToCreate);
            if (!$apiResult['status']) {
                // Bulk API create failed: log all as failures
                foreach ($newMembersToCreate as $r) {
                    $failureRecords[] = [
                        'enumber' => $r['e_no'],
                        'regimental_number' => $r['regimental_number'],
                        'rank' => $r['rank'],
                        'name' => $r['name'],
                        'category' => $category,
                        'unit' => $r['unit'],
                        'amount' => $r['amount'],
                        'reason' => 'API bulk member creation failed: ' . $apiResult['message'],
                        'year' => $depositYear,
                        'month' => $depositMonth,
                    ];
                }

                // Return early because members creation failed
                return [
                    'inserted' => 0,
                    'updated' => 0,
                    'failed' => count($newMembersToCreate),
                ];
            }

            // 3. Bulk create members locally
            foreach ($newMembersToCreate as $r) {
                try {
                    $newMember = Membership::create([
                        'enumber' => $r['e_no'],
                        'name' => $r['name'] ?? '',
                        'regimental_number' => $r['regimental_number'] ?? '',
                        'dateabfenlisted' => now(),
                        'comment' => $r['emp_no'] ?? '',
                        'contribution_amount' => $r['amount'] ?? 0,
                        'member_status_id' => 2,
                        'acceptedl1' => 0,
                        'acceptedl2' => 0,
                        'rejectedl2' => 0,
                        'altering' => 0,
                        'last_modified_date' => now(),
                    ]);

                    $existingMembers[$r['e_no']] = $newMember;
                    $newMembersMap[$r['e_no']] = $newMember;

                } catch (\Exception $e) {
                    $failureRecords[] = [
                        'enumber' => $r['e_no'],
                        'regimental_number' => $r['regimental_number'],
                        'rank' => $r['rank'],
                        'name' => $r['name'],
                        'category' => $category,
                        'unit' => $r['unit'],
                        'amount' => $r['amount'],
                        'reason' => 'Local member creation failed: ' . $e->getMessage(),
                        'year' => $depositYear,
                        'month' => $depositMonth,
                    ];
                }
            }
        }

        $toInsert = [];
        $toUpdate = [];
        $cashbookPayload = [];

        // 4. Process contributions for all members (existing + new)
        foreach ($records as $r) {
            $empNum = $r['e_no'];
            $member = $existingMembers[$empNum] ?? null;

            if (!$member) {
                // This means member creation failed previously, skip contribution for this member
                continue;
            }

            $memberId = $member->id;
            $amount = $r['amount'];

            if (isset($contributions[$memberId])) {
                $contribution = $contributions[$memberId];

                if ($contribution->manual == 1) {
                    $contribution->amount += $amount;
                    $contribution->transaction_date = now();
                    $toUpdate[] = $contribution;

                    // Prepare API payload for update
                    $cashbookPayload[] = [
                        'amount' => $contribution->amount,
                        'eNumber' => $member->regimental_number . '-' . $member->enumber,
                        'name' => $member->name,
                    ];
                } else {
                    // Contribution exists and manual != 1, log failure
                    $failureRecords[] = [
                        'enumber' => $empNum,
                        'regimental_number' => $r['regimental_number'],
                        'rank' => $r['rank'],
                        'name' => $r['name'],
                        'category' => $category,
                        'unit' => $r['unit'],
                        'amount' => $amount,
                        'reason' => 'Contribution already exists.',
                        'year' => $depositYear,
                        'month' => $depositMonth,
                    ];
                }
            } else {
                // No contribution found, prepare insert
                $toInsert[] = [
                    'version' => 0,
                    'amount' => $amount,
                    'membership_id' => $memberId,
                    'year' => $depositYear,
                    'month' => $depositMonth,
                    'manual' => 0,
                    'currentuser' => Auth::user()->id,
                    'created_system' => 'AFMS',
                    'transaction_date' => now(),
                ];

                $cashbookPayload[] = [
                    'cashbookId' => 'CB077',
                    'credit' => $amount,
                    'debit' => 0,
                    'transactionDate' => now()->toIso8601String(),
                    'customer' => $member->regimental_number . '-' . $member->enumber,
                    'description' => 'Monthly Contribution',
                    'reference' => $member->name,
                    'comments' => $member->name,
                    'gl' => false,
                    'ar' => true,
                    // 'transactioncCodeID' => 1,
                    // 'taxTypeID' => 1,
                ];
            }
        }
//        dd($toInsert);
//         5. Bulk insert new contributions
        if (!empty($toInsert)) {
            Contribution::insert($toInsert);
        }

        // 6. Bulk update manual contributions
        foreach ($toUpdate as $contribution) {
            $contribution->save();
        }

        // 7. Send bulk cashbook update via API
        $this->sendCashBookUpdate($cashbookPayload);
//        if (!empty($cashbookPayload)) {
//            $cashbookResponse = $this->sendCashBookUpdate($cashbookPayload);

//            if (!$cashbookResponse['success']) {
//
//                foreach ($cashbookPayload as $item) {
//                    $apiFailures[] = [
//                        'enumber' => explode('-', $item['customer'])[1] ?? '',
//                        'regimental_number' => explode('-', $item['customer'])[0] ?? '',
//                        'name' => $item['reference'],
//                        'amount' => $item['credit'],
//                        'reason' => 'Cashbook API bulk failed. ' . $cashbookResponse['message'],
//                        'year' => $depositYear,
//                        'month' => $depositMonth,
//                        'category' => $category,
//                        'unit' => '',
//                        'rank' => '',
//                    ];
//                }
//            }
//        }

//         8. Log all failures to DB
        if (!empty($failureRecords)) {
            foreach (array_chunk($failureRecords, 500) as $chunk) {
                ContributionFailures::insert($chunk);
            }
        }
//        if (!empty($apiFailures)) {
//            foreach (array_chunk($apiFailures, 500) as $chunk) {
//                ContributionFailures::insert($chunk);
//            }
//        }

        return [
            'inserted' => count($toInsert),
            'updated' => count($toUpdate),
            'failed' => count($failureRecords),
        ];
    }
    private function sendCashBookUpdate(array $payload)
    {
        try {
            $response = Http::timeout(300)
                ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl . '/api/Transaction/CashBookUpdate', $payload);

            $logContent = [
                'timestamp' => now()->toDateTimeString(),
                'success' => $response->successful(),
                'status' => $response->status(),
                'request' => $payload[0]['customer'] ?? 'N/A',
                'response' => $response->body(),
            ];

            $filename = 'deduction_cashbook_response_' . now()->format('Ymd_His') . '_' . uniqid() . '.log';
            $logPath = storage_path('logs/cashbook/' . $filename);

            // Ensure the directory exists
            if (!file_exists(dirname($logPath))) {
                mkdir(dirname($logPath), 0755, true);
            }

            file_put_contents($logPath, print_r($logContent, true));
//            file_put_contents($logPath, json_encode($logContent, JSON_PRETTY_PRINT));
            return [
                'success' => $response->successful(),
                'message' => $response->body(),
            ];
        } catch (\Exception $e) {
            $logContent = [
                'timestamp' => now()->toDateTimeString(),
                'success' => false,
                'error' => $e->getMessage(),
                'request' => $payload[0]['customer'] ?? 'N/A',
            ];

            $filename = 'deduction_cashbook_error_' . now()->format('Ymd_His') . '_' . uniqid() . '.log';
            $logPath = storage_path('logs/cashbook/' . $filename);

            if (!file_exists(dirname($logPath))) {
                mkdir(dirname($logPath), 0755, true);
            }

            file_put_contents($logPath, print_r($logContent, true));
//            file_put_contents($logPath, json_encode($logContent, JSON_PRETTY_PRINT));
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

//    private function sendCashBookUpdate(array $payload)
//    {
//        try {
//            $response = Http::withHeaders([
//                'Authorization' => 'Bearer ' . $this->apiToken,
//                'Accept' => 'application/json',
//                'Content-Type' => 'application/json',
//            ])->post($this->apiBaseUrl . '/api/Transaction/CashBookUpdate', $payload);
//
//            $responseBody = $response->json();
//
//            $failedItems = [];
//
//            if (is_array($responseBody)) {
//                foreach ($responseBody as $index => $item) {
//                    if (isset($item['status']) && strtolower($item['status']) === 'error') {
//                        $failedItems[] = [
//                            'index' => $index,
//                            'reference' => $item['reference'] ?? '',
//                            'message' => $item['message'] ?? 'Unknown error',
//                            'customer' => $item['customer'] ?? '',
//                        ];
//                    }
//                }
//            }
//
//            return [
//                'success' => empty($failedItems),
//                'message' => $failedItems,
//            ];
//
//        } catch (\Exception $e) {
//            return [
//                'success' => false,
//                'message' => [['message' => $e->getMessage()]],
//            ];
//        }
//    }
    public function downloadCSV($depositYear, $depositMonth, $reqCategory)
    {
        $failures = ContributionFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->where('category', $reqCategory)
            ->get();

        if ($failures->isEmpty()) {
            return redirect()->route('contribution-upload')
                ->with('success', 'No failure data found for download.');
        }

        $filename = "failures_{$depositYear}_{$depositMonth}_{$reqCategory}_" . time() . ".csv";
        $filePath = storage_path("app/{$filename}");

        $file = fopen($filePath, 'w');
        fputcsv($file, ['eNo', 'unit', 'regimentalNo', 'rank', 'name', 'amount', 'error']);

        foreach ($failures as $failure) {
            fputcsv($file, [
                $failure->enumber,
                $failure->unit,
                $failure->regimental_number,
                $failure->rank,
                $failure->name,
                $failure->amount,
                $failure->reason,
            ]);
        }

        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
    public function repaymentView()
    {
        $regiments = Regiment::all();
        $recently = RecentRepayment::with('regiments','category')
            ->get();
        return view('monthlyDeductions.repayment', compact('regiments', 'recently'));
    }
    public function uploadRepayment(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
            'deposit_year' => 'required|integer',
            'deposit_month' => 'required|integer',
        ]);

        $reqCategory = $request->input('regiment_id') ?? $request->input('type');
        $depositYear = $request->input('deposit_year');
        $depositMonth = $request->input('deposit_month');

        $filePath = $request->file('xml_file')->getRealPath();
        $reader = new \XMLReader();
        $reader->open($filePath);

        $batch = [];
        $totalInserted = 0;
        $totalUpdated = 0;
        $totalFailed = 0;
        $category = null;

        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }

        while ($reader->read()) {
            if ($reader->nodeType == XMLReader::ELEMENT && $reader->localName == 'G_REGIMENT') {
                $node = simplexml_load_string($reader->readOuterXML());

                $fieldKey = property_exists($node, 'REGTLNO') ? 'REGTLNO' :
                    (property_exists($node, 'OFFRSNO') ? 'OFFRSNO' : null);

                $batch[] = [
                    'regimental_number' => (string) $node->$fieldKey,
                    'e_no' => (int) $node->E_NO,
                    'amount' => (float) $node->AMOUNT,
                    'rank' => (string) $node->RANK,
                    'name' => (string) $node->NAME,
                    'unit' => (string) $node->UNIT,
                    'category' => $reqCategory,
                ];

                if ($fieldKey == 'REGTLNO') {
                    $category = 2;
                } elseif ($fieldKey == 'OFFRSNO') {
                    $category = 1;
                }

                if (count($batch) >= 1000) {
                    $result = $this->processRepaymentBatch($batch, $depositYear, $depositMonth);
                    $totalInserted += $result['inserted'];
                    $totalUpdated += $result['updated'];
                    $totalFailed += $result['failed'];
                    $batch = [];
                }
            }
        }

        $reader->close();

        if (!empty($batch)) {
            $result = $this->processRepaymentBatch($batch, $depositYear, $depositMonth);
            $totalInserted += $result['inserted'];
            $totalUpdated += $result['updated'];
            $totalFailed += $result['failed'];
        }

        $failures = RepaymentFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->whereRaw('LOWER(category) = ?', [strtolower($reqCategory)])
            ->get();

        $summary = [
            'inserted' => $totalInserted,
            'updated' => $totalUpdated,
            'failed' => $totalFailed,
        ];

        $fullCount = $totalInserted + $totalUpdated + $totalFailed;
        $successCount = $totalInserted + $totalUpdated;

        RecentRepayment::create([
            'regiment_id' => $reqCategory,
            'category_id' => $category,
            'year' => $depositYear,
            'month' => $depositMonth,
            'pnr_count' => $fullCount,
            'success_count' => $successCount,
        ]);

        return view('monthlyDeductions.repayment-report', compact('failures', 'depositYear', 'depositMonth', 'reqCategory', 'summary'));
    }
//    private function processRepaymentBatch(array $records, int $year, int $month): array
//    {
//        $now = now();
//
//        $keys = collect($records)->map(fn($r) => $r['e_no'] . '|' . $r['regimental_number'])->unique();
//
//        $applications = LoanApplication::with(['membership', 'loan'])
//            ->whereHas('membership', fn($q) => $q->whereRaw("CONCAT(enumber, '|', regimental_number) IN ('" . $keys->implode("','") . "')"))
//            ->whereHas('loan', fn($q) => $q->where('settled', 0))
//            ->get()
//            ->keyBy(fn($a) => $a->membership->enumber . '|' . $a->membership->regimental_number);
//
//        $repaymentMap = LoanRecoveryPayment::where('year', $year)
//            ->where('month', $month)
//            ->whereIn('loan_id', $applications->pluck('application_reg_no'))
//            ->get()
//            ->keyBy('loan_id');
//
//        $inserted = 0;
//        $updated = 0;
//        $failures = [];
//
//        foreach ($records as $r) {
//            $key = $r['e_no'] . '|' . $r['regimental_number'];
//            $app = $applications->get($key);
//
//            if (!$app) {
//                $failures[] = [
//                    'enumber' => $r['e_no'],
//                    'regimental_number' => $r['regimental_number'],
//                    'rank' => $r['rank'],
//                    'name' => $r['name'],
//                    'unit' => $r['unit'],
//                    'amount' => $r['amount'],
//                    'reason' => 'Loan record not found or settled',
//                    'year' => $year,
//                    'month' => $month,
//                    'category' => $r['category'],
//                    'created_at' => $now,
//                    'updated_at' => $now,
//                ];
//                continue;
//            }
//
//            $repayment = $repaymentMap->get($app->application_reg_no);
//            if (!$repayment) {
//                $failures[] = [
//                    'enumber' => $r['e_no'],
//                    'regimental_number' => $r['regimental_number'],
//                    'rank' => $r['rank'],
//                    'name' => $r['name'],
//                    'unit' => $r['unit'],
//                    'amount' => $r['amount'],
//                    'reason' => 'No repayment record found',
//                    'year' => $year,
//                    'month' => $month,
//                    'category' => $r['category'],
//                    'created_at' => $now,
//                    'updated_at' => $now,
//                ];
//                continue;
//            }
//
//            $interest = $repayment->interest_due;
//            $capital = $r['amount'] - $interest;
//
//            $app->last_pay_date = $now->format('Y-m-d');
//            $app->arrest_dates = 0;
//            $app->arrest_interest = 0;
//            $app->save();
//
//            $loan = $app->loan;
//            $loan->no_of_installments_paid += 1;
//            $loan->total_recovered_capital += $capital;
//            $loan->total_recovered_interest += $interest;
//            $loan->currentuser = Auth::user()->name;
//            $loan->created_system = 'AFMS';
//            if ($loan->total_capital <= $loan->total_recovered_capital) {
//                $loan->settled = 1;
//            }
//            $loan->save();
//
//            $repayment->capital_received = $capital;
//            $repayment->interest_received = $interest;
//            $repayment->loan_balance = $loan->total_capital - $loan->total_recovered_capital;
//            $repayment->payment_date = $now->format('Y-m-d');
//            $repayment->save();
//
//            $updated++;
//        }
//
//        if (!empty($failures)) {
//            RepaymentFailures::insert($failures);
//        }
//
//        return [
//            'inserted' => $inserted, // You can track this if you insert new repayments
//            'updated' => $updated,
//            'failed' => count($failures),
//        ];
//    }
    private function processRepaymentBatch(array $records, int $year, int $month): array
    {
        $now = now();

        $keys = collect($records)->map(fn($r) => $r['e_no'] . '|' . $r['regimental_number'])->unique();

        $applications = LoanApplication::select('id', 'application_reg_no', 'approved_amount', 'member_id',
            'monthly_capital_portion', 'no_of_installments', 'arrest_dates', 'arrest_interest', 'next_installement',
            'next_interest', 'last_pay_date')
            ->with(['membership:id,enumber,regimental_number,name', 'loan'])
            ->whereHas('membership', fn($q) => $q->whereRaw("CONCAT(enumber, '|', regimental_number) IN ('" . $keys->implode("','") . "')"))
            ->whereHas('loan', fn($q) => $q->where('settled', 0))
            ->get()
            ->keyBy(fn($a) => $a->membership->enumber . '|' . $a->membership->regimental_number);

        $repaymentMap = LoanRecoveryPayment::where('year', $year)
            ->where('month', $month)
            ->whereIn('loan_id', $applications->pluck('application_reg_no'))
            ->get()
            ->keyBy('loan_id');

        $inserted = 0;
        $updated = 0;
        $failures = [];
        $cashbookPayload = [];

        foreach ($records as $r) {
            $key = $r['e_no'] . '|' . $r['regimental_number'];
            $app = $applications->get($key);

            if (!$app) {
                $failures[] = [
                    'enumber' => $r['e_no'],
                    'regimental_number' => $r['regimental_number'],
                    'rank' => $r['rank'],
                    'name' => $r['name'],
                    'unit' => $r['unit'],
                    'amount' => $r['amount'],
                    'reason' => 'Loan record not found or settled',
                    'year' => $year,
                    'month' => $month,
                    'category' => $r['category'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                continue;
            }

            $repayment = $repaymentMap->get($app->application_reg_no);

            if (!$repayment) {
                $failures[] = [
                    'enumber' => $r['e_no'],
                    'regimental_number' => $r['regimental_number'],
                    'rank' => $r['rank'],
                    'name' => $r['name'],
                    'unit' => $r['unit'],
                    'amount' => $r['amount'],
                    'reason' => 'No repayment record found',
                    'year' => $year,
                    'month' => $month,
                    'category' => $r['category'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                continue;
            }

            $interest = $repayment->interest_due;
            $capital = $r['amount'] - $interest;

            // Update LoanApplication
            $app->last_pay_date = $now->format('Y-m-d');
            $app->arrest_dates = 0;
            $app->arrest_interest = 0;
            $app->save();

            // Update Loan
            $loan = $app->loan;
            $loan->no_of_installments_paid += 1;
            $loan->total_recovered_capital += $capital;
            $loan->total_recovered_interest += $interest;
            $loan->currentuser = Auth::user()->name;
            $loan->created_system = 'AFMS';
            if ($loan->total_capital <= $loan->total_recovered_capital) {
                $loan->settled = 1;
            }
            $loan->save();

            // Update Repayment
            $repayment->capital_received = $capital;
            $repayment->interest_received = $interest;
            $repayment->loan_balance = $loan->total_capital - $loan->total_recovered_capital;
            $repayment->payment_date = $now->format('Y-m-d');
            $repayment->save();

            // Prepare cashbook payload
            $customer = $r['regimental_number'] . '-' . $r['e_no'] ?? '000000000';
            $name = $r['name'] ?? '';

            $cashbookPayload[] = [
                "cashbookId" => 'CB073',
                "credit" => 0,
                "debit" => $capital,
                "transactionDate" => $now->toIso8601String(),
                "customer" => $customer,
                "description" => $name,
                "reference" => 'Recovery',
                "comments" => 'Recovery',
                "gl" => false,
                "ar" => true,
            ];

            $cashbookPayload[] = [
                "cashbookId" => 'CB079',
                "credit" => 0,
                "debit" => $interest,
                "transactionDate" => $now->toIso8601String(),
                "customer" => $customer,
                "description" => $name,
                "reference" => 'Interest',
                "comments" => 'Interest',
                "gl" => false,
                "ar" => true,
            ];

            $updated++;
        }

        // Send to CashBook API
        $cashbookResponse = $this->sendCashBookUpdate($cashbookPayload);

//        if (!empty($cashbookPayload)) {
//            $cashbookResponse = $this->sendCashBookUpdate($cashbookPayload);
//
//            if (!$cashbookResponse['success']) {
//                foreach ($cashbookPayload as $entry) {
//                    $failures[] = [
//                        'enumber' => explode('-', $entry['customer'])[1] ?? '',
//                        'regimental_number' => explode('-', $entry['customer'])[0] ?? '',
//                        'rank' => '',
//                        'name' => $entry['description'],
//                        'unit' => '',
//                        'amount' => $entry['debit'],
//                        'reason' => 'Cashbook API failed: ' . $cashbookResponse['message'],
//                        'year' => $year,
//                        'month' => $month,
//                        'category' => 'Repayment',
//                        'created_at' => $now,
//                        'updated_at' => $now,
//                    ];
//                }
//            }
//        }

        if (!empty($failures)) {
            foreach (array_chunk($failures, 500) as $chunk) {
                RepaymentFailures::insert($chunk);
            }
        }

        return [
            'inserted' => $inserted, // not tracked in current logic
            'updated' => $updated,
            'failed' => count($failures),
        ];
    }

    public function repaymentCSV($depositYear, $depositMonth, $reqCategory)
    {
        $failures = RepaymentFailures::where('year', $depositYear)
            ->where('month', $depositMonth)
            ->where('category', $reqCategory)
            ->get();

        if ($failures->isEmpty()) {
            return redirect()->route('repayment-upload')
                ->with('success', 'No failure data found for download.');
        }

        $filename = "Repayment_failures_{$depositYear}_{$depositMonth}_{$reqCategory}_" . time() . ".csv";
        $filePath = storage_path("app/{$filename}");

        $file = fopen($filePath, 'w');
        fputcsv($file, ['eNo', 'unit', 'regimentalNo', 'rank', 'name', 'amount', 'error']);

        foreach ($failures as $failure) {
            fputcsv($file, [
                $failure->enumber,
                $failure->unit,
                $failure->regimental_number,
                $failure->rank,
                $failure->name,
                $failure->amount,
                $failure->reason,
            ]);
        }

        fclose($file);

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
//    public function repaymentCSV()
//    {
//        $failures = session('failures');
//        if (empty($failures)) {
//            return redirect()->route('repayment-upload')
//                ->with('success', 'No failures data recorded');
//        }
//
//        $filename = 'failures_' . time() . '.csv';
//
//        $file = fopen(storage_path('app/' . $filename), 'w');
//
//        $headers = ['loanId', 'unit', 'regimentalNo', 'rank', 'name', 'capital', 'interest', 'error'];
//        fputcsv($file, $headers);
//
//        foreach ($failures as $failure) {
//            fputcsv($file, $failure);
//        }
//
//        fclose($file);
//
//        $headers = array(
//            "Content-Type" => "text/csv",
//        );
//
//        return response()->download(storage_path('app/' . $filename), $filename, $headers);
//    }
    public function repaymentCreate()
    {
        $recently = RepaymentBatch::latest('created_at')->take(5)->get();
        return view('monthlyDeductions.repayment-create', compact('recently'));
    }
    public function repaymentBatch(Request $request)
    {
        $recently = RepaymentBatch::where('year', $request->year)->where('month',$request->month)->get();
        $request->validate([
            'processing' => 'required',
        ]);

        if ($recently->count()==0){

            $loans = LoanApplication::with(['loan', 'product' => function($query) {
                $query->select('id', 'interest_rate');
            }])
                ->select('application_reg_no', 'member_id', 'arrest_interest', 'product_id')
                ->whereHas('loan', function ($query) {
                    $query->where('settled', '!=', 1);
                })->get();
            $withdrawals = PartialWithdrawalApplication::where('processing','!=',2)->where('processing','>=',$request->processing)->get();
            $fullWithdrawals = FullWithdrawalApplication::where('processing','!=',2)->where('processing','>=',$request->processing)->get();

            $exPartials = PartialWithdrawalApplication::whereNotIn('processing', [0, 2])->where('processing','<',$request->processing)->get();
            $exFull = FullWithdrawalApplication::whereNotIn('processing', [0, 2])->where('processing','<',$request->processing)->get();

            if ($exPartials->count()>0 | $exFull->count()>0){
                return redirect()->back()->with('error', 'Partial or Full Withdrawal Applications processing queue is not empty! ');

            } else {
                foreach ($loans as $loan) {
                    if ($withdrawals->where('member_id', $loan->member_id)->count() == 0 || $fullWithdrawals->where('member_id', $loan->member_id)->count() == 0){

                        $interest = round(($loan->loan->total_capital - $loan->loan->total_recovered_capital) * $loan->product->interest_rate / 1200, 2);

                        LoanRecoveryPayment::create([
                            'loan_id' => $loan->application_reg_no,
                            'payment_no' => $loan->loan->no_of_installments_paid+1,
                            'capital_due' => $loan->loan->current_monthly_capital_portion,
                            'interest_due' => $interest + $loan->arrest_interest,
                            'year' => $request->year,
                            'month' => $request->month,
                            'is_manual_update' => 0,
                            'yearly_interest_rate_id' => $loan->product_id,
                            'is_new_raw_written' => 1,
                            'version' => 0,
                            'currentuser' => Auth::user()->name,
                            'created_system' => 'AFMS',
                        ]);
                    }
                }
                RepaymentBatch::create([
                    'year' => $request->year,
                    'month' => $request->month,
                ]);

                return redirect()->back()->with('success', 'Batch created successfully');
            }
        } else {
            return redirect()->back()->with('error', 'Sorry, batch already created');
        }

    }
    public function partialsCSV(Request $request)
    {
        $withdrawals = PartialWithdrawalApplication::whereNotIn('processing', [0, 2])->where('processing','<',$request->processing)->get();

        $filename = "PartialWithdrawals.csv";
        $csvData = "Application No,Reg No,Name,Registered Date\n";

        foreach ($withdrawals as $withdrawal) {
            $csvData .= ($withdrawal->application_reg_no ?? 'NA') . ',';
            $csvData .= ($withdrawal->membership->regimental_number ?? 'NA') . ',';
            $csvData .= ($withdrawal->membership->ranks->rank_name ?? '-') . ' ' . ($withdrawal->membership->name ?? '-') . ',';
            $csvData .= ($withdrawal->registered_date ?? '-'). "\n";
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('repayment-create')]);
    }
    public function fullCSV(Request $request)
    {
        $withdrawals = FullWithdrawalApplication::whereNotIn('processing', [0, 2])->where('processing','<',$request->processing)->get();
        $filename = "FullWithdrawals.csv";
        $csvData = "Application No,Reg No,Name,Registered Date\n";

        foreach ($withdrawals as $withdrawal) {
            $csvData .= ($withdrawal->application_reg_no ?? 'NA') . ',';
            $csvData .= ($withdrawal->membership->regimental_number ?? 'NA') . ',';
            $csvData .= ($withdrawal->membership->ranks->rank_name ?? '-') . ' ' . ($withdrawal->membership->name ?? '-') . ',';
            $csvData .= ($withdrawal->registered_date ?? '-'). "\n";
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('repayment-create')]);
    }

    public function edit($id)
    {
        $contributionsAddition = ContributionAdditional::with('membership')->find($id);
        $users = User::all();
        return view('monthlyDeductions.edit',compact('contributionsAddition', 'users'));
    }
    public function correctionsEdit($id)
    {
        $correction = ContributionCorrection::with('membership')->find($id);
        $users = User::all();
        return view('monthlyDeductions.correction-edit',compact('correction', 'users'));
    }
    public function update(Request $request, $contribution_id)
    {
        $validatedData = $request->validate([
            'amount' => 'required|numeric',
            'year' => 'required',
            'month' => 'required',
            'type' => 'required',
            'remark' => 'nullable',
        ]);

        $validatedData['accepted'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;;
        $validatedData['approved_by'] = 'Pending';

        $contributionsAddition = ContributionAdditional::findOrFail($contribution_id);

        $contributionsAddition->update($validatedData);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);
        $validatedAssign['additional_id'] = $contributionsAddition->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Edit contribution';
        $validatedAssign['fwd_to_reason'] = 'For Check & Approval';

        ContributionAssign::create($validatedAssign);

        return redirect()->route('additional-contribution')
            ->with('success', 'Edited Contribution Send for Approval');
    }
    public function correctionsUpdate(Request $request, $id)
    {

        $validatedData = $request->validate([
            'amount' => 'required',
            'type' => 'required',
            'remark' => 'nullable',
        ]);

        $validatedData['amount'] = str_replace(',', '', $validatedData['amount']);
        $validatedData['accepted'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;;
        $validatedData['created_system'] = 'AFMS-update';

        $contributionsAddition = ContributionCorrection::findOrFail($id);

        $contributionsAddition->update($validatedData);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);

        $validatedAssign['correction_id'] = $id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = '';
        $validatedAssign['fwd_to_reason'] = 'For Approval';
        CorrectionAssign::create($validatedAssign);

        return redirect()->route('corrections')
            ->with('success', 'Updated Contribution Correction Send for Approval');
    }

    public function destroy($id)
    {
        ContributionAdditional::find($id)->delete();
        return redirect()->route('additional-contribution')
            ->with('success','Contribution deleted successfully');
    }
    public function correctionDestroy($id)
    {
        ContributionCorrection::find($id)->delete();
        return redirect()->route('corrections')
            ->with('success','Error correction data deleted successfully');
    }
}
