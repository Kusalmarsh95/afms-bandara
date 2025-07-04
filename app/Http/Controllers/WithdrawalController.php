<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\DirectSettlment;
use App\Models\FailedAdjustmentApi;
use App\Models\FailedWithdrawalApi;
use App\Models\FullWithdrawal;
use App\Models\FullWithdrawalApplication;
use App\Models\FullWithdrawalLog;
use App\Models\LoanApplication;
use App\Models\Membership;
use App\Models\NomineeBank;
use App\Models\PartialWithdrawal;
use App\Models\PartialWithdrawalApplication;
use App\Models\PartialWithdrawalLog;
use App\Models\RejectReason;
use App\Models\Suwasahana;
use App\Models\User;
use App\Models\WithdrawalAssign;
use App\Models\WithdrawalProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use PDF;

class WithdrawalController extends Controller
{
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
    public function indexPartial(){
        $partialWithdrawals = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason',])
            ->where('processing', '!=', 0)
            ->get();

        foreach ($partialWithdrawals as $partialWithdrawal) {
            $latestAssignment = $partialWithdrawal->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $partialWithdrawal->userName = $user ? $user->name : '-';
                $partialWithdrawal->reason = $reason ?: '-';
            } else {
                $partialWithdrawal->userName = '-';
                $partialWithdrawal->reason = '-';
            }
        }

        return view('withdrawals.index-partial',compact('partialWithdrawals'));

    }
    public function indexPartialApproved(){
        $partialWithdrawals = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason',])
            ->where('processing', '>', 3)
            ->orWhere('is_banked', '>=', 0)->get();

        $sendToBankCount = PartialWithdrawalApplication::where('processing', '=', 5)->count();
        $payCount = PartialWithdrawalApplication::where('processing', '=', 6)->count();
        foreach ($partialWithdrawals as $partialWithdrawal) {
            $latestAssignment = $partialWithdrawal->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $partialWithdrawal->userName = $user ? $user->name : '-';
                $partialWithdrawal->reason = $reason ?: '-';
            } else {
                $partialWithdrawal->userName = '-';
                $partialWithdrawal->reason = '-';
            }
        }

        return view('withdrawals.index-partial-approved',compact('partialWithdrawals',
        'sendToBankCount', 'payCount'));

    }
    public function indexfullApproved(){
        $fullWithdrawals = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason',])
            ->where('processing', '>', 3)
            ->orWhere('is_banked', '>=', 0)->get();

        $sendToBankCount = FullWithdrawalApplication::where('processing', '=', 5)->count();
        $payCount = FullWithdrawalApplication::where('processing', '=', 6)->count();
        foreach ($fullWithdrawals as $fullWithdrawal) {
            $latestAssignment = $fullWithdrawal->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $fullWithdrawal->userName = $user ? $user->name : '-';
                $fullWithdrawal->reason = $reason ?: '-';
            } else {
                $fullWithdrawal->userName = '-';
                $fullWithdrawal->reason = '-';
            }
        }

        return view('withdrawals.index-full-approved',compact('fullWithdrawals',
            'sendToBankCount', 'payCount'));

    }

    public function viewPartial($id){
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason', 'assigns'])
        ->find($id);
//        $users = User::all();
        $currentUser = auth()->user();
        $userRole = $currentUser->roles()->first()->name;
//        $rolesToForward = $currentUser->forward_roles ?? [];
        $rolesToForward = ($userRole == 'Ledger Section OC') ? ['80 Payment Section Cleark'] : $currentUser->forward_roles ?? [];

        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();
        $rejectReasons = RejectReason::all();

        if ($partialWithdrawal->withdrawal->purpose==1) {
            $approvedAmount = floor($partialWithdrawal->withdrawal->eligible_amount / 100) * 100 +
                ($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest);
        }else if ($partialWithdrawal->withdrawal->purpose==2){
            $approvedAmount = $partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas;
        }else{
            $approvedAmount = $partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest;
        }

        if ($approvedAmount > $partialWithdrawal->withdrawal->requested_amount & $partialWithdrawal->withdrawal->requested_amount!=0){
            $approvedAmount = $partialWithdrawal->withdrawal->requested_amount;
        }

        return view('withdrawals.view-partial',compact('partialWithdrawal', 'usersForward',
            'usersReject', 'rejectReasons', 'approvedAmount'));
    }
    public function partialVoucher(Request $request, $id)
    {
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal'])
            ->find($id);
        if ($partialWithdrawal->withdrawal->purpose==1 | $partialWithdrawal->withdrawal->purpose==1) {
            $partialWithdrawal->approvedAmount = floor($partialWithdrawal->withdrawal->eligible_amount / 100) * 100 +
                ($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest);
        }else if ($partialWithdrawal->withdrawal->purpose==2){
            $partialWithdrawal->approvedAmount = $partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas;
        }else{
            $partialWithdrawal->approvedAmount = $partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest;
        }

        if ($partialWithdrawal->approvedAmount > $partialWithdrawal->withdrawal->requested_amount & $partialWithdrawal->withdrawal->requested_amount!=0){
            $partialWithdrawal->approvedAmount = $partialWithdrawal->withdrawal->requested_amount;
        }

        if ($partialWithdrawal){
            view()->share('partialWithdrawal',$partialWithdrawal);
            if ($request->has('download')) {
                $pdfFileName = 'withdrawal-voucher-' . $partialWithdrawal->application_reg_no . '.pdf';
                $pdf = PDF::loadView('reports.withdrawal-voucher', compact('partialWithdrawal'));
                $pdf->setOptions(['footer-html' => view('reports.withdrawal-voucher', ['pdf' => $pdf])->render()]);
                return $pdf->download($pdfFileName);
            }
            return view('reports.withdrawal-voucher',compact('partialWithdrawal'));
        } else{
            return redirect()->route('partial-view', $id)->with('error', 'Withdrawal data cannot found!');
        }

    }
    public function fullVoucher(Request $request, $id)
    {
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason', 'assigns'])
            ->find($id);

        $fullWithdrawal->approvedAmount = $fullWithdrawal->fullWithdrawal->fund_balance - ($fullWithdrawal->fullWithdrawal->other_deduction) ;

        if ($fullWithdrawal){
            view()->share('fullWithdrawal',$fullWithdrawal);
            if ($request->has('download')) {
                $pdfFileName = 'full-withdrawal-voucher-' . $fullWithdrawal->application_reg_no . '.pdf';
                $pdf = PDF::loadView('reports.full-withdrawal-voucher', compact('fullWithdrawal'));
                $pdf->setOptions(['footer-html' => view('reports.full-withdrawal-voucher', ['pdf' => $pdf])->render()]);
                return $pdf->download($pdfFileName);
            }
            return view('reports.full-withdrawal-voucher',compact('fullWithdrawal'));
        } else{
            return redirect()->route('full-view', $id)->with('error', 'Withdrawal data cannot found!');
        }
    }
    public function approveReject(Request $request, $id)
    {
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason', 'assigns'])
            ->find($id);
        $action = $request->input('approval');

        if ($action === 'forward') {
            $partialWithdrawal->processing = 1;
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->approved_amount = str_replace(',', '', $request->approved_amount);
            $partialWithdrawal->withdrawal->requested_amount = str_replace(',', '', $request->requested_amount);
            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for further proceedings.';
            WithdrawalAssign::create($validatedAssign);


            return redirect()->route('withdrawals.indexPartial')->with('success', 'Application forwarded to process..');

        } elseif ($action === 'process') {
            $partialWithdrawal->processing = 3;
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->requested_amount = str_replace(',', '', $request->requested_amount);
            $partialWithdrawal->withdrawal->approved_amount = str_replace(',', '', $request->approved_amount);
            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and successfully registered for the process.';

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')->with(['success' => 'Successfully registered..']);

        }elseif ($action === 'approve') {
            $partialWithdrawal->processing = 4;
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->approved_amount = str_replace(',', '', $request->approved_amount);
            $partialWithdrawal->withdrawal->requested_amount = str_replace(',', '', $request->requested_amount);
            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->save();


            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and approved for the payment';

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')->with(['success' => 'Successfully approved..']);


        }elseif ($action === 'reject') {
            $partialWithdrawal->processing = 2;
            $partialWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $partialWithdrawal->reject_date = now();
            $partialWithdrawal->reject_level = Auth::user()->name;
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')->with(['success' => 'Rejected the application']);


        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function approvedPartial($id){
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason', 'assigns'])
            ->find($id);
//        $users = User::all();.
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

        if ($partialWithdrawal->withdrawal->purpose==1 | $partialWithdrawal->withdrawal->purpose==1) {
            $withdrawAmount = $partialWithdrawal->withdrawal->approved_amount -
                ($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest);
        }else if ($partialWithdrawal->withdrawal->purpose==2){
            $withdrawAmount = $partialWithdrawal->withdrawal->approved_amount -
                ($partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas);
        }else{
            $withdrawAmount = $partialWithdrawal->withdrawal->approved_amount;
        }


        return view('withdrawals.disburse-partial',compact('partialWithdrawal', 'usersForward',
            'usersReject','rejectReasons', 'withdrawAmount'));
    }
    public function disbursePartial(Request $request, $id){

        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal', 'rejectReason', 'assigns'])
            ->find($id);

        $loanApplication = LoanApplication::with('loan', 'directSettlement')->where('member_id', $partialWithdrawal->membership->id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $partialWithdrawal->membership->id)->latest('Issue_Date')->first();

        $action = $request->input('approval');

        if ($action === 'forward') {
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $voucher_no = $request->validate([
                'voucher_no' => 'required',
            ]);
            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->voucher_no = $voucher_no['voucher_no'];
            $partialWithdrawal->withdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $partialWithdrawal->withdrawal->total_withdraw_amount = str_replace(',', '', $request->total_withdraw_amount);
            $partialWithdrawal->withdrawal->currentuser = Auth::user()->name;

            $partialWithdrawal->withdrawal->save();

            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for disburse.';
            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')->with('success', 'Application forwarded to process');

        } elseif ($action === 'send') {
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->processing = 5;
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $voucher_no = $request->validate([
                'voucher_no' => 'required',
            ]);
            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->voucher_no = $voucher_no['voucher_no'];
            $partialWithdrawal->withdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $partialWithdrawal->withdrawal->total_withdraw_amount = str_replace(',', '', $request->total_withdraw_amount);
            $partialWithdrawal->withdrawal->currentuser = Auth::user()->name;

            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for disburse.';
            WithdrawalAssign::create($validatedAssign);


            return redirect()->route('withdrawals.indexPartial')->with('success', 'Application forwarded to disburse..');

        } elseif ($action === 'disburse') {
//            dd('disburse');
            $partialWithdrawal->reject_reason_id = 0;
            $partialWithdrawal->reject_date = null;
            $partialWithdrawal->reject_level = '';
            $partialWithdrawal->status_id = 614;
            $partialWithdrawal->processing = 0;
            $partialWithdrawal->is_banked = 0;
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->reject_reason_id = 0;
            $partialWithdrawal->withdrawal->voucher_no = $request->input('voucher_no');
            $partialWithdrawal->withdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $partialWithdrawal->withdrawal->total_withdraw_amount = str_replace(',', '', $request->total_withdraw_amount);
            $partialWithdrawal->withdrawal->paid_date = now()->format('Y-m-d');

            $partialWithdrawal->withdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->withdrawal->save();

            PartialWithdrawalLog::create([
                'withdrawal_id' => $request->application_reg_no,
                'version' => 0,
                'direct_loan_amount' =>  $request->input('loan_due_cap') + $request->input('arrest_interest'),
                'is_batch_to_do' => 1,
                'membership_id' => $request->input('membership_id'),
                'withdrawal_amount' =>  $request->input('voucher_amount'),
                'currentuser' => Auth::user()->name,
            ]);

            if($partialWithdrawal->withdrawal->purpose==2){
//                dd($suwasahana);
                $suwasahana->created_system = 'AFMS';
                $suwasahana->settled = 1;
                $suwasahana->settled_type = '80% Withdrawal';
                $suwasahana->settled_date = now()->format('Y-m-d');
                $suwasahana->total_recovered_capital += $partialWithdrawal->withdrawal->suwasahana_amount;
                $suwasahana->total_recovered_interest += $partialWithdrawal->withdrawal->suwasahana_arreas;

                $suwasahana->save();

            } else {
                if($loanApplication){
                    $loanApplication->loan->settled = 1;
                    $loanApplication->loan->total_recovered_capital += $request->input('loan_due_cap');
                    $loanApplication->loan->currentuser = Auth::user()->name;
                    $loanApplication->loan->save();

                    DirectSettlment::create([
                        'direct_settlement_id' => $loanApplication->application_reg_no,
                        'arrest_interest' => $request->input('arrest_interest'),
                        'loan_due_cap' =>  $request->input('loan_due_cap'),
                        'direct_settlement_voucher_no' => $request->input('voucher_no'),
                        'withdrawal_application_id' => $partialWithdrawal->application_reg_no,
                        'settlement_type_id' => 2,
                        'payment_mode_id' => 2,
                        'status' => 5070,
                        'extra_id' => 1,
                        'receipt_no' => 16,
                        'settlement_date' => now(),
                        'approved' => 1,
                        'settlement_amount' => $request->input('loan_due_cap') + $request->input('arrest_interest'),
                        'currentuser' => Auth::user()->name,
                        'created_system' => 'AFMS',
                    ]);
                }
            }

            WithdrawalAssign::create([
                'withdrawal_id' => $partialWithdrawal->application_reg_no,
                'fwd_to' => 0,
                'fwd_to_reason' => 'Nothing to Process',
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Paid',
            ]);

            return redirect()->route('withdrawals.indexPartial')->with(['success' => 'Successfully Disbursed..']);

        } elseif ($action === 'reject') {
            $partialWithdrawal->processing = 2;
            $partialWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $partialWithdrawal->reject_date = now();
            $partialWithdrawal->reject_level = Auth::user()->name;
            $partialWithdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->save();

            $partialWithdrawal->withdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $partialWithdrawal->withdrawal->currentuser = Auth::user()->name;
            $partialWithdrawal->withdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $partialWithdrawal->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')->with(['success' => 'Rejected the application..']);


        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function partialToBulk(Request $request)
    {
        $request->validate([
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'integer|exists:withdrawal_application,id',
        ]);

        $partials = PartialWithdrawalApplication::with('withdrawal')->whereIn('id', $request->withdrawal_ids)->get();

        $rolesToForward = ['Account OC', 'Account Section OC'];
        $user = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })
            ->latest()
            ->first();

        foreach ($partials as $partial) {
            if ($partial->withdrawal->purpose==0 | $partial->withdrawal->purpose==1) {
                $withdrawAmount = $partial->withdrawal->approved_amount -
                    ($partial->withdrawal->loan_due_cap + $partial->withdrawal->arrest_interest);
            }else if ($partial->withdrawal->purpose==2){
                $withdrawAmount = $partial->withdrawal->approved_amount -
                    ($partial->withdrawal->suwasahana_amount + $partial->withdrawal->suwasahana_arreas);
            }else{
                $withdrawAmount = $partial->withdrawal->approved_amount;
            }
            $partial->processing = 5;
            $partial->is_banked = 0;
            $partial->currentuser = Auth::user()->name;
            $partial->save();

            $partial->withdrawal->total_withdraw_amount = $withdrawAmount;
            $partial->withdrawal->currentuser = Auth::user()->name;
            $partial->withdrawal->save();

            WithdrawalAssign::create([
                'withdrawal_id' => $partial->application_reg_no,
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Checked and forwarded to process',
                'fwd_to' => $user->id,
                'fwd_to_reason' => 'Send to Approval',
            ]);
        }

        return response()->json(['message' => 'Processing status updated successfully.']);
    }
    public function releasePartialPayment(Request $request){
        $partialWithdrawals = PartialWithdrawalApplication::with(['membership:id,enumber,regimental_number,name', 'withdrawal'])
            ->where('processing','5')
            ->where('is_banked','=','0')
            ->get();
        $arPayloads=[];
        $cashbookPayloads=[];
        $action = $request->approval;
        $voucherId = Carbon::now()->format('Ymd');

        if ($action === 'pay') {
            if (!$this->authenticateApi()) {
                return response()->json(['error' => 'Failed to authenticate with external API'], 500);
            }
            foreach ($partialWithdrawals as $partialWithdrawal) {
                $suwasahana = Suwasahana::where('member_id', $partialWithdrawal->member_id)
                    ->where('settled', 0)
                    ->latest('Issue_Date')->first();
                $loanApplication = LoanApplication::with('loan')
                    ->where('member_id', $partialWithdrawal->membership->id)
                    ->whereHas('loan', function ($query) {
                        $query->where('settled', 0);
                    })
                    ->latest('registered_date')
                    ->first();

                if($partialWithdrawal->withdrawal->purpose==2){
                    $suwasahana->created_system = 'AFMS';
                    $suwasahana->settled = 1;
                    $suwasahana->settled_type = '80% Withdrawal';
                    $suwasahana->settled_date = now()->format('Y-m-d');
                    $suwasahana->total_recovered_capital += $partialWithdrawal->withdrawal->suwasahana_amount;
                    $suwasahana->total_recovered_interest += $partialWithdrawal->withdrawal->suwasahana_arreas;
                    $suwasahana->save();

                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
//                        "credit" => 0,
//                        "debit" => ($partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas) ?? 0,
                        "amount" => ($partialWithdrawal->withdrawal->suwasahana_amount + $partialWithdrawal->withdrawal->suwasahana_arreas) ?? 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                        "description" => 'Suwasahana recovery from Fund',
                        "reference" => 'Suwasahana recovery from Fund',
                        "comments" => 'Suwasahana recovery from Fund',
                        "transactioncCodeID" => 'LoanRecM',
                        "taxTypeID" => 1,
                        "gl" => false,
                        "ar" => true,
                    ];
                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
                        "amount" => $partialWithdrawal->withdrawal->suwasahana_amount ?? 0,
//                        "credit" => $partialWithdrawal->withdrawal->suwasahana_amount ?? 0,
//                        "debit" => 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => '7500>7530',
                        "description" => 'Suwasahana loan recovery from Fund',
                        "reference" => 'Suwasahana loan recovery',
//                        "reference" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                        "comments" => 'Suwasahana loan recovery from Fund',
                        "transactioncCodeID" => 'JNL',
                        "taxTypeID" => 1,
                        "gl" => true,
                        "ar" => false,
                    ];
                    if ($partialWithdrawal->withdrawal->suwasahana_arreas>0){
                        $arPayloads[] = [
                            "aRbatchId" => 'ARB008',
                            "amount" => $partialWithdrawal->withdrawal->suwasahana_arreas ?? 0,
//                        "credit" => $partialWithdrawal->withdrawal->suwasahana_arreas ?? 0,
//                        "debit" => 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => '1000>1012',
                            "description" => 'Suwasahana interest recovery from Fund',
//                        "reference" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                            "reference" => 'Suwasahana loan recovery',
                            "comments" => 'Suwasahana interest recovery from Fund',
                            "transactioncCodeID" => 'JNL',
                            "taxTypeID" => 1,
                            "gl" => true,
                            "ar" => false,
                        ];
                    }
                } else {
                    if($loanApplication){
                        $loanApplication->loan->settled = 1;
                        $loanApplication->loan->total_recovered_capital += $partialWithdrawal->withdrawal->loan_due_cap ?? 0;
                        $loanApplication->loan->total_recovered_interest += $partialWithdrawal->withdrawal->arrest_interest ?? 0;
                        $loanApplication->loan->currentuser = Auth::user()->name;
                        $loanApplication->loan->save();
                        if ($partialWithdrawal->withdrawal->purpose==1){
                            $cashbookPayloads[] = [
                                'cashbookId' => 'CB076',
                                'credit' => $partialWithdrawal->withdrawal->total_withdraw_amount ?? 0,
                                'debit' => 0,
                                'customer' => $partialWithdrawal->membership->regimental_number . '-' . ($partialWithdrawal->membership->enumber ?? '000000'),
                                'transactionDate' => now()->toIso8601String(),
                                'description' => 'Withdrawal transaction',
                                'comments' => 'Withdrawal',
                                'reference' => $partialWithdrawal->membership->name,
                                'gl' => false,
                                'ar' => true,
                            ];
                        }

                        $arPayloads[] = [
                            "aRbatchId" => 'ARB008',
//                            "credit" => 0,
//                            "debit" => ($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest) ?? 0,
                            "amount" => ($partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest) ?? 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                            "description" => 'ABF Loan recovery from Fund',
                            "reference" => 'ABF Loan recovery from Fund',
                            "comments" => 'ABF Loan recovery from Fund',
                            "transactioncCodeID" => 'LoanRecM',
                            "taxTypeID" => 1,
                            "gl" => false,
                            "ar" => true,
                        ];
                        $arPayloads[] = [
                            "aRbatchId" => 'ARB008',
                            "amount" => $partialWithdrawal->withdrawal->loan_due_cap ?? 0,
//                            "credit" => $partialWithdrawal->withdrawal->loan_due_cap ?? 0,
//                            "debit" => 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => '7300',
                            "description" => 'ABF loan recovery from Fund',
                            "reference" => 'ABF loan recovery',
//                            "reference" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                            "comments" => 'ABF loan recovery from Fund',
                            "transactioncCodeID" => 'JNL',
                            "taxTypeID" => 1,
                            "gl" => true,
                            "ar" => false,
                        ];
                        if($partialWithdrawal->withdrawal->arrest_interest>0){
                            $arPayloads[] = [
                                "aRbatchId" => 'ARB008',
                                "amount" => $partialWithdrawal->withdrawal->arrest_interest ?? 0,
//                            "credit" => $partialWithdrawal->withdrawal->arrest_interest ?? 0,
//                            "debit" => 0,
                                "transactionDate" => now()->toIso8601String(),
                                "customer" => '1000>LOAN INTEREST',
                                "description" => 'ABF interest recovery from Fund',
                                "reference" => 'ABF interest recovery',
//                            "reference" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                                "comments" => 'ABF interest recovery from Fund',
                                "transactioncCodeID" => 'JNL',
                                "taxTypeID" => 1,
                                "gl" => true,
                                "ar" => false,
                            ];
                        }

                        DirectSettlment::create([
                            'direct_settlement_id' => $loanApplication->application_reg_no,
                            'arrest_interest' => $partialWithdrawal->withdrawal->arrest_interest,
                            'loan_due_cap' =>  $partialWithdrawal->withdrawal->loan_due_cap,
                            'direct_settlement_voucher_no' => $voucherId,
                            'withdrawal_application_id' => $partialWithdrawal->application_reg_no,
                            'settlement_type_id' => 2,
                            'payment_mode_id' => 2,
                            'status' => 5070,
                            'extra_id' => 1,
                            'receipt_no' => 16,
                            'settlement_date' => now(),
                            'approved' => 1,
                            'settlement_amount' => $partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest,
                            'currentuser' => Auth::user()->name,
                            'created_system' => 'AFMS',
                        ]);
                    } else{
                        $arPayloads[] = [
                            "aRbatchId" => 'ARB013',
                            "amount" => $partialWithdrawal->withdrawal->approved_amount ?? 0,
//                            "credit" => $partialWithdrawal->withdrawal->approved_amount ?? 0,
//                            "debit" => 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => $partialWithdrawal->membership->regimental_number . '-' . $partialWithdrawal->membership->enumber ?? '00000000',
                            "description" => '80% Withdrawal',
                            "reference" => 'Withdrawal application of '. $partialWithdrawal->application_reg_no,
                            "comments" => 'Withdrawal-'. $partialWithdrawal->membership->name,
                            "transactioncCodeID" => 'WW',
                            "taxTypeID" => 1,
                            "gl" => false,
                            "ar" => true,
                        ];
                    }
                }

                $partialWithdrawal->reject_reason_id = 0;
                $partialWithdrawal->processing = 6;
                $partialWithdrawal->reject_date = null;
                $partialWithdrawal->reject_level = '';
                $partialWithdrawal->status_id = 614;
                $partialWithdrawal->is_banked = 0;
                $partialWithdrawal->currentuser = Auth::user()->name;
                $partialWithdrawal->save();

                $partialWithdrawal->withdrawal->reject_reason_id = 0;
                $partialWithdrawal->withdrawal->voucher_no = $voucherId;
                $partialWithdrawal->withdrawal->voucher_amount = $partialWithdrawal->withdrawal->approved_amount;
                $partialWithdrawal->withdrawal->paid_date = now()->format('Y-m-d');

                $partialWithdrawal->withdrawal->currentuser = Auth::user()->name;
                $partialWithdrawal->withdrawal->save();

                PartialWithdrawalLog::create([
                    'withdrawal_id' => $partialWithdrawal->application_reg_no,
                    'version' => 0,
                    'direct_loan_amount' =>  $partialWithdrawal->withdrawal->loan_due_cap + $partialWithdrawal->withdrawal->arrest_interest,
                    'is_batch_to_do' => 1,
                    'membership_id' => $partialWithdrawal->member_id,
                    'withdrawal_amount' =>  $partialWithdrawal->withdrawal->voucher_amount,
                    'currentuser' => Auth::user()->name,
                ]);

                WithdrawalAssign::create([
                    'withdrawal_id' => $partialWithdrawal->application_reg_no,
                    'fwd_to' => 0,
                    'fwd_to_reason' => 'Nothing to Process',
                    'fwd_by' => Auth::user()->id,
                    'fwd_by_reason' => 'Paid',
                ]);

            }

            $this->sendARBatchUpdate($arPayloads);

            $this->sendCashBookUpdate($cashbookPayloads);
//            $apiSuccess = $response['success'];
//            if (!$apiSuccess) {
//                FailedWithdrawalApi::create([
//                    'enumber' => $partialWithdrawal->membership->enumber,
//                    'amount' => $partialWithdrawal->amount,
//                    'reference' => $partialWithdrawal->membership->name,
//                    'reason' => 'Withdrawal failed: ' . $response['message'],
//                ]);
//            }

            return redirect()->back()->with('success', 'Disbursement finalized.');
        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    private function sendARBatchUpdate(array $payloads)
    {
        $now = now();
        $filename = 'ar_batch_update_' . $now->format('Ymd_His') . '_' . uniqid() . '.log';
        $logPath = storage_path('logs/adjustments/' . $filename);

        if (!file_exists(dirname($logPath))) {
            mkdir(dirname($logPath), 0755, true);
        }

        try {
            $response = Http::timeout(90)
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
                    FailedWithdrawalApi::create([
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
                FailedWithdrawalApi::create([
                    'enumber' => explode('-', $payload['customer'])[0] ?? 'N/A',
                    'amount' => $payload['debit'],
                    'reference' => $payload['reference'] ?? '',
                    'reason' => 'Exception: ' . $e->getMessage(),
                ]);
            }
            return false;
        }
    }

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
                'request' => $payload[0]['customer'] ?? 'N/A',
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
                'request' => $payload[0]['customer'] ?? 'N/A',
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

    public function partialBanked(Request $request){
        $partialWithdrawals = PartialWithdrawalApplication::where('processing','6')
            ->where('is_banked','=','0')
            ->get();
        $action = $request->approval;

        if ($action === 'banked') {
            foreach ($partialWithdrawals as $partialWithdrawal) {
                $partialWithdrawal->processing = 0;
                $partialWithdrawal->is_banked = 1;
                $partialWithdrawal->save();
            }

            return redirect()->route('partial.bulk')->with('success', 'Disbursement finalized.');
        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function fullToBulk(Request $request)
    {
        $request->validate([
            'withdrawal_ids' => 'required|array',
            'withdrawal_ids.*' => 'integer|exists:full_withdrawal_application,id',
        ]);

        $fulls = FullWithdrawalApplication::with('fullWithdrawal')
            ->whereIn('id', $request->withdrawal_ids)->get();

        $rolesToForward = ['Account OC', 'Account Section OC'];
        $user = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })
            ->latest()
            ->first();

        foreach ($fulls as $full) {

            $full->processing = 5;
            $full->is_banked = 0;
            $full->currentuser = Auth::user()->name;
            $full->save();

            $full->fullWithdrawal->currentuser = Auth::user()->name;
            $full->fullWithdrawal->save();

            WithdrawalAssign::create([
                'withdrawal_id' => $full->application_reg_no,
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Checked and forwarded to process',
                'fwd_to' => $user->id,
                'fwd_to_reason' => 'Send to Approval',
            ]);
        }

        return response()->json(['message' => 'Processing status updated successfully.']);
    }
    public function releaseFullPayment(Request $request){
        $fullWithdrawals = FullWithdrawalApplication::with(['membership:id,enumber,regimental_number,name', 'fullWithdrawal'])
            ->where('processing','5')
            ->where('is_banked','=','0')
            ->get();

        $cashbookPayloads = [];
        $arPayloads = [];
        $action = $request->approval;
        $voucherId = Carbon::now()->format('Ymd');
        if ($action === 'pay') {
            if (!$this->authenticateApi()) {
                return response()->json(['error' => 'Failed to authenticate with external API'], 500);
            }
            foreach ($fullWithdrawals as $fullWithdrawal) {
                $suwasahana = Suwasahana::where('member_id', $fullWithdrawal->member_id)
                    ->where('settled', 0)
                    ->latest('Issue_Date')->first();
                $loanApplication = LoanApplication::with('loan')
                    ->where('member_id', $fullWithdrawal->member_id)
                    ->whereHas('loan', function ($query) {
                        $query->where('settled', 0);
                    })
                    ->latest('registered_date')->first();

                if($loanApplication){
                    $loanApplication->loan->settled = 1;
                    $loanApplication->loan->total_recovered_capital +=$fullWithdrawal->fullWithdrawal->loan_due_cap;
                    $loanApplication->loan->total_recovered_interest +=$fullWithdrawal->fullWithdrawal->arrest_interest;
                    $loanApplication->loan->currentuser = Auth::user()->name;
                    $loanApplication->loan->save();

                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
//                        "credit" => 0,
//                        "debit" => ($fullWithdrawal->fullWithdrawal->loan_due_cap + $fullWithdrawal->fullWithdrawal->arrest_interest) ?? 0,
                        "amount" => ($fullWithdrawal->fullWithdrawal->loan_due_cap + $fullWithdrawal->fullWithdrawal->arrest_interest) ?? 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                        "description" => 'ABF Loan recovery from Fund',
                        "reference" => 'Full Withdrawal ',
//                        "reference" => 'Full Withdrawal '.$fullWithdrawal->application_reg_no,
                        "comments" => 'ABF Loan recovery from Fund',
                        "transactioncCodeID" => 'LoanRecM',
                        "taxTypeID" => 1,
                        "gl" => false,
                        "ar" => true,
                    ];
                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
                        "amount" => $fullWithdrawal->fullWithdrawal->loan_due_cap ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->loan_due_cap ?? 0,
//                        "debit" => 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => '7300',
                        "description" => 'ABF loan recovery application',
                        "reference" => 'loan recovery',
                        "comments" => 'ABF loan recovery from Full Withdrawal',
                        "transactioncCodeID" => 'JNL',
                        "taxTypeID" => 1,
                        "gl" => true,
                        "ar" => false,
                    ];
                    if ($fullWithdrawal->fullWithdrawal->arrest_interest>0){
                        $arPayloads[] = [
                            "aRbatchId" => 'ARB008',
                            "amount" =>  $fullWithdrawal->fullWithdrawal->arrest_interest ?? 0,
//                        "credit" =>  $fullWithdrawal->fullWithdrawal->arrest_interest ?? 0,
//                        "debit" => 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => '1000>LOAN INTEREST',
                            "description" => 'ABF interest recovery from Fund',
//                        "reference" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                            "reference" => 'loan interest',
                            "comments" => 'ABF interest recovery from Fund',
                            "transactioncCodeID" => 'JNL',
                            "taxTypeID" => 1,
                            "gl" => true,
                            "ar" => false,
                        ];
                    }
                    DirectSettlment::create([
                        'direct_settlement_id' => $loanApplication->application_reg_no,
                        'arrest_interest' => $fullWithdrawal->fullWithdrawal->arrest_interest,
                        'loan_due_cap' =>  $fullWithdrawal->fullWithdrawal->loan_due_cap,
                        'direct_settlement_voucher_no' => $voucherId,
                        'withdrawal_application_id' => $fullWithdrawal->application_reg_no,
                        'settlement_type_id' => 3,
                        'payment_mode_id' => 2,
                        'status' => 5070,
                        'extra_id' => 1,
                        'receipt_no' => 16,
                        'settlement_date' => now(),
                        'approved' => 1,
                        'settlement_amount' => $fullWithdrawal->fullWithdrawal->loan_due_cap + $fullWithdrawal->fullWithdrawal->arrest_interest,
                        'currentuser' => Auth::user()->name,
                        'created_system' => 'AFMS',
                    ]);
                } elseif ($suwasahana) {
                    $suwasahana->created_system = 'AFMS';
                    $suwasahana->settled = 1;
                    $suwasahana->settled_type = 'Full Withdrawal';
                    $suwasahana->settled_date = now()->format('Y-m-d');
                    $suwasahana->total_recovered_capital += $fullWithdrawal->fullWithdrawal->suwasahana_amount;
                    $suwasahana->total_recovered_interest += $fullWithdrawal->fullWithdrawal->suwasahana_arreas;
                    $suwasahana->save();
                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
//                        "credit" => 0,
//                        "debit" => ($fullWithdrawal->fullWithdrawal->suwasahana_amount + $fullWithdrawal->fullWithdrawal->suwasahana_arreas) ?? 0,
                        "amount" => ($fullWithdrawal->fullWithdrawal->suwasahana_amount + $fullWithdrawal->fullWithdrawal->suwasahana_arreas) ?? 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                        "description" => 'Suwasahana Loan recovery from Fund',
                        "reference" => 'Full Withdrawal',
                        "comments" => 'Suwasahana Loan recovery from Fund',
                        "transactioncCodeID" => 'LoanRecM',
                        "taxTypeID" => 1,
                        "gl" => false,
                        "ar" => true,
                    ];
                    $arPayloads[] = [
                        "aRbatchId" => 'ARB008',
                        "amount" => $fullWithdrawal->fullWithdrawal->suwasahana_amount ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_amount ?? 0,
//                        "debit" => 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => '7500>7530',
                        "description" => 'Suwasahana loan recovery from Fund',
                        "reference" => 'Full Withdrawal',
//                        "reference" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                        "comments" => 'Suwasahana loan recovery from Fund',
                        "transactioncCodeID" => 'JNL',
                        "taxTypeID" => 1,
                        "gl" => true,
                        "ar" => false,
                    ];
                    if ($fullWithdrawal->fullWithdrawal->suwasahana_arreas>0){
                        $arPayloads[] = [
                            "aRbatchId" => 'ARB008',
                            "amount" => $fullWithdrawal->fullWithdrawal->suwasahana_arreas ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->suwasahana_arreas ?? 0,
//                        "debit" => 0,
                            "transactionDate" => now()->toIso8601String(),
                            "customer" => '1000>1012',
                            "description" => 'Suwasahana interest recovery from Fund',
                            "reference" => 'Full Withdrawal',
//                        "reference" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                            "comments" => 'Suwasahana interest recovery from Fund',
                            "transactioncCodeID" => 'JNL',
                            "taxTypeID" => 1,
                            "gl" => true,
                            "ar" => false,
                        ];
                    }
                } else{
                    $arPayloads[] = [
                        "aRbatchId" => 'ARB012',
                        "amount" => $fullWithdrawal->fullWithdrawal->approved_amount ?? 0,
//                        "credit" => $fullWithdrawal->fullWithdrawal->approved_amount ?? 0,
//                        "debit" => 0,
                        "transactionDate" => now()->toIso8601String(),
                        "customer" => $fullWithdrawal->membership->regimental_number . '-' . $fullWithdrawal->membership->enumber ?? '00000000',
                        "description" => 'Full Withdrawal',
                        "reference" => 'Full Withdrawal',
//                        "reference" => 'Withdrawal application of '. $fullWithdrawal->application_reg_no,
                        "comments" => 'Withdrawal-'. $fullWithdrawal->membership->name,
                        "transactioncCodeID" => 'WW',
                        "taxTypeID" => 1,
                        "gl" => false,
                        "ar" => true,
                    ];
                }

                $fullWithdrawal->status_id = 714;
                $fullWithdrawal->processing = 6;
                $fullWithdrawal->is_banked = 0;
                $fullWithdrawal->currentuser = Auth::user()->name;
                $fullWithdrawal->save();

                $fullWithdrawal->fullWithdrawal->voucher_no = $voucherId;
                $fullWithdrawal->fullWithdrawal->voucher_amount = $fullWithdrawal->fullWithdrawal->approved_amount;
                $fullWithdrawal->fullWithdrawal->withdrawal_amount = $fullWithdrawal->fullWithdrawal->eligible_amount;
                $fullWithdrawal->fullWithdrawal->paid_date = now()->format('Y-m-d');

                $fullWithdrawal->fullWithdrawal->currentuser = Auth::user()->name;
                $fullWithdrawal->fullWithdrawal->save();

                $fullWithdrawal->membership->member_status_id = 8;
                $fullWithdrawal->membership->save();

                if ($fullWithdrawal->fullWithdrawal->eligible_amount > 0){
                    $cashbookPayloads[] = [
                        'cashbookId' => 'CB076',
                        'credit' => $fullWithdrawal->fullWithdrawal->eligible_amount ?? 0,
                        'debit' => 0,
                        'customer' => $fullWithdrawal->membership->regimental_number . '-' . ($fullWithdrawal->membership->enumber ?? '000000'),
                        'transactionDate' => now()->toIso8601String(),
                        'description' => 'Withdrawal transaction',
                        'comments' => 'Withdrawal',
                        'reference' => $fullWithdrawal->membership->name,
                        'gl' => false,
                        'ar' => true,
                    ];
                }

                FullWithdrawalLog::create([
                    'full_withdrawal_id' => $fullWithdrawal->application_reg_no,
                    'version' => 0,
                    'is_batch_to_do' => 1,
                    'membership_id' => $fullWithdrawal->member_id,
                    'withdrawal_amount' =>  $fullWithdrawal->fullWithdrawal->approved_amount,
                    'currentuser' => Auth::user()->name,
                ]);

                WithdrawalAssign::create([
                    'withdrawal_id' => $fullWithdrawal->application_reg_no,
                    'fwd_to' => 0,
                    'fwd_to_reason' => 'Nothing to Process',
                    'fwd_by' => Auth::user()->id,
                    'fwd_by_reason' => 'Paid',
                ]);
            }

            $this->sendARBatchUpdate($arPayloads);

            $this->sendCashBookUpdate($cashbookPayloads);
//            $apiSuccess = $response['success'];
//            if (!$apiSuccess) {
//                FailedWithdrawalApi::create([
//                    'enumber' => $fullWithdrawal->membership->enumber,
//                    'amount' => $fullWithdrawal->amount,
//                    'reference' => $fullWithdrawal->membership->name,
//                    'reason' => 'Full withdrawal failed: ' . $response['message'],
//                ]);
//            }

            return redirect()->back()->with('success', 'Disbursement finalized.');
        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function fullBanked(Request $request){
        $fullWithdrawals = FullWithdrawalApplication::where('processing','6')
            ->where('is_banked','=','0')
            ->get();
        $action = $request->approval;

        if ($action === 'banked') {
            foreach ($fullWithdrawals as $fullWithdrawal) {
                $fullWithdrawal->processing = 0;
                $fullWithdrawal->is_banked = 1;
                $fullWithdrawal->save();
            }

            return redirect()->route('full.bulk')->with('success', 'Disbursement finalized.');
        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }

    public function approvedFull($id){
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason', 'assigns'])
            ->find($id);
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

        return view('withdrawals.disburse-full',compact('fullWithdrawal', 'usersForward',
            'usersReject', 'rejectReasons'));
    }
    public function disburseFull(Request $request, $id){
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason', 'assigns'])
            ->find($id);

        $loanApplication = LoanApplication::with('loan', 'directSettlement')
            ->where('member_id', $fullWithdrawal->membership->id)->latest('registered_date')->first();

        $action = $request->input('approval');

        if ($action === 'forward') {
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $voucher_no = $request->validate([
                'voucher_no' => 'required',
            ]);
            $fullWithdrawal->fullWithdrawal->voucher_no = $voucher_no['voucher_no'];
            $fullWithdrawal->fullWithdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $fullWithdrawal->fullWithdrawal->withdrawal_amount = str_replace(',', '', $request->withdrawal_amount);
            $fullWithdrawal->fullWithdrawal->currentuser = Auth::user()->name;

            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded to process.';
            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with('success', 'Application forwarded to process');

        } elseif ($action === 'send') {
            $fullWithdrawal->processing = 5;
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $voucher_no = $request->validate([
                'voucher_no' => 'required',
            ]);
            $fullWithdrawal->fullWithdrawal->voucher_no = $voucher_no['voucher_no'];
            $fullWithdrawal->fullWithdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $fullWithdrawal->fullWithdrawal->withdrawal_amount = str_replace(',', '', $request->withdrawal_amount);
            $fullWithdrawal->fullWithdrawal->currentuser = Auth::user()->name;

            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for disbursement.';
            WithdrawalAssign::create($validatedAssign);


            return redirect()->route('withdrawals.indexFull')->with('success', 'Application forwarded for disbursement');

        } elseif ($action === 'disburse') {
            $fullWithdrawal->status_id = 714;
            $fullWithdrawal->processing = 0;
            $fullWithdrawal->is_banked = 0;
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->voucher_no = $request->input('voucher_no');
            $fullWithdrawal->fullWithdrawal->voucher_amount = str_replace(',', '', $request->voucher_amount);
            $fullWithdrawal->fullWithdrawal->withdrawal_amount = str_replace(',', '', $request->withdrawal_amount);
            $fullWithdrawal->fullWithdrawal->paid_date = now()->format('Y-m-d');

            $fullWithdrawal->fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->fullWithdrawal->save();

            $fullWithdrawal->membership->member_status_id = 8;
            $fullWithdrawal->membership->save();

            FullWithdrawalLog::create([
                'full_withdrawal_id' => $request->application_reg_no,
                'version' => 0,
                'is_batch_to_do' => 1,
                'membership_id' => $fullWithdrawal->membership->id,
                'withdrawal_amount' =>  $request->input('voucher_amount'),
                'currentuser' => Auth::user()->name,
            ]);

            if($loanApplication){
                $loanApplication->loan->settled = 1;
                $loanApplication->loan->total_recovered_capital += $request->input('loan_due_cap');
                $loanApplication->loan->currentuser = Auth::user()->name;
                $loanApplication->loan->save();

                DirectSettlment::create([
                    'direct_settlement_id' => $loanApplication->application_reg_no,
                    'arrest_interest' => $request->input('arrest_interest'),
                    'loan_due_cap' =>  $request->input('loan_due_cap'),
                    'direct_settlement_voucher_no' => $request->input('voucher_no'),
                    'withdrawal_application_id' => $fullWithdrawal->application_reg_no,
                    'settlement_type_id' => 3,
                    'payment_mode_id' => 2,
                    'status' => 5070,
                    'extra_id' => 1,
                    'receipt_no' => 16,
                    'settlement_date' => now(),
                    'approved' => 1,
                    'settlement_amount' => $request->input('loan_due_cap') + $request->input('arrest_interest'),
                    'currentuser' => Auth::user()->name,
                    'created_system' => 'AFMS',
                ]);
            }

            WithdrawalAssign::create([
                'withdrawal_id' => $fullWithdrawal->application_reg_no,
                'fwd_to' => 0,
                'fwd_to_reason' => 'Nothing to Process',
                'fwd_by' => Auth::user()->id,
                'fwd_by_reason' => 'Paid',
            ]);

            return redirect()->route('withdrawals.indexFull')->with(['success' => 'Successfully Disbursed..']);

        } elseif ($action === 'reject') {
            $fullWithdrawal->processing = 2;
            $fullWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $fullWithdrawal->reject_date = now();
            $fullWithdrawal->reject_level = Auth::user()->name;
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $fullWithdrawal->fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with(['success' => 'Successfully approved..']);


        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function indexFull(){
        $fullWithdrawals = FullWithdrawalApplication::with('membership', 'fullWithdrawal')
            ->where('processing', '!=', 0)
            ->get();
        foreach ($fullWithdrawals as $fullWithdrawal) {
            $latestAssignment = $fullWithdrawal->assigns->sortByDesc('created_at')->first();

            if ($latestAssignment && $latestAssignment->fwd_to) {
                    $userId = $latestAssignment->fwd_to;
                $reason = $latestAssignment->fwd_to_reason;
                $user = User::find($userId);

                $fullWithdrawal->userName = $user ? $user->name : '-';
                $fullWithdrawal->reason = $reason ?: '-';
            } else {
                $fullWithdrawal->userName = '-';
                $fullWithdrawal->reason = '-';
            }
        }

        return view('withdrawals.index-full',compact('fullWithdrawals'));
    }
    public function viewFull($id){
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason', 'assigns'])
            ->find($id);
//        $users = User::all();
        $currentUser = auth()->user();

        $userRole = $currentUser->roles()->first()->name;
//        $rolesToForward = $currentUser->forward_roles ?? [];
        $rolesToForward = ($userRole == 'Ledger Section OC') ? ['Full Payment Section Cleark'] : $currentUser->forward_roles ?? [];

        $usersForward = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $rolesToReject = $currentUser->reject_roles ?? [];
        $usersReject = User::whereHas('roles', function($query) use ($rolesToReject) {
            $query->whereIn('name', $rolesToReject);
        })->get();
        $rejectReasons = RejectReason::all();

        $approvedAmount = $fullWithdrawal->fullWithdrawal->fund_balance - ($fullWithdrawal->fullWithdrawal->other_deduction) ;

//        dd($partialWithdrawal);
        return view('withdrawals.view-full',compact('fullWithdrawal', 'usersForward', 'usersReject', 'rejectReasons', 'approvedAmount'));
    }
    public function fullApproveReject(Request $request, $id)
    {
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal', 'rejectReason', 'assigns'])
            ->find($id);
        $action = $request->input('approval');

        if ($action === 'forward') {

            $fullWithdrawal->processing = 1;
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->reject_date = null;
            $fullWithdrawal->reject_level = '';
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->approved_amount = str_replace(',', '', $request->approved_amount);
            $fullWithdrawal->fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and forwarded for further proceedings.';
            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with('success', 'Application forwarded to process..');

        } elseif ($action === 'process') {
            $fullWithdrawal->processing = 3;
            $fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->reject_date = null;
            $fullWithdrawal->reject_level = '';
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->approved_amount = str_replace(',', '', $request->approved_amount);
            $fullWithdrawal->fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and successfully registered for the process.';

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with(['success' => 'Successfully registered..']);

        }elseif ($action === 'approve') {
            $fullWithdrawal->processing = 4;
            $fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->reject_date = null;
            $fullWithdrawal->reject_level = '';
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->reject_reason_id = 0;
            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Checked and approved for the payment';

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with(['success' => 'Successfully approved..']);


        }elseif ($action === 'reject') {
            $fullWithdrawal->processing = 2;
            $fullWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $fullWithdrawal->reject_date = now();
            $fullWithdrawal->reject_level = Auth::user()->name;
            $fullWithdrawal->currentuser = Auth::user()->name;
            $fullWithdrawal->save();

            $fullWithdrawal->fullWithdrawal->reject_reason_id = $request->input('fwd_to_reason');
            $fullWithdrawal->fullWithdrawal->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
            ]);
            $validatedAssign['withdrawal_id'] = $fullWithdrawal->application_reg_no;
            $validatedAssign['fwd_to_reason'] = 'Rejected';
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = $request->input('fwd_to_reason');

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexFull')->with(['success' => 'Rejected the application']);

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function create($id)
    {
        $membership = Membership::with('ranks', 'absents')->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $withdrawalProducts = WithdrawalProduct::where('id', '!=', 3)->get();
        $loanApplication = LoanApplication::with('loan')->where('member_id', $id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $id)->latest('Issue_Date')->first();
//        $users = User::all();
        $prePartialWithdrawals = PartialWithdrawalApplication::where('member_id', $id)->get();
        $prePartialWithdrawal = PartialWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();
        $partialWithdrawal = PartialWithdrawalApplication::latest('registered_date')->first();
        $preFullWithdrawal = FullWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();

        $transactionController = new MembershipController();
        $result = $transactionController->show($id);
        $fundBalance = $result['fundBalance'];
        $armyService = $result['armyService'];

        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        $count = $prePartialWithdrawals->where('processing', 0)
            ->where('status_id', 614)
            ->count();

        if ($membership->member_status_id == 8){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Account already closed!');
        } elseif ($count >= 2){
                return redirect()->route('memberships.show', $id)
                    ->with('error', 'Cannot apply, almost taken maximum number of partial withdrawals.!');
        }elseif ($count == 1 && $armyService < 25){
                return redirect()->route('memberships.show', $id)
                    ->with('error', 'Cannot apply, military service not satisfied to proceed.!');
        } elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing==2){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Registered withdrawal application is rejected, delete the application and try again!');
        } elseif ($prePartialWithdrawal && ($prePartialWithdrawal->processing!=0 || ($prePartialWithdrawal->status_id>599 && $prePartialWithdrawal->status_id!=614))) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, Withdrawal application is already processing!');
        } elseif ($preFullWithdrawal) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, already Withdrew the full balance!');
        } elseif ($loanApplication && $loanApplication->processing!=0) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply for a withdrawal. The loan application is already being processed.!');
        } else {


            if(!$partialWithdrawal){
                $partialValue = '00001';
            }else{
                $partial_reg = $partialWithdrawal->application_reg_no;
                $partial_count = explode('/', $partial_reg);
                if (count($partial_count) >= 3) {
                    $partialValue = (int)$partial_count[2]+1;
                    $partialValue = str_pad($partialValue, 5, '0', STR_PAD_LEFT);
                } else {
                    $partialValue = 'NA';
                }
            }


            if ($loanApplication){
                if (!$loanApplication->loan){
                    $dueAmount = 0;

                } elseif ($loanApplication->loan->settled==0) {
                    $dueAmount = $loanApplication->loan->total_capital - $loanApplication->loan->total_recovered_capital;
                } else{
                    $dueAmount = 0;
                }
            }else{
                $dueAmount = 0;
            }

            if ($suwasahana){
                if ($suwasahana->settled==0){
                    $loanAmount = $suwasahana->total_capital;
                    $interestSuwasahana = $suwasahana->total_interest - $suwasahana->total_recovered_interest;
                    $recoveredAmount = $suwasahana->total_recovered_capital;
                    $dueSuwasahana = $loanAmount - $recoveredAmount;
                }else{
                    $loanAmount = 0;
                    $recoveredAmount = 0;
                    $dueSuwasahana = 0;
                    $interestSuwasahana = 0;
                }
            }else{
                $loanAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
                $interestSuwasahana = 0;
            }

            return view('withdrawals.create',compact('membership', 'withdrawalProducts', 'users',
                'loanApplication', 'partialValue','banks', 'branches', 'branchCodes', 'fundBalance', 'suwasahana',
                'loanAmount', 'recoveredAmount', 'dueSuwasahana', 'interestSuwasahana','dueAmount', 'armyService',
                'prePartialWithdrawal'));
        }

    }
    public function createSpecial($id)
    {
        $membership = Membership::with('ranks', 'absents')->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $withdrawalProducts = WithdrawalProduct::where('id', '!=', 3)->get();
        $loanApplication = LoanApplication::with('loan')->where('member_id', $id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $id)->latest('Issue_Date')->first();
//        $users = User::all();
        $prePartialWithdrawals = PartialWithdrawalApplication::where('member_id', $id)->get();
        $prePartialWithdrawal = PartialWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();
        $partialWithdrawal = PartialWithdrawalApplication::latest('registered_date')->first();
        $preFullWithdrawal = FullWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();

        $transactionController = new MembershipController();
        $result = $transactionController->show($id);
        $fundBalance = $result['fundBalance'];
        $armyService = $result['armyService'];

        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();

        if ($membership->member_status_id == 8){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Account already closed!');
        } elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing==2){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Registered withdrawal application is rejected, delete the application and try again!');
        } elseif ($prePartialWithdrawal && ($prePartialWithdrawal->processing!=0 || ($prePartialWithdrawal->status_id>599 && $prePartialWithdrawal->status_id!=614))) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, Withdrawal application is already processing!');
        } elseif ($preFullWithdrawal) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, already Withdrew the full balance!');
        } elseif ($loanApplication && $loanApplication->processing!=0) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply for a withdrawal. The loan application is already being processed.!');
        } else {
            $partial_reg = $partialWithdrawal->application_reg_no;
            $partial_count = explode('/', $partial_reg);
            if (count($partial_count) >= 3) {
                $partialValue = (int)$partial_count[2]+1;
                $partialValue = str_pad($partialValue, 5, '0', STR_PAD_LEFT);
            } else {
                $partialValue = 'NA';
            }

            if ($loanApplication){
                if (!$loanApplication->loan){
                    $dueAmount = 0;

                } elseif ($loanApplication->loan->settled==0) {
                    $dueAmount = $loanApplication->loan->total_capital - $loanApplication->loan->total_recovered_capital;
                } else{
                    $dueAmount = 0;
                }
            }else{
                $dueAmount = 0;
            }

            if ($suwasahana){
                if ($suwasahana->settled==0){
                    $loanAmount = $suwasahana->total_capital;
                    $interestSuwasahana = $suwasahana->total_interest - $suwasahana->total_recovered_interest;
                    $recoveredAmount = $suwasahana->total_recovered_capital;
                    $dueSuwasahana = $loanAmount - $recoveredAmount;
                }else{
                    $loanAmount = 0;
                    $recoveredAmount = 0;
                    $dueSuwasahana = 0;
                    $interestSuwasahana = 0;
                }
            }else{
                $loanAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
                $interestSuwasahana = 0;
            }

            return view('withdrawals.create-special',compact('membership', 'withdrawalProducts', 'users',
                'loanApplication', 'partialValue','banks', 'branches', 'branchCodes', 'fundBalance', 'suwasahana',
                'loanAmount', 'recoveredAmount', 'dueSuwasahana', 'dueAmount', 'armyService',
                'prePartialWithdrawal', 'interestSuwasahana'));
        }

    }
    public function createFull($id)
    {
        $membership = Membership::with('ranks')->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $loanApplication = LoanApplication::with('loan')->where('member_id', $id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $id)->latest('Issue_Date')->first();
//        $users = User::all();
        $fullWithdrawal = FullWithdrawalApplication::latest('registered_date')->first();
        $prePartialWithdrawal = PartialWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();
        $preFullWithdrawal = FullWithdrawalApplication::where('member_id', $id)->latest('registered_date')->first();

        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();
        if ($membership->member_status_id == 8){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Member already closed!');
        } elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing==2){
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Registered partial withdrawal application is rejected, delete the application and try again!');
        } elseif ($prePartialWithdrawal && $prePartialWithdrawal->processing!=0) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, partial withdrawal application is already processing!');
        } elseif ($preFullWithdrawal) {
            return redirect()->route('memberships.show', $id)
                ->with('error', 'Cannot apply, already Withdrew the full balance!');
        } else {
            $full_reg = $fullWithdrawal->application_reg_no ?? '0/0/0';
            $full_count = explode('/', $full_reg);

            if (count($full_count) >= 3) {
                $fullValue = (int)$full_count[2]+1;
                $fullValue = str_pad($fullValue, 5, '0', STR_PAD_LEFT);
            } else {
                $fullValue = 'NA';
            }
            if ($loanApplication){
                if (!$loanApplication->loan){
                    $dueAmount = 0;
                } elseif ($loanApplication->loan->settled==0) {
                    $dueAmount = $loanApplication->loan->total_capital - $loanApplication->loan->total_recovered_capital;
                } else{
                    $dueAmount = 0;
                }
            }else{
                $dueAmount = 0;
            }

            if ($suwasahana){
                if ($suwasahana->settled==0){
                    $loanAmount = $suwasahana->total_capital;
                    $interestSuwasahana = $suwasahana->total_interest - $suwasahana->total_recovered_interest;
                    $recoveredAmount = $suwasahana->total_recovered_capital;
                    $dueSuwasahana = $loanAmount - $recoveredAmount;
                }else{
                    $loanAmount = 0;
                    $recoveredAmount = 0;
                    $dueSuwasahana = 0;
                    $interestSuwasahana = 0;
                }
            }else{
                $loanAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
                $interestSuwasahana = 0;
            }

            $transactionController = new MembershipController();
            $result = $transactionController->show($id);
            $fundBalance = $result['fundBalance'];

            return view('withdrawals.create-full',compact('membership', 'users',
                'loanApplication', 'fullValue','banks', 'branches', 'branchCodes', 'fundBalance', 'suwasahana',
                'loanAmount', 'recoveredAmount', 'dueSuwasahana', 'interestSuwasahana', 'dueAmount', 'fullWithdrawal',
            ));
        }
    }
    public function store(Request $request, $id){
//dd($request);
        $partialWithdrawal = PartialWithdrawalApplication::latest('registered_date')->first();

        if(!$partialWithdrawal){
            $partialValue = '00000';
        }else{
            $partial_reg = $partialWithdrawal->application_reg_no;
            $partial_count = explode('/', $partial_reg);
            if (count($partial_count) >= 3) {
                $partialValue = (int)$partial_count[2]+1;
                $partialValue = str_pad($partialValue, 5, '0', STR_PAD_LEFT);

            } else {
                $partialValue = 'NA';
            }
        }

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $reg_no = $request->input('application_reg_no');
        $reg_no = explode('/', $reg_no);
        if (count($reg_no) >= 3) {
            $reg_no = (int)$reg_no[2]+1;
        } else {
            $reg_no = 'NA';
        }

        if ($reg_no == $partialValue) {
            return redirect()->back()->withErrors(['error' => 'Application number already exists']);
        } else{
            $validatedRegistration = $request->validate([
                'application_reg_no' => 'required',
                'received_date' => 'required',
//                'withdrawal_product' => 'required',
//                'gender' => 'required',
                'requested_amount' => 'numeric|min:0',
                'purpose' => 'required',
            ]);
            $validatedRegistration['registered_date'] = now();
            $validatedRegistration['member_id'] = $id;
            $validatedRegistration['status_id'] = 600;
            $validatedRegistration['altering'] = 0;
            $validatedRegistration['withdrawal_product'] = $request->withdrawal_product;
            $validatedRegistration['gender'] = $request->gender;
            $validatedRegistration['special'] = $request->special;
            $validatedRegistration['processing'] = 1;
            $validatedRegistration['created_system'] = 'AFMS';
            $validatedRegistration['currentuser'] = Auth::user()->name;
            $withdrawalApplication = PartialWithdrawalApplication::create($validatedRegistration);

//            $requestedAmount = round($request->requested_amount / 100) * 100;

            PartialWithdrawal::create([
                'withdrawal_id' => $validatedRegistration['application_reg_no'],
                'withdrawal_application_id' => $validatedRegistration['application_reg_no'],
                'membership_id' => $validatedRegistration['member_id'],
                'purpose' => $validatedRegistration['purpose'],
                'currentuser' => $validatedRegistration['currentuser'],
                'created_system' => $validatedRegistration['created_system'],
                'withdrawal_product' => $validatedRegistration['withdrawal_product'],
                'account_no' => $request->account_no,
                'bank_code' => $request->bank_code,
                'bank_name' => $request->bank_name,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'fund_balance' => $request->fund_balance,
                'calculated_amount' => $request->calculated_amount,
                'requested_amount' => $validatedRegistration['requested_amount'],
                'eligible_amount' => str_replace(',', '', $request->eligible_amount),
                'loan_due_cap' => $request->loan_due_cap,
                'arrest_interest' => $request->arrest_interest,
                'suwasahana_amount' => str_replace(',', '', $request->suwasahana_amount),
                'suwasahana_arreas' => str_replace(',', '', $request->suwasahana_arreas),
            ]);
            $validatedAssign['withdrawal_id'] = $withdrawalApplication->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Registered a withdrawal';

            WithdrawalAssign::create($validatedAssign);

            return redirect()->route('withdrawals.indexPartial')
                ->with('success', 'Withdrawal created successfully');
        }

    }
    public function storeFull(Request $request, $id){

        $fullWithdrawal = FullWithdrawalApplication::latest('registered_date')->first();
        $membership = Membership::find($id);

        $full_reg = $fullWithdrawal->application_reg_no ?? '0/0/0';
        $full_count = explode('/', $full_reg);
        if (count($full_count) >= 3) {
            $fullValue = (int)$full_count[2]+1;
            $fullValue = str_pad($fullValue, 5, '0', STR_PAD_LEFT);
        } else {
            $fullValue = 'NA';
        }
        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $reg_no = $request->input('application_reg_no');
        $reg_no = explode('/', $reg_no);
        if (count($reg_no) >= 3) {
            $reg_no = (int)$reg_no[2]+1;
        } else {
            $reg_no = 'NA';
        }

        if ($reg_no == $fullValue){
            return redirect()->back()->withErrors(['error' => 'Application number already exists']);
        }else{
            if ($membership->member_status_id == 3){
                $request->validate([
                    'become_kia' => 'required',
                ]);
                $paidAmounts = $request->input('paid_amount');
                $nomineeIds = $request->input('nominee_id');
                foreach ($nomineeIds as $index => $nomineeId) {

                    $nomineeDetail = NomineeBank::where('nominee_id', $nomineeId)->firstOrFail();
                    $paidAmount = $paidAmounts[$index];

                    $nomineeDetail->paid_amount = str_replace(',', '', $paidAmount);
                    $nomineeDetail->save();
                }
            }

            $validatedRegistration = $request->validate([
                'application_reg_no' => 'required',
                'received_date' => 'required',
            ]);
            $validatedRegistration['registered_date'] = now();
            $validatedRegistration['member_id'] = $id;
            $validatedRegistration['status_id'] = 700;
            $validatedRegistration['member_status_id'] = $membership->member_status_id;
            $validatedRegistration['altering'] = 0;
            $validatedRegistration['processing'] = 1;
            $validatedRegistration['created_system'] = 'AFMS';
            $validatedRegistration['currentuser'] = Auth::user()->name;

            $withdrawalApplication = FullWithdrawalApplication::create($validatedRegistration);

            FullWithdrawal::create([
                'withdrawal_id' => $validatedRegistration['application_reg_no'],
                'full_withdrawal_application_id' => $validatedRegistration['application_reg_no'],
                'membership_id' => $validatedRegistration['member_id'],
                'currentuser' => $validatedRegistration['currentuser'],
                'created_system' => $validatedRegistration['created_system'],
                'account_no' => $request->account_no,
                'bank_code' => $request->bank_code,
                'bank_name' => $request->bank_name,
                'branch_code' => $request->branch_code,
                'branch_name' => $request->branch_name,
                'fund_balance' => $request->fund_balance,
                'eligible_amount' => str_replace(',', '', $request->eligible_amount),
                'other_deduction' => str_replace(',', '', $request->other_deduction),
                'withdrawal_product' => $request->withdrawal_product,
                'become_kia' => $request->become_kia,
                'loan_due_cap' => $request->loan_due_cap,
                'arrest_interest' => $request->arrest_interest,
                'suwasahana_amount' => $request->suwasahana_amount,
                'suwasahana_arreas' => $request->suwasahana_arreas,

            ]);
            $validatedAssign['withdrawal_id'] = $withdrawalApplication->application_reg_no;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Registered a withdrawal';

            $WithdrawalAssign = WithdrawalAssign::create($validatedAssign);
            return redirect()->route('withdrawals.indexFull')
                ->with('success', 'Full Withdrawal created successfully');
        }

    }
    public function show($id)
    {
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal'])
            ->find($id);
        return view('withdrawals.show',compact('partialWithdrawal'));
    }
    public function showFull($id)
    {
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal'])
            ->find($id);

        return view('withdrawals.show-full',compact('fullWithdrawal'));
    }
    public function editPartial($id)
    {
        $partialWithdrawal = PartialWithdrawalApplication::with(['membership', 'withdrawal'])
            ->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $withdrawalProduct = WithdrawalProduct::find($partialWithdrawal->withdrawal_product);
//        dd($withdrawalProduct);
        $loanApplication = LoanApplication::with('loan')->where('member_id', $partialWithdrawal->member_id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $partialWithdrawal->member_id)->latest('Issue_Date')->first();
//        $users = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();
        $dateEnlisted = $partialWithdrawal->membership->date_army_enlisted ? \Carbon\Carbon::parse($partialWithdrawal->membership->date_army_enlisted) : null;
        if ($dateEnlisted === null) {
            $armyEnlisted = 'Date not specified';
            $difference = 'N/A';
        } else {
            $difference = \Carbon\Carbon::now()->diffInYears($dateEnlisted);
            $armyEnlisted = $dateEnlisted->toDateString() . ' (' . $difference . ' Years ago)';
        }

        if ($loanApplication){
            if ($loanApplication->loan->settled==0){
                $dueAmount = $loanApplication->loan->total_capital - $loanApplication->loan->total_recovered_capital;

            }else{
                $dueAmount = 0;
            }
        }else{
            $dueAmount = 0;
        }

        if ($suwasahana){
            if ($suwasahana->settled==0){
                $loanAmount = $suwasahana->total_capital + $suwasahana->total_interest;
                $recoveredAmount = $suwasahana->total_recovered_capital + $suwasahana->total_recovered_interest;

                $dueSuwasahana = $loanAmount - $recoveredAmount;
            }else{
                $loanAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
            }
        }else{
            $loanAmount = 0;
            $recoveredAmount = 0;
            $dueSuwasahana = 0;
        }

        $transactionController = new MembershipController();
        $result = $transactionController->show($partialWithdrawal->member_id);
        $fundBalance = $result['fundBalance'];

        return view('withdrawals.edit-partial',compact('partialWithdrawal', 'withdrawalProduct', 'users',
            'armyEnlisted', 'difference', 'loanApplication','banks', 'branches', 'branchCodes',
            'fundBalance', 'suwasahana', 'loanAmount', 'recoveredAmount', 'dueSuwasahana', 'dueAmount',
            ));
    }
    public function updatePartial(Request $request, $id){

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $validatedRegistration = $request->validate([
            'application_reg_no' => 'required',
            'purpose' => 'required',
        ]);
        $withdrawalApplication = PartialWithdrawalApplication::find($id);

        $validatedRegistration['member_id'] = $withdrawalApplication->member_id;
        $validatedRegistration['altering'] = 1;
        $validatedRegistration['created_system'] = 'AFMS';
        $validatedRegistration['currentuser'] = Auth::user()->name;


        $withdrawalApplication -> update($validatedRegistration);

        PartialWithdrawal::where('withdrawal_id', $withdrawalApplication->application_reg_no)->update([
            'withdrawal_application_id' => $validatedRegistration['application_reg_no'],
            'membership_id' => $validatedRegistration['member_id'],
            'purpose' => $validatedRegistration['purpose'],
            'currentuser' => $validatedRegistration['currentuser'],
            'created_system' => $validatedRegistration['created_system'],
            'account_no' => $request->account_no,
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
            'fund_balance' => $request->fund_balance,
            'calculated_amount' => $request->calculated_amount,
            'requested_amount' => str_replace(',', '', $request->requested_amount),
            'eligible_amount' => $request->eligible_amount,
            'loan_due_cap' => $request->loan_due_cap,
            'arrest_interest' => str_replace(',', '', $request->arrest_interest),
            'suwasahana_amount' => $request->suwasahana_amount,
        ]);
        $validatedAssign['withdrawal_id'] = $withdrawalApplication->application_reg_no;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Registered a withdrawal';

        $WithdrawalAssign = WithdrawalAssign::create($validatedAssign);

        return redirect()->route('withdrawals.indexPartial')->with('success', 'Withdrawal updated successfully');

    }
    public function editFull($id)
    {
        $fullWithdrawal = FullWithdrawalApplication::with(['membership', 'fullWithdrawal'])
            ->find($id);
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $loanApplication = LoanApplication::with('loan')->where('member_id', $fullWithdrawal->member_id)->latest('registered_date')->first();
        $suwasahana = Suwasahana::where('member_id', $fullWithdrawal->member_id)->latest('Issue_Date')->first();
//        $users = User::all();
        $currentUser = auth()->user();
        $rolesToForward = $currentUser->forward_roles ?? [];
        $users = User::whereHas('roles', function($query) use ($rolesToForward) {
            $query->whereIn('name', $rolesToForward);
        })->get();
        if ($loanApplication){
            if ($loanApplication->loan->settled==0){
                $dueAmount = $loanApplication->loan->total_capital - $loanApplication->loan->total_recovered_capital;

            }else{
                $dueAmount = 0;
            }
        }else{
            $dueAmount = 0;
        }

        if ($suwasahana){
            if ($suwasahana->settled==0){
                $loanAmount = $suwasahana->total_capital + $suwasahana->total_interest;
                $recoveredAmount = $suwasahana->total_recovered_capital + $suwasahana->total_recovered_interest;

                $dueSuwasahana = $loanAmount - $recoveredAmount;
            }else{
                $loanAmount = 0;
                $recoveredAmount = 0;
                $dueSuwasahana = 0;
            }
        }else{
            $loanAmount = 0;
            $recoveredAmount = 0;
            $dueSuwasahana = 0;
        }

        $transactionController = new MembershipController();
        $result = $transactionController->show($fullWithdrawal->member_id);
        $fundBalance = $result['fundBalance'];

        return view('withdrawals.edit-full',compact('fullWithdrawal', 'users',
            'loanApplication','banks', 'branches', 'branchCodes', 'fundBalance', 'suwasahana',
            'loanAmount', 'recoveredAmount', 'dueSuwasahana', 'dueAmount',
            ));
    }
    public function updateFull(Request $request, $id){

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
            'fwd_to_reason' => 'required',
        ]);

        $validatedRegistration = $request->validate([
            'application_reg_no' => 'required',
        ]);
        $withdrawalApplication = FullWithdrawalApplication::find($id);

        $validatedRegistration['member_id'] = $withdrawalApplication->member_id;
        $validatedRegistration['altering'] = 1;
        $validatedRegistration['created_system'] = 'AFMS';
        $validatedRegistration['currentuser'] = Auth::user()->name;

        $withdrawalApplication -> update($validatedRegistration);

        if ($withdrawalApplication->member_status_id == 3){
            $request->validate([
                'become_kia' => 'required',
            ]);
            $paidAmounts = $request->input('paid_amount');
            $nomineeIds = $request->input('nominee_id');
            foreach ($nomineeIds as $index => $nomineeId) {

                $nomineeDetail = NomineeBank::where('nominee_id', $nomineeId)->firstOrFail();
                $paidAmount = $paidAmounts[$index];

                $nomineeDetail->paid_amount = str_replace(',', '', $paidAmount);
                $nomineeDetail->save();
            }
        }

        FullWithdrawal::where('withdrawal_id', $withdrawalApplication->application_reg_no)->update([
            'full_withdrawal_application_id' => $validatedRegistration['application_reg_no'],
            'membership_id' => $validatedRegistration['member_id'],
            'currentuser' => $validatedRegistration['currentuser'],
            'created_system' => $validatedRegistration['created_system'],
            'account_no' => $request->account_no,
            'bank_code' => $request->bank_code,
            'bank_name' => $request->bank_name,
            'branch_code' => $request->branch_code,
            'branch_name' => $request->branch_name,
            'fund_balance' => $request->fund_balance,
            'eligible_amount' => str_replace(',', '', $request->eligible_amount),
            'other_deduction' => str_replace(',', '', $request->other_deduction),
            'loan_due_cap' => $request->loan_due_cap,
            'become_kia' => $request->become_kia ?? null,
            'arrest_interest' => str_replace(',', '', $request->arrest_interest),
            'suwasahana_amount' => $request->suwasahana_amount,
        ]);
        $validatedAssign['withdrawal_id'] = $withdrawalApplication->application_reg_no;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Registered a withdrawal';

        $WithdrawalAssign = WithdrawalAssign::create($validatedAssign);

        return redirect()->route('withdrawals.indexFull')->with('success', 'Full withdrawal updated successfully');

    }
    public function destroyPartial($id){
        $partialWithdrawal = PartialWithdrawalApplication::with('withdrawal')->find($id);

        $partialWithdrawal->delete();
        $partialWithdrawal->withdrawal->delete();

        return redirect()->route('withdrawals.indexPartial')
            ->with('success','Withdrawal application deleted successfully');
    }
    public function destroyFull($id){
        $fullWithdrawal = FullWithdrawalApplication::with('fullWithdrawal')->find($id);

        $fullWithdrawal->delete();
        $fullWithdrawal->fullWithdrawal->delete();

        return redirect()->route('withdrawals.indexFull')
            ->with('success','Withdrawal application deleted successfully');
    }
}
