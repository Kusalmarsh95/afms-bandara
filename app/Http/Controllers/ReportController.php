<?php

namespace App\Http\Controllers;

use App\Exports\LoanExport;
use App\Models\Contribution;
use App\Models\ContributionSummary;
use App\Models\DirectSettlment;
use App\Models\FullWithdrawalApplication;
use App\Models\LoanApplication;
use App\Models\LoanRecoveryPayment;
use App\Models\MemberCategory;
use App\Models\Membership;
use App\Models\PartialWithdrawalApplication;
use App\Models\Regiment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function contributionView(Request $request)
    {
        $regiments = Regiment::all();
        $category = $request->input('category_id');
        $regimentId = $request->input('regiment_id');
        $type = $request->input('type');

        $memberships = [];
        if ($category == 2) {
            $memberships = Membership::with('ranks', 'regiments', 'category')
                ->where('member_status_id', '!=','8')
                ->where('category_id', $category)
                ->where('regiment_id', $regimentId)
                ->get();

        } else {
            $memberships = Membership::with('ranks', 'regiments', 'category')
                ->where('member_status_id', '!=','8')
                ->where('category_id', $category)
                ->where('type', $type)
                ->get();
        }

        return view('reports.contributions', compact('memberships', 'regiments', 'category'));
    }
    public function contributionCSV(Request $request)
    {
        $category = $request->input('category_id');
        $regimentId = $request->input('regiment_id');
        $type = $request->input('type');

        if ($category == 2) {
            $memberships = Membership::with('ranks', 'regiments', 'category')
                ->where('member_status_id', '!=','8')
                ->where('category_id', $category)
                ->where('regiment_id', $regimentId)
                ->get();

            $regimentName = $memberships->first()->regiments->regiment_name ?? 'Unknown';
            $filename = "ORs-contribution_$regimentName.csv";

        } else {
            $memberships = Membership::with('ranks', 'regiments', 'category')
                ->where('member_status_id', '!=','8')
                ->where('category_id', $category)
                ->where('type', $type)
                ->get();

            $filename = "OFFICERs-contribution_$type.csv";
        }

        $csvData = "Reg Number,E Number, Rank Type,Name,Regiment,Contribution\n";

        foreach ($memberships as $key => $membership) {
            $csvData .= ($membership->regimental_number ?? 'NA') . ',';
            $csvData .= ($membership->enumber ?? 'NA') . ',';
            $csvData .= ($membership->category->category_name ?? ' NA ') . ',';
            $csvData .= ($membership->ranks->rank_name ?? ' NA ') . ' ' . ($membership->name ?? ' NA') . ',';
            $csvData .= ($membership->regiments->regiment_name ?? '-') . ',';
            $csvData .= ($membership->contribution_amount ?? '0.00') . "\n";
        }

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('member-contribution')]);
    }
    public function outstanding() {
        $loans = LoanApplication::where('processing', '!=', '0')->get();

        $loanCounts = [
            'Registration' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Ledger' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Recovery' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Section' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Audit' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Payment' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Account' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'IT' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Other' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ]
        ];
        $loanAmounts = [
            'Registration' => 0,
            'Ledger' => 0,
            'Loan Recovery' => 0,
            'Loan Section' => 0,
            'Audit' => 0,
            'Payment' => 0,
            'Account' => 0,
            'IT' => 0,
            'Other' => 0
        ];

        foreach ($loans as $loan) {
            $latestAssignment = $loan->assigns()->latest()->first();

            if ($latestAssignment) {
                $userId = $latestAssignment->fwd_to;
                $user = User::find($userId);

                if ($user) {
                    $category = $this->getCategoryFromRole($user->name);
                    $registrationDate = $loan->registered_date;

                    $daysSinceRegistration = now()->diffInDays($registrationDate);

                    if ($daysSinceRegistration >= 0 && $daysSinceRegistration <= 7) {
                        $timeFrame = '1-7 Days';
                    } elseif ($daysSinceRegistration >= 8 && $daysSinceRegistration <= 15) {
                        $timeFrame = '8-15 Days';
                    } elseif ($daysSinceRegistration >= 16 && $daysSinceRegistration <= 30) {
                        $timeFrame = '16-30 Days';
                    } elseif ($daysSinceRegistration >= 31 && $daysSinceRegistration <= 60) {
                        $timeFrame = '1-2 Months';
                    } elseif ($daysSinceRegistration >= 61 && $daysSinceRegistration <= 180) {
                        $timeFrame = '2-6 Months';
                    } else {
                        continue;
                    }

                    if (isset($loanCounts[$category][$timeFrame])) {
                        $loanCounts[$category][$timeFrame]++;
                    }
                    if (!$loan->approved_amount){
                        $amount = $loan->total_amount_requested > 0 ? $loan->total_amount_requested : $loan->suggested_amount;
                    } else {
                        $amount = $loan->approved_amount;
                    }
                    $loanAmounts[$category] += $amount;
                }
            }
        }

        $partialWithdrawals = PartialWithdrawalApplication::with(['withdrawal','assigns'])
            ->where('processing','!=','0')
            ->get();

        $partialCounts = [
            'Registration' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Ledger' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Recovery' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Section' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Audit' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Payment' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Account' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'IT' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Other' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ]
        ];
        $partialAmounts = [
            'Registration' => 0,
            'Ledger' => 0,
            'Loan Recovery' => 0,
            'Loan Section' => 0,
            'Audit' => 0,
            'Payment' => 0,
            'Account' => 0,
            'IT' => 0,
            'Other' => 0
        ];
        foreach ($partialWithdrawals as $partialWithdrawal) {
            $latestAssignment = $partialWithdrawal->assigns()->latest()->first();

            if ($latestAssignment) {
                $userId = $latestAssignment->fwd_to;
                $user = User::find($userId);

                if ($user) {
                    $category = $this->getCategoryFromRole($user->name);
                    $registrationDate = $partialWithdrawal->registered_date;

                    $daysSinceRegistration = now()->diffInDays($registrationDate);

                    if ($daysSinceRegistration >= 0 && $daysSinceRegistration <= 7) {
                        $timeFrame = '1-7 Days';
                    } elseif ($daysSinceRegistration >= 8 && $daysSinceRegistration <= 15) {
                        $timeFrame = '8-15 Days';
                    } elseif ($daysSinceRegistration >= 16 && $daysSinceRegistration <= 30) {
                        $timeFrame = '16-30 Days';
                    } elseif ($daysSinceRegistration >= 31 && $daysSinceRegistration <= 60) {
                        $timeFrame = '1-2 Months';
                    } elseif ($daysSinceRegistration >= 61 && $daysSinceRegistration <= 180) {
                        $timeFrame = '2-6 Months';
                    } else {
                        continue;
                    }

                    if (isset($partialCounts[$category][$timeFrame])) {
                        $partialCounts[$category][$timeFrame]++;
                    }
                    if (!$partialWithdrawal->withdrawal->approved_amount) {
                        $amount = $partialWithdrawal->withdrawal->requested_amount > 0 ? $partialWithdrawal->withdrawal->requested_amount : $partialWithdrawal->withdrawal->eligible_amount;
                    } else {
                        $amount = $partialWithdrawal->withdrawal->approved_amount;
                    }
                    $partialAmounts[$category] += $amount;
                }
            }
        }

        $fullWithdrawals = FullWithdrawalApplication::with(['fullWithdrawal','assigns'])
            ->where('processing','!=','0')
            ->get();

        $fullCounts = [
            'Registration' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Ledger' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Recovery' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Loan Section' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Audit' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Payment' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Account' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'IT' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ],
            'Other' => [
                '1-7 Days' => 0,
                '8-15 Days' => 0,
                '16-30 Days' => 0,
                '1-2 Months' => 0,
                '2-6 Months' => 0
            ]
        ];
        $fullAmounts = [
            'Registration' => 0,
            'Ledger' => 0,
            'Loan Recovery' => 0,
            'Loan Section' => 0,
            'Audit' => 0,
            'Payment' => 0,
            'Account' => 0,
            'IT' => 0,
            'Other' => 0
        ];
        foreach ($fullWithdrawals as $fullWithdrawal) {
            $latestAssignment = $fullWithdrawal->assigns()->latest()->first();

            if ($latestAssignment) {
                $userId = $latestAssignment->fwd_to;
                $user = User::find($userId);

                if ($user) {
                    $category = $this->getCategoryFromRole($user->name);
                    $registrationDate = $fullWithdrawal->registered_date;

                    $daysSinceRegistration = now()->diffInDays($registrationDate);

                    if ($daysSinceRegistration >= 0 && $daysSinceRegistration <= 7) {
                        $timeFrame = '1-7 Days';
                    } elseif ($daysSinceRegistration >= 8 && $daysSinceRegistration <= 15) {
                        $timeFrame = '8-15 Days';
                    } elseif ($daysSinceRegistration >= 16 && $daysSinceRegistration <= 30) {
                        $timeFrame = '16-30 Days';
                    } elseif ($daysSinceRegistration >= 31 && $daysSinceRegistration <= 60) {
                        $timeFrame = '1-2 Months';
                    } elseif ($daysSinceRegistration >= 61 && $daysSinceRegistration <= 180) {
                        $timeFrame = '2-6 Months';
                    } else {
                        continue;
                    }

                    if (isset($fullCounts[$category][$timeFrame])) {
                        $fullCounts[$category][$timeFrame]++;
                    }
                    if (!$fullWithdrawal->fullWithdrawal->approved_amount) {
                        $amount = $fullWithdrawal->fullWithdrawal->eligible_amount;
                    } else {
                        $amount = $fullWithdrawal->fullWithdrawal->approved_amount;
                    }
                    $fullAmounts[$category] += $amount;
                }
            }
        }

        return view('reports.outstanding', compact('loans', 'partialWithdrawals',
            'fullWithdrawals', 'loanCounts', 'loanAmounts', 'partialCounts', 'partialAmounts', 'fullCounts', 'fullAmounts'));
    }
    private function getCategoryFromRole($role) {
        if (strpos($role, 'Registration') !== false) {
            return 'Registration';
        } elseif (strpos($role, 'Ledger') !== false) {
            return 'Ledger';
        } elseif (strpos($role, 'Loan Recovery') !== false) {
            return 'Loan Recovery';
        } elseif (strpos($role, 'Loan Section') !== false) {
            return 'Loan Section';
        } elseif (strpos($role, 'Audit') !== false) {
            return 'Audit';
        } elseif (strpos($role, 'Payment') !== false) {
            return 'Payment';
        } elseif (strpos($role, 'Account') !== false) {
            return 'Account';
        } elseif (strpos($role, 'IT') !== false) {
            return 'IT';
        } else {
            return 'Other';
        }
    }
    public function pdfOutstandingSummary(){
        $data = $this->outstanding()->getData();

        $loanCounts = $data['loanCounts'];
        $loanAmounts = $data['loanAmounts'];
        $partialCounts = $data['partialCounts'];
        $partialAmounts = $data['partialAmounts'];
        $fullCounts = $data['fullCounts'];
        $fullAmounts = $data['fullAmounts'];

        $pdf = PDF::loadView('reports.pdf-outstanding-summary', compact('loanCounts',
            'partialCounts', 'fullCounts', 'loanAmounts', 'partialAmounts', 'fullAmounts'))
            ->setPaper('a4', 'landscape');

        $filename = 'outstanding_report_' . now() . '.pdf';

        return $pdf->download($filename);
    }
    public function pdfOutstandingWeekly(){
        $data = $this->outstanding()->getData();

        $loanCounts = $data['loanCounts'];
        $partialCounts = $data['partialCounts'];
        $fullCounts = $data['fullCounts'];

        $pdf = PDF::loadView('reports.pdf-outstanding-weekly', compact('loanCounts', 'partialCounts', 'fullCounts'))
            ->setPaper('a4', 'landscape');

        $filename = 'outstanding_report_weekly' . now() . '.pdf';

        return $pdf->download($filename);
    }
    public function closingBalanceView(Request $request) {
        $regiments = Regiment::all();

        $contributions = [];
        if ($request->has('year')) {

            $year = $request->input('year');
            $icpId = $request->input('icp_id');
            $regimentId = $request->input('regiment_id');

            $contributions = ContributionSummary::where('year', $year)
                ->where('icp_id', $icpId)
                ->whereHas('membership', function ($query) use ($regimentId) {
                    $query->where('regiment_id', $regimentId)
                        ->where('member_status_id', '!=', 8);
                })
                ->get();
        }

        return view('reports.closing-balance', compact('regiments', 'contributions'));
    }

    public function closingBalanceCSV(Request $request)
    {
        $year = $request->input('year');
        $icpId = $request->input('icp_id');
        $regimentId = $request->input('regiment_id');

        $contributions = ContributionSummary::with(['membership'])
            ->where('year', '=', $year)
            ->where('icp_id', '=', $icpId)
            ->whereHas('membership', function ($query) use ($regimentId){
                $query->where('member_status_id', '!=', 8)
                    ->where('regiment_id', '=', $regimentId);
            })
            ->get();

        $csvData = "Reg Number, Rank Type,Name,Regiment,Balance\n";

        foreach ($contributions as $contribution) {
            $csvData .= ($contribution->membership->regimental_number ?? 'NA') . ',';
            $csvData .= ($contribution->membership->category->category_name?? 'NA') . ',';
            $csvData .= ($contribution->membership->ranks->rank_name  ?? ' NA ') . ' ' . ($contribution->membership->name ?? ' NA ') . ',';
            $csvData .= ($contribution->membership->regiments->regiment_name ?? ' NA ') . ',';
            $csvData .= ($contribution->closing_balance ?? '0.00') . "\n";
        }


        $regimentName = $contributions->first()->membership->regiments->regiment_name ?? 'Unknown';

        $filename = "$year-$icpId-Closing_balance_$regimentName.csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('closing-balance')]);

    }
    public function custOut()
    {
        $loans = LoanApplication::with(['loan', 'membership'])
            ->where('processing','6')
            ->where('is_banked','=','0')
            ->get();

        $csvData = "Bank Code, Branch Code,Account Number,Name,Amount,Regiment,Reg Number\n";

        foreach ($loans as $loan) {
            $csvData .= ($loan->membership->bank_code ?? '') . ',';
            $csvData .= ($loan->membership->branch_code ?? '') . ',';
            $csvData .= '"' . ' ' . str_pad(($loan->membership->account_no ?? ''), 12, '0', STR_PAD_LEFT) . '"' . ',';
            $csvData .= ($loan->membership->name ?? '') . ',';
            $csvData .= ($loan->approved_amount ?? '0.00') . ',';
            $csvData .= ($loan->membership->regiments->regiment_name ?? '') . ',';
            $csvData .= ($loan->membership->regimental_number ?? '') . "\n";
        }
        $date = date('Y-m-d');
        $filename = "Loan_BankCustOut_$date.csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('loan.bulk')]);

    }
    public function withdrawalCustOut()
    {
        $withdrawals = PartialWithdrawalApplication::with(['withdrawal', 'membership'])
            ->where('processing','6')
            ->where('is_banked','=','0')
            ->get();

        $csvData = "Bank Code, Branch Code,Account Number,Name,Amount,Regiment,Reg Number\n";

        foreach ($withdrawals as $withdrawal) {
            $csvData .= ($withdrawal->membership->bank_code ?? '') . ',';
            $csvData .= ($withdrawal->membership->branch_code ?? '') . ',';
            $csvData .= '"' . ' ' . str_pad(($withdrawal->membership->account_no ?? ''), 12, '0', STR_PAD_LEFT) . '"' . ',';
            $csvData .= ($withdrawal->membership->name ?? '') . ',';
            $csvData .= ($withdrawal->withdrawal->total_withdraw_amount ?? '0.00') . ',';
            $csvData .= ($withdrawal->membership->regiments->regiment_name ?? '') . ',';
            $csvData .= ($withdrawal->membership->regimental_number ?? '') . "\n";
        }
        $date = date('Y-m-d');
        $filename = "PartialWithdrawal_BankCustOut_$date.csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('partial.bulk')]);

    }
    public function fullCustOut()
    {
        $withdrawals = FullWithdrawalApplication::with(['fullWithdrawal', 'membership'])
            ->where('processing','6')
            ->where('is_banked','=','0')
            ->get();

        $csvData = "Bank Code, Branch Code,Account Number,Name,Amount,Regiment,Reg Number\n";

        foreach ($withdrawals as $withdrawal) {
            $csvData .= ($withdrawal->membership->bank_code ?? '') . ',';
            $csvData .= ($withdrawal->membership->branch_code ?? '') . ',';
            $csvData .= '"' . str_pad(($withdrawal->membership->account_no ?? ''), 12, '0', STR_PAD_LEFT) . '"' . ',';
            $csvData .= ($withdrawal->membership->name ?? '') . ',';
            $csvData .= ($withdrawal->withdrawal->fullWithdrawal->withdrawal_amount ?? '0.00') . ',';
            $csvData .= ($withdrawal->membership->regiments->regiment_name ?? '') . ',';
            $csvData .= ($withdrawal->membership->regimental_number ?? '') . "\n";
        }
        $date = date('Y-m-d');
        $filename = "PartialWithdrawal_BankCustOut_$date.csv";

        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('partial.bulk')]);

    }
    public function finalPayment(Request $request) {
        if ($request->has('become_kia')) {

            $kia = $request->input('become_kia');
            $from = $request->input('registered_from');
            $to = $request->input('registered_to');

            if ($kia == 1) {
                $fullWithdrawals = FullWithdrawalApplication::with(['fullWithdrawal', 'membership' => function ($query) {
                    $query->where('member_status_id', 3);
                }])
//                    ->where('registered_date','>',$from)
//                    ->where('registered_date','<',$to)
                    ->paginate(5);
                dd($fullWithdrawals);

            } else {
                $fullWithdrawals = FullWithdrawalApplication::with(['membership', 'fullWithdrawal'])
                    ->where('registered_date','>',$from)
                    ->where('registered_date','<',$to)
                    ->get();
                dd($fullWithdrawals);

            }

        }

        $fullWithdrawals = [];
        return view('reports.final-payment', compact('fullWithdrawals'));
    }
    public function outstandingDetails(Request $request) {
        $applications = [];
        if ($request->has('type')) {
            $type = $request->input('type');
            $dateRange = $request->input('date_range');
            if ($type == 1){
                $applications = LoanApplication::with(['membership' => function ($query) {
                    $query->where('member_status_id', '!=', 8);
                }])
                    ->where('processing', '!=', '0')
                    ->where('registered_date', '<=', $dateRange)
                    ->get();

                foreach ($applications as $application) {
                    $latestAssignment = $application->assigns->sortByDesc('created_at')->first();

                    $application->userName = $latestAssignment && $latestAssignment->fwd_to
                        ? User::find($latestAssignment->fwd_to)->name ?? '-' : '-';
                }

            } elseif ($type == 2) {
                $applications = PartialWithdrawalApplication::with(['membership' => function ($query) {
                    $query->where('member_status_id', '!=', 8);
                },'withdrawal'])
                    ->where('processing','!=','0')
                    ->where('registered_date','!=',$dateRange)
                    ->get();
                foreach ($applications as $application) {
                    $latestAssignment = $application->assigns->sortByDesc('created_at')->first();

                    $application->userName = $latestAssignment && $latestAssignment->fwd_to
                        ? User::find($latestAssignment->fwd_to)->name ?? '-' : '-';
                }
            } elseif ($type == 3) {
                $applications = FullWithdrawalApplication::with(['membership' => function ($query) {
                    $query->where('member_status_id', '!=', 8);
                }, 'fullWithdrawal'])
                    ->where('processing','!=','0')
                    ->get();
                foreach ($applications as $application) {
                    $latestAssignment = $application->assigns->sortByDesc('created_at')->first();

                    $application->userName = $latestAssignment && $latestAssignment->fwd_to
                        ? User::find($latestAssignment->fwd_to)->name ?? '-' : '-';
                }
            } else {
                return view('reports.outstanding-details');
            }
        }

        return view('reports.outstanding-details', compact('applications'));
    }
    public function pdfOutstandingDetails(Request $request) {

        $data = $this->outstandingDetails($request)->getData();
        $type = $request->input('type');
        $dateRange = $request->input('date_range');

        $applications = $data['applications'];
        if ($type == 1){
            $name = 'Loan';
        } elseif ($type == 2){
            $name = 'Withdrawal';
        } elseif ($type == 3){
            $name = 'Full Withdrawal';
        } else {
            $name = '';
        }
        $pdf = PDF::loadView('reports.pdf-outstanding-details', compact('applications', 'name', 'dateRange'))
            ->setPaper('a4', 'landscape');

        $filename = 'details_report_' . $name . now() . '.pdf';

        return $pdf->download($filename);
    }
    public function loanInstallmentView(Request $request) {

        $regiments = Regiment::all();
        $category = $request->input('category_id');
        $regimentId = $request->input('regiment_id');
        $type = $request->input('type');

        if ($category == 2) {
            $loans = LoanApplication::with(['loan', 'membership', 'repayment' => function($query) use ($request) {
                $query->where('year', $request->year)
                    ->where('month', $request->month);
            }])
                ->whereHas('loan', function ($query) {
                    $query->where('settled', 0);
                })
                ->whereHas('membership', function ($query) use ($category, $regimentId) {
                    $query->where('category_id', $category)
                        ->where('regiment_id', $regimentId);
                })
                ->get();

        } else {
            $loans = LoanApplication::with(['loan', 'membership', 'repayment' => function($query) use ($request) {
                $query->where('year', $request->year)
                    ->where('month', $request->month);
            }])
                ->whereHas('loan', function ($query) {
                    $query->where('settled', 0);
                })
                ->whereHas('membership', function ($query) use ($category, $type) {
                    $query->where('category_id', $category)
                        ->where('type', $type);
                })
                ->get();
        }

        return view('reports.loan-installment', compact('loans', 'regiments'));

    }
    public function ledgerSheet($id)
    {
        $membership = Membership::find($id);
        $contributionSummaries = ContributionSummary::where('membership_id', $id)->get();

        $contributions = Contribution::where('membership_id', $id)->get();

        $withdrawals = PartialWithdrawalApplication::with('withdrawal')
            ->where('member_id', $id)
            ->where('processing', '!=', 2)
            ->get();

        $loans = LoanApplication::with('directSettlement', 'loan')
            ->where('member_id', $id)->get();

        $ledgerData = [];

        foreach ($contributionSummaries as $summary) {
            $year = $summary->year;
            $icp_id = $summary->icp_id;
            $opening_balance = $summary->opening_balance;
            $contribution_amount = $summary->contribution_amount;
            $yearly_interest = $summary->yearly_interest;
            $closing_balance = $summary->closing_balance;

            $months = [
                'Jan' => 0, 'Feb' => 0, 'Mar' => 0, 'Apr' => 0,
                'May' => 0, 'Jun' => 0, 'Jul' => 0, 'Aug' => 0,
                'Sep' => 0, 'Oct' => 0, 'Nov' => 0, 'Dec' => 0
            ];

            foreach ($contributions as $contribution) {
                if ($contribution->year == $year) {
                    switch ($icp_id) {
                        case 0:
                            $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            break;
                        case 1:
                            if ($contribution->month <= 6) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                        case 2:
                            if ($contribution->month > 6) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                        case 10:
                            if ($contribution->month <= 3) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                        case 20:
                            if ($contribution->month > 3 && $contribution->month <= 6) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                        case 30:
                            if ($contribution->month > 6 && $contribution->month <= 9) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                        case 40:
                            if ($contribution->month > 9) {
                                $months[$this->getMonthAbbreviation($contribution->month)] += $contribution->amount;
                            }
                            break;
                    }
                }
            }

            $row = [
                'year' => $year,
                'icp_id' => $icp_id,
                'opening_balance' => $opening_balance,
                'Jan' => $months['Jan'],
                'Feb' => $months['Feb'],
                'Mar' => $months['Mar'],
                'Apr' => $months['Apr'],
                'May' => $months['May'],
                'Jun' => $months['Jun'],
                'Jul' => $months['Jul'],
                'Aug' => $months['Aug'],
                'Sep' => $months['Sep'],
                'Oct' => $months['Oct'],
                'Nov' => $months['Nov'],
                'Dec' => $months['Dec'],
                'contribution_amount' => $contribution_amount,
                'yearly_interest' => $yearly_interest,
                'withdrawal_amount' => 0,
                'settlement_amount' => 0,
                'closing_balance' => $closing_balance
            ];

            foreach ($withdrawals as $withdrawal) {
                $w_year = date('Y', strtotime($withdrawal->registered_date));
                $w_month = date('M', strtotime($withdrawal->registered_date));
                if ($w_year == $year) {
                    switch ($icp_id) {
                        case 0:
                            $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            break;
                        case 1:
                            if ($w_month <= 6) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                        case 2:
                            if ($w_month > 6) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                        case 10:
                            if ($w_month <= 3) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                        case 20:
                            if ($w_month > 3 && $w_month <= 6) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                        case 30:
                            if ($w_month > 6 && $w_month <= 9) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                        case 40:
                            if ($w_month > 9) {
                                $row['withdrawal_amount'] = $withdrawal->withdrawal->approved_amount;
                            }
                            break;
                    }

                }
            }

            foreach ($loans as $settlement) {
                if ($settlement->directSettlement) {
                    $s_year = date('Y', strtotime($settlement->directSettlement->settlement_date));
                    $s_month = date('M', strtotime($settlement->directSettlement->settlement_date));
                    if ($s_year == $year) {
                        switch ($icp_id) {
                            case 0:
                                $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                break;
                            case 1:
                                if ($s_month <= 6) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                            case 2:
                                if ($s_month > 6) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                            case 10:
                                if ($s_month <= 3) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                            case 20:
                                if ($s_month > 3 && $w_month <= 6) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                            case 30:
                                if ($s_month > 6 && $w_month <= 9) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                            case 40:
                                if ($s_month > 9) {
                                    $row['settlement_amount'] = $settlement->directSettlement->settlement_amount;
                                }
                                break;
                        }

                    }
                }
            }

            $ledgerData[] = $row;
        }

//        return view('reports.pdf-ledger-sheet', compact('ledgerData', 'membership', 'loans'));
        $pdf = PDF::loadView('reports.pdf-ledger-sheet', compact('ledgerData',
            'membership', 'loans'))
            ->setPaper('a4', 'landscape');

        $filename = $membership->regimental_number . '_ledger-sheet_' . now() . '.pdf';

        return $pdf->download($filename);
    }

    // Helper function to get month abbreviation
    private function getMonthAbbreviation($monthNumber)
    {
        return date('M', mktime(0, 0, 0, $monthNumber, 1));
    }
    public function installmentCSV(Request $request)
    {
        $category = $request->input('category_id');
        $regimentId = $request->input('regiment_id');
        $type = $request->input('type');

        if ($category == 2) {
            $loans = LoanApplication::with(['loan', 'membership', 'repayment' => function($query) use ($request) {
                $query->where('year', $request->year)
                    ->where('month', $request->month);
            }])
                ->whereHas('loan', function ($query) {
                    $query->where('settled', 0);
                })
                ->whereHas('membership', function ($query) use ($category, $regimentId) {
                    $query->where('category_id', $category)
                        ->where('regiment_id', $regimentId);
                })
                ->get();

            $regiment = Regiment::where('id', $regimentId)->select('regiment_name')->first();
            $filename = "ORs-loan_$regiment->regiment_name.csv";

        } else {
            $loans = LoanApplication::with(['loan', 'membership', 'repayment' => function($query) use ($request) {
                $query->where('year', $request->year)
                    ->where('month', $request->month);
            }])
                ->whereHas('loan', function ($query) {
                    $query->where('settled', 0);
                })
                ->whereHas('membership', function ($query) use ($category, $type) {
                    $query->where('category_id', $category)
                        ->where('type', $type);
                })
                ->get();

            $filename = "OFFICERs-loan_$type.csv";
        }

        $csvData = "Application No,Reg No,Name,Unit,Total Capital,Recovered Capital,Payment No, Capital Due, Interest Due, Installment\n";

        foreach ($loans as $loan) {
            $csvData .= ($loan->application_reg_no ?? 'NA') . ',';
            $csvData .= ($loan->membership->regimental_number ?? 'NA') . ',';
            $csvData .= ($loan->membership->ranks->rank_name ?? '-') . ' ' . ($loan->membership->name ?? '-') . ',';
            $csvData .= ($loan->membership->regiments->regiment_name ?? '-'). ',';
            $csvData .= ($loan->loan->total_capital ?? '-') . ',';
            $csvData .= ($loan->loan->total_recovered_capital ?? '') . ',';
            $csvData .= (($loan->repayment[0]->payment_no) ?? '') . ',';
            $csvData .= (($loan->repayment[0]->capital_due) ?? '') . ',';
            $csvData .= (($loan->repayment[0]->interest_due) ?? '') . ',';
            $csvData .= ($loan->repayment[0]->capital_due + $loan->repayment[0]->interest_due ?? 0) . "\n";
        }
        $headers = array(
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        );

        $response = Response::make($csvData, 200, $headers);

        return $response->withHeaders(['Refresh' => "0;url=" . route('loan-installment')]);
    }

    public function loanDisburseView() {
        $loans = LoanApplication::with(['loan', 'membership'])
            ->where('is_banked','0')
            ->get();

        $total = 0;

        $loans->each(function ($loan) use (&$total) {
            $total += $loan->approved_amount;

        });
        return view('reports.disburse-loan', compact('loans', 'total'));
    }
    public function loanDisbursePDF() {
        $loans = LoanApplication::with(['loan', 'membership'])
            ->where('processing','>', '5')
            ->where('is_banked','=','0')
            ->get();

        $total = 0;

        $loans->each(function ($loan) use (&$total) {
            $total += $loan->approved_amount;

        });
        view()->share('loans',$loans);
        view()->share('total', $total);

        $pdf = PDF::loadView('reports.pdf-disburse-loan');

        $footerHtml = view('reports.pdf-disburse-loan', ['pdf' => $pdf])->render();
        $pdf->setOptions(['footer-html' => $footerHtml]);
        return $pdf->download('disbursed-loans-' . date('Y-m-d') . '.pdf');
    }

    public function partialDisburseView() {
        $partialWithdrawals = PartialWithdrawalApplication::with(['withdrawal', 'membership'])
            ->where('is_banked','0')
            ->get();

        $total = 0;

        $partialWithdrawals->each(function ($partialWithdrawals) use (&$total) {
            $total += $partialWithdrawals->withdrawal->total_withdraw_amount;

        });
        return view('reports.disburse-partial', compact('partialWithdrawals', 'total'));
    }
    public function partialDisbursePDF() {
        $partialWithdrawals = PartialWithdrawalApplication::with(['withdrawal', 'membership'])
            ->where('processing','5')
            ->where('is_banked','0')
            ->get();

        $total = 0;

        $partialWithdrawals->each(function ($partialWithdrawals) use (&$total) {
            $total += $partialWithdrawals->withdrawal->total_withdraw_amount;

        });

        $pdf = PDF::loadView('reports.pdf-disburse-partial', compact('partialWithdrawals', 'total'));
        return $pdf->download('disbursed-partial-' . date('Y-m-d') . '.pdf');
    }

    public function fullDisburseView() {
        $fullWithdrawals = FullWithdrawalApplication::with(['fullWithdrawal', 'membership'])
            ->where('is_banked','0')
            ->get();

        $total = 0;

        $fullWithdrawals->each(function ($fullWithdrawals) use (&$total) {
            $total += $fullWithdrawals->fullWithdrawal->withdrawal_amount;

        });
        return view('reports.disburse-full', compact('fullWithdrawals', 'total'));
    }

    public function fullDisbursePDF() {
        $fullWithdrawals = FullWithdrawalApplication::with(['fullWithdrawal', 'membership'])
            ->where('processing','5')
            ->where('is_banked','0')
            ->get();

        $total = 0;

        $fullWithdrawals->each(function ($fullWithdrawals) use (&$total) {
            $total += $fullWithdrawals->fullWithdrawal->withdrawal_amount;

        });

        $pdf = PDF::loadView('reports.pdf-disburse-full', compact('fullWithdrawals', 'total'));
        return $pdf->download('disbursed-full-' . date('Y-m-d') . '.pdf');
    }

    public function fundBalance(Request $request) {
//        if ($request->has('regiment_id')) {
            $members = Membership::select('regimental_number', 'name', 'rank_id', 'regiment_id', 'member_status_id')
                ->with('ranks', 'regiments')
                ->where('member_status_id', '!=',8)
                ->paginate();

            foreach ($members as $member) {
        $id = $member->id;
        $openingBalance = DB::table('contribution_yearly_summary')
            ->where('membership_id', $id)
            ->orderBy('transaction_date')
            ->value('opening_balance');

        $transactionsQuery = DB::table('withdrawal')
            ->select('total_withdraw_amount as transaction_amount')
            ->where('membership_id', $id)
            ->whereNotNull('paid_date')
            ->unionAll(DB::table('contribution')
                ->select('amount as transaction_amount')
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('contribution_additional')
                ->select('amount as transaction_amount')
                ->where('membership_id', $id)
                ->where('accepted', 1)
            )
            ->unionAll(DB::table('contribution_correction')
                ->select('amount as transaction_amount')
                ->where('membership_id', $id)
                ->where('accepted', 1)
            )
            ->unionAll(DB::table('contribution_yearly_summary')
                ->select('yearly_interest as transaction_amount')
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('absent_settlement')
                ->select('settlement_amount as transaction_amount')
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('full_withdrawal')
                ->select('voucher_amount as transaction_amount')
                ->where('membership_id', $id)
            );

        if ($openingBalance > 0) {
            $transactionsQuery = DB::table('contribution_yearly_summary')
                ->select('opening_balance as transaction_amount')
                ->where('membership_id', $id)
                ->orderBy('transaction_date')
                ->limit(1)
                ->unionAll($transactionsQuery);
        }

        $totalFundBalance = $transactionsQuery->sum('transaction_amount');
        $member->fund_balance = $totalFundBalance + $openingBalance;;

            }
//        }
        dd($members);

    }
    public function loanLedger($id)
    {
        $loan = LoanApplication::with('loan', 'repayment')->findOrFail($id);
        $membership = Membership::select('id', 'name', 'regimental_number', 'rank_id', 'regiment_id')->with('ranks', 'regiments')
            ->where('id', $loan->member_id)->get();

//        return view('reports.pdf-ledger-sheet', compact('ledgerData', 'membership', 'loans'));
        $pdf = PDF::loadView('reports.pdf-loan-ledger', compact(
            'membership', 'loan'))
            ->setPaper('a4', 'landscape');

        $filename = $membership->regimental_number . '_loan-ledger_' . now() . '.pdf';

        return $pdf->download($filename);
    }


}
