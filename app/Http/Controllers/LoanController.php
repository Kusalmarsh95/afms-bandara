<?php

namespace App\Http\Controllers;

use App\Models\AbsentSettlement;
use App\Models\AbsentSettlementAssign;
use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\DirectSettlementAssign;
use App\Models\DirectSettlment;
use App\Models\FailureLoanApi;
use App\Models\FullWithdrawalApplication;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanAssign;
use App\Models\LoanProduct;
use App\Models\Membership;
use App\Models\PartialWithdrawalApplication;
use App\Models\PartialWithdrawalLog;
use App\Models\RejectReason;
use App\Models\Suwasahana;
use App\Models\User;
use Carbon\Carbon;
use DateTime;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use PDF;

class LoanController extends Controller
{
    public function index(){
        $loans = LoanApplication::with(['membership', 'rejectReason'])
            ->where('processing', '!=', 0)->get();

        foreach ($loans as $loan) {
            $latestAssignment = $loan->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $loan->userName = $user ? $user->name : '-';
                $loan->reason = $reason ?? '-';
            } else {
                $loan->userName = '-';
                $loan->reason = $reason ?? '-';

            }
        }
        return view('loans.index',compact('loans'));
    }
    public function indexApproved(){
        $loans = LoanApplication::with(['membership', 'rejectReason'])
            ->where('processing', '>', 3)
            ->orWhere('is_banked', '>=', 0)->get();

        $sendToBankCount = LoanApplication::where('processing', '=', 5)->count();
        $payCount = LoanApplication::where('processing', '=', 6)->count();

        foreach ($loans as $loan) {
            $latestAssignment = $loan->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                if ($userId==0){
                    $loan->userName = '-';
                    $loan->reason = $reason ?? '-';
                }else{
                    $user = User::find($userId);
                    $loan->userName = $user ? $user->name : '-';
                    $loan->reason = $reason ?? '-';
                }

            } else {
                $loan->userName = '-';
                $loan->reason = $reason ?? '-';
            }
        }
        return view('loans.index-approved',compact('loans', 'sendToBankCount',
        'payCount'));
    }
    public function create($id)
    {
        $membership = Membership::with('ranks')->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $loanApplication = LoanApplication::with('loan')->where('member_id', $id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $id)->latest('Issue_Date')->first();
        $partialWithdrawal = PartialWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();
        $loan = LoanApplication::latest('registered_date')->first();
        $loanProducts = LoanProduct::where('status', 1)->get();
        $prePartialWithdrawal = PartialWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();
        $preFullWithdrawal = FullWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();

        $currentUser = auth()->user();

        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $transactionController = new MembershipController();
        $result = $transactionController->show($id);
        $fundBalance = $result['fundBalance'];
        $armyEnlisted = $result['armyEnlisted'];
        $difference = $result['difference'];
        $armyService = $result['armyService'];

        if ($membership->member_status_id == 8){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Account already closed!');
        } elseif ($armyService < 5 || $armyEnlisted == 'Date not specified' && $fundBalance <= 100000){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply for a loan, minimum requirement not satisfied!');
        }  elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing==2){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Registered partial withdrawal application is rejected, delete the application and try again!');
        } elseif ($preFullWithdrawal) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, already Withdrew the full balance!');
        } elseif ($loanApplication && $loanApplication->processing!=0) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'The loan application is already being processed.!');
        } elseif ($loanApplication && ($loanApplication->loan && $loanApplication->loan->settled!=1)) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Sorry, cannot apply for a new loan. The previous loan is still recovering.!');
        } elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing!=0) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, partial withdrawal application is already processing!');
        } else {
            if(!$loan){
                $loanNo = '00001';
            }else{
                $loan_reg = $loan->application_reg_no;
                $loan_count = explode('/', $loan_reg);
                if (count($loan_count) >= 3) {
                    $loanNo = (int)$loan_count[2]+1;
                    $loanNo = str_pad($loanNo, 5, '0', STR_PAD_LEFT);
                } else {
                    $loanNo = 'NA';
                }
            }

            if ($difference){
                $installments = $difference->y * 12;
                if ($installments > 120){
                    $installments = 120;
                }
            } else {
                $installments = 0;
//                return redirect()->route('memberships.show', $id)
//                    ->with('success', 'Please specify the retirement date and try again!');
            }

            if ($suwasahana){
                if ($suwasahana->settled==0){
                    $suwasahanaAmount = $suwasahana->total_capital + $suwasahana->total_interest;
                    $recoveredAmount = $suwasahana->total_recovered_capital + $suwasahana->total_recovered_interest;

                    $dueSuwasahana = $suwasahanaAmount - $recoveredAmount;
                }else{
                    $suwasahanaAmount = 0;
                    $recoveredAmount = 0;
                    $dueSuwasahana = 0;
                }
            }else{
                $suwasahanaAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
            }

            return view('loans.create',compact('membership', 'users', 'armyEnlisted', 'armyService',
                'installments', 'partialWithdrawal','banks', 'branches', 'branchCodes','fundBalance', 'suwasahana',
                'loanNo', 'loanProducts', 'suwasahanaAmount', 'recoveredAmount', 'dueSuwasahana','loanApplication'
            ));
        }
    }
    public function store(Request $request, $id){
        $loan = LoanApplication::latest('registered_date')->first();
        if(!$loan){
            $loanNo = '00000';
        }else{
            $loan_reg = $loan->application_reg_no;
            $loan_count = explode('/', $loan_reg);
            if (count($loan_count) >= 3) {
                $loanNo = (int)$loan_count[2];
                $loanNo = str_pad($loanNo, 5, '0', STR_PAD_LEFT);
            } else {
                $loanNo = 'NA';
            }
        }

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $reg_no = $request->input('application_reg_no');
        $reg_no = explode('/', $reg_no);
        if (count($reg_no) >= 3) {
            $reg_no = (int)$reg_no[2];
        } else {
            $reg_no = 'NA';
        }

        if ($reg_no == $loanNo) {
            return redirect()->back()->withErrors(['error' => 'Sorry, Loan application number already exists']);
        } else{
            $validatedRegistration = $request->validate([
                'application_reg_no' => 'required',
                'received_date' => 'required',
                'basic_salary' => 'required',
                'no_of_installments' => 'required',
                'bank_acc_no' => 'required',
                'bank_name' => 'required',
                'branch_code' => 'required',
                'bank_code' => 'required',
                'bank_branch' => 'required',
                'product_id' => 'required',
            ]);

            $validatedRegistration['approved_amount'] = 0;
            $validatedRegistration['suwasahana_amount'] = $request->suwasahana_amount;
            $validatedRegistration['total_salary'] = str_replace(',', '', $request->total_salary);
            $validatedRegistration['salary_40'] = str_replace(',', '', $request->salary_40);
            $validatedRegistration['basic_salary'] = str_replace(',', '', $request->basic_salary);
            $validatedRegistration['deductions'] = str_replace(',', '', $request->deductions);
            $validatedRegistration['good_conduct'] = str_replace(',', '', $request->good_conduct);
            $validatedRegistration['ten_month_loan'] = str_replace(',', '', $request->ten_month_loan);
            $validatedRegistration['incentive'] = str_replace(',', '', $request->incentive);
            $validatedRegistration['other_loan'] = str_replace(',', '', $request->other_loan);
            $validatedRegistration['qualification'] = str_replace(',', '', $request->qualification);
            $validatedRegistration['ration'] = str_replace(',', '', $request->ration);
            $validatedRegistration['special_advance'] = str_replace(',', '', $request->special_advance);
            $validatedRegistration['festival_advance'] = str_replace(',', '', $request->festival_advance);
            $validatedRegistration['fund_balance'] = str_replace(',', '', $request->fund_balance);
            $validatedRegistration['allowed_amount_from_fund'] = str_replace(',', '', $request->allowed_amount_from_fund);
            $validatedRegistration['no_of_installments'] = $request->no_of_installments;
            $validatedRegistration['suggested_amount'] = str_replace(',', '', $request->suggested_amount);
            $validatedRegistration['total_amount_requested'] = str_replace(',', '', $request->total_amount_requested);
            $validatedRegistration['product_id'] = $request->product_id;
            $validatedRegistration['monthly_capital_portion'] = 0;
            $validatedRegistration['registered_date'] = now();
            $validatedRegistration['member_id'] = $id;
            $validatedRegistration['status_id'] = 3010;
            $validatedRegistration['altering'] = 0;
            $validatedRegistration['processing'] = 1;
            $validatedRegistration['created_system'] = 'AFMS';
            $validatedRegistration['currentuser'] = Auth::user()->name;

            LoanApplication::create($validatedRegistration);

            Loan::create([
                'loan_id' => $validatedRegistration['application_reg_no'],
                'application_reg_no' => $validatedRegistration['application_reg_no'],
                'created_system' => $validatedRegistration['created_system'],
            ]);

            $validatedAssign['loan_id'] = $request->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Registered a new loan';

            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')
                ->with('success', 'Loan application created successfully');
        }
    }
    public function view($id){
        $loan = LoanApplication::with(['membership', 'product'])->findOrFail($id);
        $currentUser = auth()->user();

        $rolesToForward = $currentUser->forward_roles ?? [];

        $rolesToForward = array_filter($rolesToForward, function($role) {
            return strpos($role, 'Withdrawal') === false;
        });

        $rolesToForward = array_values($rolesToForward);

        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();

        $rejectReasons = RejectReason::all();

        if ($loan->total_amount_requested == 0){
            $approvedAmount  = $loan->suggested_amount;
        }else {
            $approvedAmount = $loan->total_amount_requested;
        }
        return view('loans.view',compact('loan', 'usersForward', 'usersReject', 'rejectReasons', 'approvedAmount'));
    }
    public function approveReject(Request $request, $id){
        $loan = LoanApplication::with(['membership', 'loan'])->find($id);

        $action = $request->input('approval');

        if ($action === 'forward') {
            $loan->currentuser = Auth::user()->name;
            $loan->reject_reason_id = 0;
            $loan->reject_date = null;
            $loan->reject_level = '';
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for further proceedings.';
            LoanAssign::create($validatedAssign);


            return redirect()->route('loan.index')->with('success', 'Loan application forwarded to process..');

        } elseif ($action === 'process') {
            $loan->processing = 3;
            $loan->reject_reason_id = 0;
            $loan->reject_date = null;
            $loan->reject_level = '';
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and successfully registered for the process.';

            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with(['success' => 'Successfully registered..']);

        }elseif ($action === 'approve') {
            $loan->approved_amount = $request->input('approved_amount');
            $loan->approved_date = now()->format('Y-m-d');
            $loan->processing = 4;
            $loan->reject_reason_id = 0;
            $loan->reject_date = null;
            $loan->reject_level = '';
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and approved for the payment';

            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with(['success' => 'Loan application approved..']);

        }elseif ($action === 'reject') {
            $loan->processing = 2;
            $loan->is_banked = '';
            $loan->reject_reason_id = $request->input('fwd_to_reason');
            $loan->reject_date = now();
            $loan->reject_level = Auth::user()->name;
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with(['success' => 'Rejected the application']);


        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function approved($id){
        $loan = LoanApplication::with('membership', 'product', 'loan')->find($id);
//        $users  = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];

        $rolesToForward = array_filter($rolesToForward, function($role) {
            return strpos($role, 'Withdrawal') === false;
        });

        $rolesToForward = array_values($rolesToForward);

        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();


        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();
        $rejectReasons = RejectReason::all();

//        if ($loan->approved_date=='2000-01-01'){
//            $monthlyCapital = $loan->loan->total_capital / $loan->no_of_installments;
//            $remainingLoan = $loan->loan->total_capital;
//        } else {
//            $monthlyCapital = $loan->approved_amount / $loan->no_of_installments;
//            $remainingLoan = $loan->approved_amount;
//        }
//        $repaymentSchedule = [];
//
//        $currentMonth = (int) date('m', strtotime($loan->approved_date));
//        if ($currentMonth == 12){
//            $month = 1;
//            $currentYear = (int) date('Y', strtotime($loan->approved_date)) + 1;
//        } else {
//            $month = $currentMonth + 1;
//            $currentYear = (int) date('Y', strtotime($loan->approved_date));
//        }
//
//        $nextDueDate = date('Y-m-d', strtotime("$currentYear-$month-21"));
//
//        for ($installment = 1; $installment <= $loan->no_of_installments; $installment++) {
//            $monthlyInterest = ($remainingLoan * $loan->product->interest_rate) / (1200);
//
//            $totalInstallment = $monthlyCapital + $monthlyInterest;
//
//            $repaymentSchedule[] = [
//                'installment_number' => $installment,
//                'due_date' => $nextDueDate,
//                'monthly_capital' => $monthlyCapital,
//                'monthly_interest' => $monthlyInterest,
//                'to_recover' => $remainingLoan,
//                'total_installment' => $totalInstallment,
//            ];
//
//            $nextDueDate = date('Y-m-21', strtotime('+1 month', strtotime($nextDueDate)));
//
//            $remainingLoan -= $monthlyCapital;
//        }

        $approvedDate = new DateTime($loan->approved_date);
        $endOfMonth = clone $approvedDate;
        $endOfMonth->modify('last day of this month');
        $endOfDay = $endOfMonth->setTime(23, 59, 59);

        $dateDifference = $approvedDate->diff($endOfDay);

        $arrearsDays = $dateDifference->days;

        $arrearsInterest = ($loan->approved_amount * ($loan->product->interest_rate/100) * $arrearsDays)/360;

        return view('loans.view-disburse',compact('loan', 'usersForward', 'usersReject',
            'rejectReasons', 'arrearsDays', 'arrearsInterest'));
    }
    public function sendToBulk(Request $request)
    {
        $request->validate([
            'loan_ids' => 'required|array',
            'loan_ids.*' => 'integer|exists:loan_application,id',
        ]);


        LoanApplication::whereIn('id', $request->loan_ids)
            ->update([
                'processing' => 5,
                'is_banked' => 0,
                'currentuser' => Auth::user()->name
            ]);

        $loans = LoanApplication::whereIn('id', $request->loan_ids)->get();

        $rolesToForward = ['Account OC', 'Account Section OC'];
        $user = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })
            ->latest()
            ->first();

        foreach ($loans as $loan) {
            LoanAssign::create([
                'loan_id' => $loan->application_reg_no,
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Checked and forwarded to process',
                'fwd_to' => $user->id,
                'fwd_to_reason' => 'Send to Approval',
            ]);
        }

        return response()->json(['message' => 'Processing status updated successfully.']);
    }
    public function disburse(Request $request, $id){
        $loan = LoanApplication::with(['membership', 'loan','product'])->find($id);
        $action = $request->approval;

        if ($action === 'forward') {
            $loan->currentuser = Auth::user()->name;
            $loan->reject_reason_id = 0;
            $loan->reject_date = null;
            $loan->reject_level = '';

            $voucher_no = $request->validate([
                'voucher_id' => 'required',
                'file_ref_no' => 'required',
            ]);
            $loan->voucher_id = $voucher_no['voucher_id'];
            $loan->file_ref_no = $voucher_no['file_ref_no'];
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded to process.';
            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with('success', 'Application forwarded to process');

        } elseif ($action === 'send') {

            $loan->processing = 5;
            $loan->currentuser = Auth::user()->name;
            $voucher_no = $request->validate([
                'voucher_id' => 'required',
                'file_ref_no' => 'required',
            ]);
            $loan->voucher_id = $voucher_no['voucher_id'];
            $loan->file_ref_no = $voucher_no['file_ref_no'];
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for disbursement.';
            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with('success', 'Application forwarded for disbursement');

        } elseif ($action === 'disburse') {
            $loan->arrest_dates = $request->arrest_dates;
            $loan->arrest_interest = $request->arrest_interest;
            $loan->monthly_capital_portion = $request->monthly_capital_portion;
            $loan->status_id = 3180;
            $loan->processing = 0;
            $loan->is_banked = 0;
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            $loan->loan->total_capital = $request->approved_amount;
            $loan->loan->current_monthly_capital_portion = $request->monthly_capital_portion;
            $loan->loan->no_of_installments_paid = 0;
            $loan->loan->settled = 0;
            $loan->loan->total_recovered_capital = 0;
            $loan->loan->total_recovered_interest = 0;
            $loan->loan->over_due = 0;

            $loan->loan->save();

            LoanAssign::create([
                'loan_id' => $loan->application_reg_no,
                'fwd_to' => 0,
                'fwd_to_reason' => 'Successful',
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Paid',
            ]);

            return redirect()->route('loan.index')->with(['success' => 'Successfully Disbursed..']);

        } elseif ($action === 'reject') {
            $loan->processing = 2;
            $loan->is_banked = '';
            $loan->reject_reason_id = $request->input('fwd_to_reason');
            $loan->reject_date = now();
            $loan->reject_level = Auth::user()->name;
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            LoanAssign::create($validatedAssign);

            return redirect()->route('loan.index')->with(['success' => 'Rejected the application']);

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
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
    public function releasePayment(Request $request)
    {
        $loans = LoanApplication::with([
            'loan',
            'membership:id,enumber,regimental_number,name', 'product'
        ])
            ->where('processing', '5')
            ->where('is_banked', '0')
            ->get();


        $action = $request->approval;
        $voucherId = Carbon::now()->format('Ymd');

        if ($action !== 'pay') {
            return redirect()->back()->with('error', 'Invalid action');
        }

        $failedApiCount = 0;
        $payloads = [];
        $loanMap = []; // map index => loan
        if (!$this->authenticateApi()) {
            return response()->json(['error' => 'Failed to authenticate with external API'], 500);
        }
        // Prepare payloads and update loans locally
        foreach ($loans as $index => $loan) {
            $approvedDate = new DateTime($loan->approved_date);
            $endOfMonth = clone $approvedDate;
            $endOfMonth->modify('last day of this month')->setTime(23, 59, 59);
            $arrearsDays = $approvedDate->diff($endOfMonth)->days;
            $arrearsInterest = ($loan->approved_amount * ($loan->product->interest_rate ?? 16 / 100) * $arrearsDays) / 360;
            $loanAmountPerInstallment = round($loan->approved_amount / $loan->no_of_installments, 2);

            // Update loan application fields
            $loan->voucher_id = $voucherId;
            $loan->processing = 6;
            $loan->arrest_dates = $arrearsDays;
            $loan->arrest_interest = $arrearsInterest;
            $loan->monthly_capital_portion = $loanAmountPerInstallment;
            $loan->status_id = 3180;
            $loan->currentuser = Auth::user()->name;
            $loan->save();

            // Update related loan details
            $loan->loan->total_capital = $loan->approved_amount;
            $loan->loan->current_monthly_capital_portion = $loanAmountPerInstallment;
            $loan->loan->no_of_installments_paid = 0;
            $loan->loan->settled = 0;
            $loan->loan->total_recovered_capital = 0;
            $loan->loan->total_recovered_interest = 0;
            $loan->loan->over_due = 0;
            $loan->loan->save();

//            LoanAssign::create([
//                'loan_id' => $loan->application_reg_no,
//                'fwd_to' => 0,
//                'fwd_to_reason' => 'Successful',
//                'fwd_by' => Auth::user()->id,
//                'fwd_by_reason' => 'Paid',
//            ]);
            $payloads[] = [
                "cashbookId" => 'CB078',
                "credit" => 0,
                "debit" => $loan->approved_amount,
                "transactionDate" => now()->toIso8601String(),
                "customer" => $loan->membership->regimental_number . '-' . $loan->membership->enumber ?? '000000000',
                "description" => $loan->membership->name ?? '',
                "reference" => 'Loan Grant',
                "comments" => 'Loan disbursement for ' . $loan->application_reg_no,
                'gl' => false,
                'ar' => true,
            ];

            $loanMap[$index] = $loan;  // store loan by index for failure logging
        }

        // Send batch API request
        $response = $this->sendCashBookUpdate($payloads);

        // Handle failures with detailed messages
//        if (!$response['success']) {
//            $failedApiCount = count($response['failedIndexes'] ?? []);
//            $failures = $response['failures'] ?? [];  // array with ['index' => int, 'reason' => string]
//
//            foreach ($failures as $failure) {
//                $failedIndex = $failure['index'] ?? null;
//                $reason = $failure['reason'] ?? 'Unknown API failure';
//
//                if ($failedIndex !== null && isset($loanMap[$failedIndex])) {
//                    $loan = $loanMap[$failedIndex];
//                    FailureLoanApi::create([
//                        'enumber' => $loan->membership->enumber,
//                        'amount' => $loan->approved_amount,
//                        'reference' => 'Batch API failed - ' . $loan->loan_id,
//                        'reason' => $reason,
//                    ]);
//                }
//            }
//        }

        return redirect()->back()->with('success', "Disbursement finalized. API Failures: {$failedApiCount}");
    }

//    public function releasePayment(Request $request){
//        $loans = LoanApplication::with(['loan', 'membership'])
//            ->where('processing','5')
//            ->where('is_banked','=','0')
//            ->get();
//        $action = $request->approval;
//        $voucherId = Carbon::now()->format('Ymd');
//
//        if ($action === 'pay') {
//            $failedApiCount = 0;
//            foreach ($loans as $loan) {
//                $approvedDate = new DateTime($loan->approved_date);
//                $endOfMonth = clone $approvedDate;
//                $endOfMonth->modify('last day of this month');
//                $endOfDay = $endOfMonth->setTime(23, 59, 59);
//                $dateDifference = $approvedDate->diff($endOfDay);
//                $arrearsDays = $dateDifference->days;
//                $arrearsInterest = ($loan->approved_amount * ($loan->product->interest_rate/100) * $arrearsDays)/360;
//                $loanAmountPerInstallment = round($loan->approved_amount / $loan->no_of_installments, 2);
//
//                $loan->voucher_id = $voucherId;
//                $loan->processing = 6;
//                $loan->arrest_dates = $arrearsDays;
//                $loan->arrest_interest = $arrearsInterest;
//                $loan->monthly_capital_portion = $loanAmountPerInstallment;
//                $loan->status_id = 3180;
//                $loan->currentuser = Auth::user()->name;
//                $loan->save();
//
//                $loan->loan->total_capital = $loan->approved_amount;
//                $loan->loan->current_monthly_capital_portion = $loanAmountPerInstallment;
//                $loan->loan->no_of_installments_paid = 0;
//                $loan->loan->settled = 0;
//                $loan->loan->total_recovered_capital = 0;
//                $loan->loan->total_recovered_interest = 0;
//                $loan->loan->over_due = 0;
//
//                $loan->loan->save();
//
//                LoanAssign::create([
//                    'loan_id' => $loan->application_reg_no,
//                    'fwd_to' => 0,
//                    'fwd_to_reason' => 'Successful',
//                    'fwd_by' => Auth::user()->id,
//                    'fwd_by_reason' => 'Paid',
//                ]);
//
//                if (!$this->sendLoanCashBookUpdate($loan)) {
//                    $failedApiCount++;
//                }
//            }
//
//            return redirect()->back()->with('success', 'Disbursement finalized. API Failures: ' . $failedApiCount);
//        } else {
//            return redirect()->back()->with('error', 'Invalid action');
//        }
//    }
//    private function sendLoanCashBookUpdate($loan)
//    {
//        if (!$this->authenticateApi()) {
//            FailureLoanApi::create([
//                'enumber' => $loan->membership->enumber,
//                'amount' => $loan->approved_amount,
//                'reference' => 'Loan',
//                'reason' => 'Authentication failed',
//            ]);
//            return false;
//        }
//
//        $payload = [
//            "cashbookId" => 'CB078',
//            "credit" => 0,
//            "debit" => $loan->approved_amount,
//            "transactionDate" => now()->toIso8601String(),
//            "customer" => $loan->membership->regimental_number.'-'.$loan->membership->enumber,
//            "description" => $loan->membership->name ?? '',
//            "reference" => 'Loan Grant',
//            "comments" => 'Loan disbursement for '. $loan->loan_id,
//            'gl' => false,
//            'ar' => true,
//        ];
//
//        try {
//            $response = Http::withHeaders([
//                'Authorization' => 'Bearer ' . $this->apiToken,
//                'Accept' => 'application/json',
//            ])->post($this->apiBaseUrl . '/api/Transaction/CashBookUpdate', $payload);
//
//            if (!$response->successful()) {
//                FailureLoanApi::create([
//                    'enumber' => $loan->membership->enumber,
//                    'amount' => $loan->approved_amount,
//                    'reference' => 'API',
//                    'reason' => 'API response failed: ' . $response->body(),
//                ]);
//                return false;
//            }
//
//            return true;
//
//        } catch (\Exception $e) {
//            FailureLoanApi::create([
//                'enumber' => $loan->membership->enumber,
//                'amount' => $loan->approved_amount,
//                'reference' => 'API',
//                'reason' => 'Exception: ' . $e->getMessage(),
//            ]);
//            return false;
//        }
//    }
    private function sendCashBookUpdate(array $payload)
    {
        try {
            $response = Http::timeout(120)
                ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiToken,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl . '/api/Transaction/CashBookUpdate', $payload);
            $logContent = [
                'timestamp' => now()->toDateTimeString(),
                'success' => $response->successful(),
                'status' => $response->status(),
                'payload' => $payload[0]['customer'] ?? 'N/A',
                'response' => $response->body(),
            ];

            $filename = 'loan_cashbook_response_' . now()->format('Ymd_His') . '_' . uniqid() . '.log';
            $logPath = storage_path('logs/cashbook/' . $filename);

            // Ensure the directory exists
            if (!file_exists(dirname($logPath))) {
                mkdir(dirname($logPath), 0755, true);
            }

            file_put_contents($logPath, print_r($logContent, true));

            return [
                'success' => $response->successful(),
                'message' => $response->body(),
            ];
        } catch (\Exception $e) {
            $logContent = [
                'timestamp' => now()->toDateTimeString(),
                'success' => false,
                'error' => $e->getMessage(),
                'payload' => $payload[0]['customer'] ?? 'N/A',
            ];

            $filename = 'loan_cashbook_error_' . now()->format('Ymd_His') . '_' . uniqid() . '.log';
            $logPath = storage_path('logs/cashbook/' . $filename);

            if (!file_exists(dirname($logPath))) {
                mkdir(dirname($logPath), 0755, true);
            }

            file_put_contents($logPath, print_r($logContent, true));

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public function banked(Request $request){
        $loans = LoanApplication::with(['loan', 'membership'])
            ->where('processing','6')
            ->where('is_banked','=','0')
            ->get();
        $action = $request->approval;

        if ($action === 'banked') {
            foreach ($loans as $loan) {
                $loan->processing = 0;
                $loan->is_banked = 1;
                $loan->save();
            }

            return redirect()->route('loan.bulk')->with('success', 'Disbursement finalized.');
        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function show($id) {
        $loan = LoanApplication::with('membership', 'loan', 'repayment', 'product')->findOrFail($id);

//        $monthlyCapital = $loan->monthly_capital_portion;
//        if ($loan->approved_date=='2000-01-01'){
//            $remainingLoan = $loan->loan->total_capital;
//        } else {
//            $remainingLoan = $loan->approved_amount;
//        }
//        $repaymentSchedule = [];
//
//        $currentMonth = (int) date('m', strtotime($loan->approved_date));
//        $currentDate = (int) date('d', strtotime($loan->approved_date));
//
//        if ($currentMonth == 12){
//            if ($currentDate < 18) {
//                $month = 1;
//                $currentYear = (int) date('Y', strtotime($loan->approved_date)) + 1;
//            } else {
//                $month = 2;
//                $currentYear = (int) date('Y', strtotime($loan->approved_date)) + 1;
//            }
//
//        } else {
//            if ($currentDate < 18) {
//                $month = $currentMonth + 1;
//                $currentYear = (int) date('Y', strtotime($loan->approved_date));
//            } else {
//                $month = $currentMonth + 2;
//                $currentYear = (int) date('Y', strtotime($loan->approved_date));
//            }
//
//        }
//
//        $nextDueDate = date('Y-m-d', strtotime("$currentYear-$month-21"));
//
//        for ($installment = 1; $installment <= $loan->no_of_installments; $installment++) {
//            $monthlyInterest = ($remainingLoan * $loan->product->interest_rate) / (1200);
//
//            $totalInstallment = $monthlyCapital + $monthlyInterest;
//
//            if ($installment == 1){
//                $totalInstallment = $monthlyCapital + $monthlyInterest + $loan->arrest_interest;
//            }
//            $repaymentDetails = [
//                'installment_number' => $installment,
//                'due_date' => $nextDueDate,
//                'monthly_capital' => $monthlyCapital,
//                'monthly_interest' => $monthlyInterest,
//                'to_recover' => $remainingLoan,
//                'total_installment' => $totalInstallment,
//                'repayment_data' => null,
//            ];
//
//            $repaymentRecord = $loan->repayment->where('payment_no', $installment)->first();
//
//            if ($repaymentRecord) {
//                $paymentDateTime = \DateTime::createFromFormat('Y-m-d', $repaymentRecord->payment_date);
//                $nextDueDateTime = \DateTime::createFromFormat('Y-m-d', $nextDueDate);
//
//                $interval = $paymentDateTime->diff($nextDueDateTime);
//                $differenceInMonths = $interval->format('%m');
//
//                if ($differenceInMonths > 0){
//
//                    $adjustDate = $nextDueDate;
//                    for ($i = 0; $i < $differenceInMonths; $i++) {
//                        $adjustedRepaymentDetails = [
//                            'installment_number' => $installment,
//                            'due_date' => $adjustDate,
//                            'monthly_capital' => $monthlyCapital,
//                            'monthly_interest' => $monthlyInterest,
//                            'to_recover' => $remainingLoan,
//                            'total_installment' => $totalInstallment,
//                            'repayment_data' => [
//                                'payment_no' => $repaymentRecord->payment_no,
//                                'payment_date' => null,
//                                'capital_received' => null,
//                                'interest_received' => null,
//                            ],
//                        ];
//                        $adjustDate = date('Y-m-21', strtotime("+1 month", strtotime($adjustDate)));
//
//                        $repaymentSchedule[] = $adjustedRepaymentDetails;
//                    }
//                    $nextDueDate = date('Y-m-21', strtotime("+$differenceInMonths month", strtotime($nextDueDate)));
//
//                    $differenceInMonths = 0;
//                    $repaymentDetails = [
//                        'installment_number' => $installment,
//                        'due_date' => $adjustDate,
//                        'monthly_capital' => $monthlyCapital,
//                        'monthly_interest' => $monthlyInterest,
//                        'to_recover' => $remainingLoan,
//                        'total_installment' => $totalInstallment,
//                        'repayment_data' => [
//                            'payment_no' => $repaymentRecord->payment_no,
//                            'payment_date' => $repaymentRecord->payment_date,
//                            'capital_received' => $repaymentRecord->capital_received,
//                            'interest_received' => $repaymentRecord->interest_received,
//                        ],
//                    ];
//
//                    $repaymentSchedule[] = $repaymentDetails;
//                    $remainingLoan -= $repaymentRecord->capital_received;
//
//                } else {
//
//                    $repaymentDetails['repayment_data'] = [
//                        'payment_no' => $repaymentRecord->payment_no,
//                        'payment_date' => $repaymentRecord->payment_date,
//                        'capital_received' => $repaymentRecord->capital_received,
//                        'interest_received' => $repaymentRecord->interest_received,
//                    ];
//                    $repaymentSchedule[] = $repaymentDetails;
//                    $remainingLoan -= $repaymentRecord->capital_received;
//
//                }
//            } else {
//                if($remainingLoan < $monthlyCapital ){
//                    $repaymentDetails['monthly_capital'] = $remainingLoan;
//                    $repaymentDetails['total_installment'] = $remainingLoan+$monthlyInterest;
//                    $repaymentSchedule[] = $repaymentDetails;
//
//                    break;
//                } else if($installment == $loan->no_of_installments && $remainingLoan > $monthlyCapital){
//                    $monthlyCapital = $remainingLoan;
//                    $repaymentSchedule[] = $repaymentDetails;
//                } else {
//                    $repaymentSchedule[] = $repaymentDetails;
//                    $remainingLoan -= $monthlyCapital;
//                }
//
//            }
//
//            $nextDueDate = date('Y-m-21', strtotime('+1 month', strtotime($nextDueDate)));
//
//        }

//        return view('loans.show', compact('loan', 'repaymentSchedule'));
        return view('loans.show', compact('loan'));
    }
    public function edit($id){
        $loan = LoanApplication::with(['membership', 'product'])->findOrFail($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $loanProducts = LoanProduct::where('status', 1)->get();
        $suwasahana = Suwasahana::where('member_id', $loan->member_id)->latest('Issue_Date')->first();
//        $users  = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rejectReasons = RejectReason::all();

        if ($loan->total_amount_requested == 0){
            $approvedAmount  = $loan->suggested_amount;
        }else {
            $approvedAmount = $loan->total_amount_requested;
        }
        $transactionController = new MembershipController();
        $result = $transactionController->show($loan->member_id);
        $fundBalance = $result['fundBalance'];
//        $armyEnlisted = $result['armyEnlisted'];
        $difference = $result['difference'];
//        $differenceEnlisted = $result['differenceEnlisted'];

        if ($difference){
            $installments = $difference->y * 12;
        } else {
            $installments = 0;
        }

        if ($suwasahana){
            if ($suwasahana->settled==0){
                $suwasahanaAmount = $suwasahana->total_capital + $suwasahana->total_interest;
                $recoveredAmount = $suwasahana->total_recovered_capital + $suwasahana->total_recovered_interest;

                $dueSuwasahana = $suwasahanaAmount - $recoveredAmount;
            }else{
                $suwasahanaAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
            }
        }else{
            $suwasahanaAmount = 0;
            $recoveredAmount = 0;
            $dueSuwasahana = 0;
        }
        return view('loans.edit',compact('loan', 'banks', 'branches', 'branchCodes','usersForward',
            'rejectReasons', 'approvedAmount', 'fundBalance', 'installments', 'loanProducts', 'dueSuwasahana', 'suwasahana',
            'recoveredAmount', 'suwasahanaAmount'));
    }
    public function update(Request $request, $id){
//        dd($request);
        $loan = LoanApplication::find($id);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $validatedRegistration = $request->validate([
            'application_reg_no' => 'required',
            'received_date' => 'required',
            'basic_salary' => 'required',
            'no_of_installments' => 'required',
            'bank_acc_no' => 'required',
            'bank_name' => 'required',
            'bank_branch' => 'required',
        ]);
        $validatedRegistration['suwasahana_amount'] = $request->suwasahana_amount;
        $validatedRegistration['total_salary'] = str_replace(',', '', $request->total_salary);
        $validatedRegistration['salary_40'] = str_replace(',', '', $request->salary_40);
        $validatedRegistration['basic_salary'] = str_replace(',', '', $request->basic_salary);
        $validatedRegistration['deductions'] = str_replace(',', '', $request->deductions);
        $validatedRegistration['good_conduct'] = str_replace(',', '', $request->good_conduct);
        $validatedRegistration['ten_month_loan'] = str_replace(',', '', $request->ten_month_loan);
        $validatedRegistration['incentive'] = str_replace(',', '', $request->incentive);
        $validatedRegistration['other_loan'] = str_replace(',', '', $request->other_loan);
        $validatedRegistration['qualification'] = str_replace(',', '', $request->qualification);
        $validatedRegistration['ration'] = str_replace(',', '', $request->ration);
        $validatedRegistration['special_advance'] = str_replace(',', '', $request->special_advance);
        $validatedRegistration['festival_advance'] = str_replace(',', '', $request->festival_advance);
        $validatedRegistration['fund_balance'] = str_replace(',', '', $request->fund_balance);
        $validatedRegistration['allowed_amount_from_fund'] = str_replace(',', '',$request->allowed_amount_from_fund);
        $validatedRegistration['suggested_amount'] = str_replace(',', '',$request->suggested_amount);
        $validatedRegistration['total_amount_requested'] = str_replace(',', '',$request->total_amount_requested);
        $validatedRegistration['product_id'] = $request->product_id;
        $validatedRegistration['altering'] = $loan->altering + 1;
//        $validatedRegistration['processing'] = 1;
        $validatedRegistration['created_system'] = 'AFMS';
        $validatedRegistration['currentuser'] = Auth::user()->name;

//        dd($validatedRegistration);
        $loan -> update($validatedRegistration);

        $validatedAssign['loan_id'] = $request->application_reg_no;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Updated the loan details';

        LoanAssign::create($validatedAssign);

        return redirect()->route('loan.index')
            ->with('success', 'Loan application updated successfully');
    }
    public function indexSettlement(){
        $loans = LoanApplication::with(['loan', 'directSettlement', 'membership'])
            ->whereHas('directSettlement', function ($query) {
                $query->where('approved', '!=', 1);
            })
            ->get();
        foreach ($loans as $loan) {
            $latestAssignment = $loan->settlementAssigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $loan->userName = $user ? $user->name : '-';
                $loan->reason = $reason ?? '-';
            } else {
                $loan->userName = '-';
                $loan->reason = $reason ?? '-';
            }
        }
        return view('loans.index-settlement',compact('loans'));
    }
    public function absentSettlement()
    {
        $fourMonths = Carbon::now()->subMonths(8);

        $loans = LoanApplication::where('processing', 0)
            ->where('last_pay_date', '<', $fourMonths)
            ->whereHas('loan', function ($query) {
                $query->where('settled', 0);
            })
            ->whereHas('membership', function ($query) use ($fourMonths) {
                $query->where('member_status_id', '!=', 8);
            })
            ->with(['loan', 'membership', 'absentSettlement'])
            ->paginate(10);

        return view('loans.absent-settlement', compact('loans'));
    }
    public function editSettlement($id)
    {
        $loan = LoanApplication::with('loan', 'directSettlement', 'membership', 'repayment')->findOrFail($id);
//        $users = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();
        $rejectReasons = RejectReason::all();

        $monthlyCapital = $loan->monthly_capital_portion;
        if ($loan->approved_date=='2000-01-01'){
            $remainingLoan = $loan->loan->total_capital;
        } else {
            $remainingLoan = $loan->approved_amount;
        }
        $repaymentSchedule = [];

        $currentMonth = (int) date('m', strtotime($loan->approved_date));
        $currentDate = (int) date('d', strtotime($loan->approved_date));

        if ($currentMonth == 12){
            if ($currentDate < 15) {
                $month = 1;
                $currentYear = (int) date('Y', strtotime($loan->approved_date)) + 1;
            } else {
                $month = 2;
                $currentYear = (int) date('Y', strtotime($loan->approved_date)) + 1;
            }

        } else {
            if ($currentDate < 15) {
                $month = $currentMonth + 1;
                $currentYear = (int) date('Y', strtotime($loan->approved_date));
            } else {
                $month = $currentMonth + 2;
                $currentYear = (int) date('Y', strtotime($loan->approved_date));
            }

        }

        $nextDueDate = date('Y-m-d', strtotime("$currentYear-$month-21"));

        for ($installment = 1; $installment <= $loan->no_of_installments; $installment++) {
            $monthlyInterest = ($remainingLoan * $loan->product->interest_rate) / (1200);

            $totalInstallment = $monthlyCapital + $monthlyInterest;

            if ($installment == 1){
                $totalInstallment = $monthlyCapital + $monthlyInterest + $loan->arrest_interest;
            }
            $repaymentDetails = [
                'installment_number' => $installment,
                'due_date' => $nextDueDate,
                'monthly_capital' => $monthlyCapital,
                'monthly_interest' => $monthlyInterest,
                'to_recover' => $remainingLoan,
                'total_installment' => $totalInstallment,
                'repayment_data' => null,
            ];

            $repaymentRecord = $loan->repayment->where('payment_no', $installment)->first();

            if ($repaymentRecord) {
                $paymentDateTime = \DateTime::createFromFormat('Y-m-d', $repaymentRecord->payment_date);
                $nextDueDateTime = \DateTime::createFromFormat('Y-m-d', $nextDueDate);

                $interval = $paymentDateTime->diff($nextDueDateTime);
                $differenceInMonths = $interval->format('%m');

                if ($differenceInMonths > 0){
                    $adjustDate = $nextDueDate;
                    for ($i = 0; $i < $differenceInMonths; $i++) {

                        $adjustedRepaymentDetails = [
                            'installment_number' => $installment,
                            'due_date' => $adjustDate,
                            'monthly_capital' => $monthlyCapital,
                            'monthly_interest' => $monthlyInterest,
                            'to_recover' => $remainingLoan,
                            'total_installment' => $totalInstallment,
                            'repayment_data' => [
                                'payment_no' => $repaymentRecord->payment_no,
                                'payment_date' => null,
                                'capital_received' => null,
                                'interest_received' => null,
                            ],
                        ];
                        $adjustDate = date('Y-m-21', strtotime("+1 month", strtotime($adjustDate)));

                        $repaymentSchedule[] = $adjustedRepaymentDetails;
                    }
                    $nextDueDate = date('Y-m-21', strtotime("+$differenceInMonths month", strtotime($nextDueDate)));

                    $differenceInMonths = 0;
                    $repaymentDetails = [
                        'installment_number' => $installment,
                        'due_date' => $adjustDate,
                        'monthly_capital' => $monthlyCapital,
                        'monthly_interest' => $monthlyInterest,
                        'to_recover' => $remainingLoan,
                        'total_installment' => $totalInstallment,
                        'repayment_data' => [
                            'payment_no' => $repaymentRecord->payment_no,
                            'payment_date' => $repaymentRecord->payment_date,
                            'capital_received' => $repaymentRecord->capital_received,
                            'interest_received' => $repaymentRecord->interest_received,
                        ],
                    ];

                    $repaymentSchedule[] = $repaymentDetails;

                } else {

                    $repaymentDetails['repayment_data'] = [
                        'payment_no' => $repaymentRecord->payment_no,
                        'payment_date' => $repaymentRecord->payment_date,
                        'capital_received' => $repaymentRecord->capital_received,
                        'interest_received' => $repaymentRecord->interest_received,
                    ];
                    $repaymentSchedule[] = $repaymentDetails;
                }
            } else {
                $repaymentSchedule[] = $repaymentDetails;
            }

            $nextDueDate = date('Y-m-21', strtotime('+1 month', strtotime($nextDueDate)));

            $remainingLoan -= $monthlyCapital;
        }

        $lastPaymentDate = $loan->repayment->max('payment_date');

        $approvedDate = new DateTime($loan->approved_date);
        $endOfMonth = clone $approvedDate;
        $endOfMonth->modify('last day of this month');
        $endOfDay = $endOfMonth->setTime(23, 59, 59);

        $dateDifference = $approvedDate->diff($endOfDay);

        $arrearsDays = $dateDifference->days;

//        $arrearsInterest = ($loan->approved_amount * ($loan->product->interest_rate/100) * $arrearsDays)/360;

        return view('loans.settlement',compact('loan', 'usersForward', 'usersReject',
            'rejectReasons', 'repaymentSchedule', 'lastPaymentDate'));
    }
    public function absentSettlementView($id)
    {
        $loan = LoanApplication::with('loan', 'directSettlement', 'membership')->findOrFail($id);
//        $users = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();
        $rejectReasons = RejectReason::all();

        $transactionController = new MembershipController();
        $result = $transactionController->show($loan->member_id);
        $fundBalance = $result['fundBalance'];

        $approvedDate = new DateTime($loan->approved_date);
        $endOfMonth = clone $approvedDate;
        $endOfMonth->modify('last day of this month');
        $endOfDay = $endOfMonth->setTime(23, 59, 59);

        $dateDifference = $approvedDate->diff($endOfDay);

        $arrearsDays = $dateDifference->days;


        return view('loans.absent-settlement-view',compact('loan', 'usersForward',
            'usersReject', 'rejectReasons', 'fundBalance'));
    }
    public function showPDF($id)
    {
        $loan = LoanApplication::with('loan', 'directSettlement', 'membership')->findOrFail($id);

        return view('reports.direct-settlement',compact('loan'));

    }
    public function settlementPDF(Request $request, $id)
    {
        $loan = LoanApplication::with('loan', 'directSettlement', 'membership')->findOrFail($id);
        if ($loan->directSettlement){
            view()->share('loan',$loan);
            if ($request->has('download')) {
                $pdf = PDF::loadView('reports.direct-settlement');
                $pdf->setOptions(['footer-html' => view('reports.direct-settlement', ['pdf' => $pdf])->render()]);
                return $pdf->download('direct_settlement.pdf');
            }

            return view('reports.direct-settlement',compact('loan'));
        } else{
            return redirect()->route('loan.editSettlement', $id)->with('error', 'Direct Settlement data not found!');
        }

    }
    public function loanVoucher(Request $request, $id)
    {
        $loan = LoanApplication::with('loan', 'membership')->findOrFail($id);
        if ($loan){
            view()->share('loan',$loan);
            if ($request->has('download')) {
                $pdfFileName = 'loan-voucher-' . $loan->application_reg_no . '.pdf';
                $pdf = PDF::loadView('reports.loan-voucher', compact('loan'));
                $pdf->setOptions(['footer-html' => view('reports.loan-voucher', ['pdf' => $pdf])->render()]);
                return $pdf->download($pdfFileName);
            }
            return view('reports.loan-voucher',compact('loan'));
        } else{
            return redirect()->route('loan.view', $id)->with('error', 'Loan data cannot found!');
        }

    }
    public function updateSettlement(Request $request, $id)
    {
        $loan = LoanApplication::with('loan', 'directSettlement')->find($id);

        $action = $request->input('settlement');
        if ($action === 'forward') {

            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            $loan->directSettlement->reject_reason_id = null;
            $loan->directSettlement->reject_date = null;
            $loan->directSettlement->reject_level = '';
            $loan->directSettlement->approved = 0;
            $loan->directSettlement->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded';
            DirectSettlementAssign::create($validatedAssign);

            return redirect()->route('loan.indexSettlement')->with('success', 'Settlement application forwarded');

        }elseif ($action === 'send') {
            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            $validatedAssign = $request->validate([
                'application_reg_no' => 'unique:direct_settlement,direct_settlement_id',
            ]);

            DirectSettlment::create([
                'direct_settlement_id' => $validatedAssign['application_reg_no'],
                'settlement_amount' => str_replace(',', '', $request->settlement_amount),
                'arrest_interest' => str_replace(',', '', $request->arrest_interest),
                'loan_due_cap' =>  str_replace(',', '', $request->loan_due_cap),
                'direct_settlement_voucher_no' => $request->input('direct_settlement_voucher_no'),
                'ref_no' => $request->input('ref_no'),
                'receipt_no' => $request->input('receipt_no'),
                'settlement_type_id' => 1,
                'payment_mode_id' => 2,
                'status' => 5070,
                'extra_id' => 1,
                'approved' => 0,
                'settlement_date' => now(),
                'currentuser' => Auth::user()->name,
                'created_system' => 'AFMS',
            ]);

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded';
            DirectSettlementAssign::create($validatedAssign);

            return redirect()->route('loan.indexSettlement')->with('success', 'Settlement application sent to approval');


        }elseif ($action === 'approve') {

            if (!$this->authenticateApi()) {
                return response()->json(['error' => 'Failed to authenticate with external API'], 500);
            }
            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->total_recovered_capital += $request->input('settlement_amount');
            $loan->loan->total_recovered_interest += $request->input('arrest_interest');
            if ($loan->loan->total_capital == $loan->loan->total_recovered_capital){
                $loan->loan->settled = 1;
            }else{
                $loan->loan->settled = 0;
            }
            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            $loan->directSettlement->reject_reason_id = null;
            $loan->directSettlement->reject_date = null;
            $loan->directSettlement->reject_level = '';
            $loan->directSettlement->approved = 1;
            $loan->directSettlement->save();

            $payloads = [[
                "cashbookId" => 'CB080',
                "credit" => 0,
                "debit" => $loan->directSettlement->loan_due_cap ?? 0,
                "transactionDate" => now()->toIso8601String(),
                "customer" => $loan->membership->regimental_number . '-' . $loan->membership->enumber ?? '000000000',
                "description" => $loan->membership->name ?? '',
                "reference" => 'Direct Settlement capital',
                "comments" => 'Direct Settlement for ' . $loan->application_reg_no,
                'gl' => false,
                'ar' => true,
            ],
                [
                "cashbookId" => 'CB079',
                "credit" => 0,
                "debit" => $loan->directSettlement->arrest_interest ?? 0,
                "transactionDate" => now()->toIso8601String(),
                "customer" => $loan->membership->regimental_number . '-' . $loan->membership->enumber ?? '000000000',
                "description" => $loan->membership->name ?? '',
                "reference" => 'Direct Settlement interest',
                "comments" => 'Direct Settlement for ' . $loan->application_reg_no,
                'gl' => false,
                'ar' => true,
            ]];

            DirectSettlementAssign::create([
                'loan_id' => $loan->application_reg_no,
                'fwd_to' => 0,
                'fwd_to_reason' => 'Nothing to Process',
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Settled',
            ]);
            $response = $this->sendCashBookUpdate($payloads);

            // Handle failures with detailed messages
//            if (!$response['success']) {
//                $failedApiCount = count($response['failedIndexes'] ?? []);
//                $failures = $response['failures'] ?? [];  // array with ['index' => int, 'reason' => string]
//
//                foreach ($failures as $failure) {
//                    $failedIndex = $failure['index'] ?? null;
//                    $reason = $failure['reason'] ?? 'Unknown API failure';
//
//                    if ($failedIndex !== null && isset($loanMap[$failedIndex])) {
//                        $loan = $loanMap[$failedIndex];
//                        FailureLoanApi::create([
//                            'enumber' => $loan->membership->enumber,
//                            'amount' => $loan->approved_amount,
//                            'reference' => 'Batch API failed - ' . $loan->loan_id,
//                            'reason' => $reason,
//                        ]);
//                    }
//                }
//            }

            return redirect()->route('loan.indexSettlement')->with('success', 'Totally settled the loan');


        }elseif ($action === 'reject') {
            $loan->directSettlement->reject_reason_id = $request->input('fwd_to_reason');
            $loan->directSettlement->reject_date = now();
            $loan->directSettlement->reject_level = Auth::user()->name;
            $loan->directSettlement->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            DirectSettlementAssign::create($validatedAssign);

            return redirect()->route('loan.indexSettlement')->with('success', 'Settlement application rejected');

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function absentSettlementUpdate(Request $request, $id)
    {
        $loan = LoanApplication::with('loan', 'absentSettlement')->find($id);
        $action = $request->input('settlement');

        if ($action === 'forward') {

            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            $loan->absentSettlement->reject_reason_id = null;
            $loan->absentSettlement->reject_date = null;
            $loan->absentSettlement->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded';
            AbsentSettlementAssign::create($validatedAssign);

            return redirect()->route('absent-settlement')->with('success', 'Settlement application forwarded');

        }elseif ($action === 'send') {
            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            AbsentSettlement::create([
                'settlement_id' => $loan->application_reg_no,
                'membership_id' => $loan->member_id,
                'settlement_amount' => str_replace(',', '', $request->settlement_amount),
                'arrest_interest' => str_replace(',', '', $request->arrest_interest),
                'loan_due_cap' =>  str_replace(',', '', $request->loan_due_cap),
                'fund_balance' =>  str_replace(',', '', $request->fund_balance),
                'processing' => 1,
                'settlement_date' => now(),
                'created_system' => 'AFMS',
            ]);

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded';
            AbsentSettlementAssign::create($validatedAssign);

            return redirect()->route('absent-settlement')->with('success', 'Settlement application sent to approval');

        }elseif ($action === 'approve') {
            $loan->created_system = 'AFMS-Update';
            $loan->save();

            $loan->loan->total_recovered_capital += str_replace(',', '', $request->settlement_amount);
            $loan->loan->total_recovered_interest += str_replace(',', '', $request->arrest_interest);
            if ($loan->loan->total_capital == $loan->loan->total_recovered_capital){
                $loan->loan->settled = 1;
            }else{
                $loan->loan->settled = 0;
            }
            $loan->loan->currentuser = Auth::user()->name;
            $loan->loan->created_system = 'AFMS-Update';
            $loan->loan->save();

            $loan->absentSettlement->reject_reason_id = null;
            $loan->absentSettlement->reject_date = null;
            $loan->absentSettlement->processing = 0;
            $loan->absentSettlement->save();

            PartialWithdrawalLog::create([
                'withdrawal_id' => $loan->application_reg_no,
                'version' => 0,
                'direct_loan_amount' =>  str_replace(',', '', $request->loan_due_cap) + str_replace(',', '', $request->arrest_interest),
                'is_batch_to_do' => 1,
                'membership_id' => $loan->member_id,
                'withdrawal_amount' =>  str_replace(',', '', $request->settlement_amount),
                'currentuser' => Auth::user()->name,
            ]);

            AbsentSettlementAssign::create([
                'loan_id' => $loan->application_reg_no,
                'fwd_to' => 0,
                'fwd_to_reason' => 'Nothing to Process',
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Settled',
            ]);

            return redirect()->route('absent-settlement')->with('success', 'Totally settled the loan');

        }elseif ($action === 'reject') {
            $loan->absentSettlement->reject_reason_id = $request->input('fwd_to_reason');
            $loan->absentSettlement->reject_date = now();
            $loan->absentSettlement->processing = 2;
            $loan->absentSettlement->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['loan_id'] = $loan->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            AbsentSettlementAssign::create($validatedAssign);

            return redirect()->route('absent-settlement')->with('success', 'Settlement application rejected');

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function destroySettlement($id){
        $loan = LoanApplication::with('directSettlement')->find($id);
        $loan->directSettlement->delete();

        return redirect()->route('loan.indexSettlement')
            ->with('success', 'Settlement application deleted');

    }
    public function destroy($id){
        $loan = LoanApplication::with('loan')->find($id);
        $loan->delete();

        return redirect()->route('loan.index')
            ->with('success', 'Loan application deleted successfully');
    }
}
