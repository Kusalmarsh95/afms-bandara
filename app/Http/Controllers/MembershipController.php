<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\Contribution;
use App\Models\ContributionSummary;
use App\Models\District;
use App\Models\MemberCategory;
use App\Models\MemberEdit;
use App\Models\Membership;
use App\Models\MembershipAssign;
use App\Models\MemberStatus;
use App\Models\PartialWithdrawalApplication;
use App\Models\Rank;
use App\Models\Regiment;
use App\Models\Suwasahana;
use App\Models\TransferHistory;
use App\Models\Unit;
use App\Models\UnitTransfer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MembershipController extends Controller
{
    public function index(Request $request)
    {
        $members = Membership::with('ranks', 'regiments', 'units', 'category', 'status', 'district')
            ->where('acceptedl1', 1)
            ->where('rejectedl2', 0)
            ->where('altering', 0);

        if ($request->has('search_value')) {
            $searchValue = $request->input('search_value');
            $members->where(function ($query) use ($searchValue) {
                $query->where('regimental_number', '=', $searchValue)
                    ->orWhere('enumber', '=', $searchValue);
            });
        }

        $memberships = $members->paginate(10);

        return view('memberships.index', compact('memberships'));
    }
    public function show($id)
    {
        $membership = Membership::with('ranks', 'regiments', 'units', 'category', 'status', 'transfers', 'absents',
            'nominees', 'contributions', 'contributionsHistory','contributionsSummary',
            'fullWithdrawalApplication', 'partialWithdrawalApplication', 'loanApplications'
            )
            ->findOrFail($id);
        $suwasahana = Suwasahana::with('membership')->where('member_id', $id)->get();

        $openingBalance = DB::table('contribution_yearly_summary')
            ->where('membership_id', $id)
            ->orderBy('transaction_date')
            ->value('opening_balance');

        $transactionsQuery = DB::table('withdrawal')
            ->select('id as transaction_id', 'membership_id', 'total_withdraw_amount as transaction_amount', 'paid_date as transaction_date', DB::raw("'Withdraw' as type"), DB::raw("'Partial Withdrawal' as remark"))
            ->where('membership_id', $id)
            ->whereNotNull('paid_date')
            ->unionAll(DB::table('contribution')
                ->select('id as transaction_id', 'membership_id', 'amount as transaction_amount', 'transaction_date as transaction_date', DB::raw("'Monthly Contribution' as type"), DB::raw("'Monthly deduction' as remark"))
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('contribution_additional')
                ->select('id as transaction_id', 'membership_id', 'amount as transaction_amount', 'transaction_date as transaction_date', 'type', 'remark')
                ->where('membership_id', $id)
                ->where('accepted', 1)
            )
            ->unionAll(DB::table('contribution_correction')
                ->select('id as transaction_id', 'membership_id', 'amount as transaction_amount', 'transaction_date as transaction_date', 'type', 'remark')
                ->where('membership_id', $id)
                ->where('accepted', 1)
            )
            ->unionAll(DB::table('contribution_yearly_summary')
                ->select('id as transaction_id', 'membership_id', 'yearly_interest as transaction_amount', 'transaction_date as transaction_date', DB::raw("'Interest' as type"), DB::raw("'-' as remark"))
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('absent_settlement')
                ->select('id as transaction_id', 'membership_id', 'settlement_amount as transaction_amount', 'settlement_date as transaction_date', DB::raw("'Settlement' as type"), DB::raw("'Loan settled due to absent ' as remark"))
                ->where('membership_id', $id)
            )
            ->unionAll(DB::table('full_withdrawal')
                ->select('id as transaction_id', 'membership_id', 'voucher_amount as transaction_amount', 'paid_date as transaction_date', DB::raw("'Withdraw' as type"), DB::raw("'-' as remark"))
                ->where('membership_id', $id)
            );

        if ($openingBalance > 0) {
            $transactionsQuery = DB::table('contribution_yearly_summary')
                ->select('id as transaction_id', 'membership_id', 'opening_balance as transaction_amount', 'transaction_date as transaction_date', DB::raw("'Balance' as type"), DB::raw("'Previous balance' as remark"))
                ->where('membership_id', $id)
                ->orderBy('transaction_date')
                ->limit(1)
                ->unionAll($transactionsQuery);
        }
        $transactions = $transactionsQuery->orderBy('transaction_date')->get();

        $balance = 0;
        $fundBalance = 0;
        $count = count($transactions);

        foreach ($transactions as $key => $transaction) {
            if ($transaction->type == 'Balance') {
                $balance += $transaction->transaction_amount;
            } elseif ($transaction->type == 'Monthly Contribution' || $transaction->type == 'Interest' || $transaction->type == 'Deposit'|| $transaction->type == 'Addition' ) {
                $balance += $transaction->transaction_amount;
            } elseif ($transaction->type == 'Withdraw' || $transaction->type == 'Refund' || $transaction->type == 'Deduction' || $transaction->type == 'Settlement') {
                $balance -= $transaction->transaction_amount;
            }

            $transaction->balance = $balance;

            if ($key == $count - 1) {
                $fundBalance = $balance;
            }
        }

        $dateEnlisted = $membership->date_army_enlisted ? \Carbon\Carbon::parse($membership->date_army_enlisted) : null;

        if ($dateEnlisted === null) {
            $armyEnlisted = 'Date not specified';
            $differenceEnlisted = 'N/A';
            $armyService = 'Cannot Calculate';
        } else {
            $difference = \Carbon\Carbon::now()->diff($dateEnlisted);
            $armyEnlisted = $dateEnlisted->toDateString() . ' (' . $difference->y . ' Years ago)';

            $absentDays = 0;
            foreach ($membership->absents as $absentDate) {
                $absentDays += $absentDate->days;
            }

            $armyService = floor(($difference->days - $absentDays) / 365);
        }

        $abfEnlisted = $membership->dateabfenlisted ? \Carbon\Carbon::parse($membership->dateabfenlisted) : null;
        if ($abfEnlisted === null) {
            $abfJoined = 'Date not specified';
            $differenceJoined = 'N/A';
        } else {
            $differenceJoined = \Carbon\Carbon::now()->diffInYears($abfEnlisted);
            $abfJoined = $abfEnlisted->toDateString() . ' (' . $differenceJoined . ' Years ago)';
        }

        $retirementDate = $membership->retirement_date ? \Carbon\Carbon::parse($membership->retirement_date) : null;

        if ($retirementDate) {
            $now = \Carbon\Carbon::now();
            $difference = $now->diff($retirementDate);

            $totalMonths = $difference->y * 12 + $difference->m;
            $remainingMonths = $difference->m % 12;

            $retirement = $retirementDate->toDateString() . ' (' . $difference->y . ' years, ' . $remainingMonths . ' months)';
        } else {
            $retirement = null;
            $difference = null;
        }
        $maxYear = $membership->contributionsSummary->max('year');
        $maxMonth = $membership->contributionsSummary->where('year', $maxYear)->max('icp_id');
        $openingBalance = $membership->contributionsSummary
            ->where('year', $maxYear)
            ->where('icp_id', $maxMonth)
            ->value('opening_balance');

        $yearlyContribution = $membership->contributions->groupBy('year');


        return view('memberships.show', compact('membership', 'armyEnlisted', 'abfJoined',
            'yearlyContribution', 'openingBalance', 'difference', 'armyService',
            'transactions', 'fundBalance', 'suwasahana', 'retirement'));
    }
    public function create()
    {
        $ranks = Rank::all();
        $regiments = Regiment::all();
        $units = Unit::all();
        $categories = MemberCategory::all();
        $status = MemberStatus::all();
        $districts = District::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $users = User::all();

        return view('memberships.create',compact('ranks',
            'categories','status', 'regiments', 'units', 'districts',
            'banks', 'branches', 'branchCodes', 'users'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'army_id' => 'nullable',
            'category_id' => 'required|exists:member_category,id', //Officer or Other Rank
            'comment' => 'required', //Employee number
            'contribution_amount' => 'required',
            'dateabfenlisted' => 'required', //ABF enlisted date*/
            'date_army_enlisted' => 'required', // Army enlisted date
            'decorations' => 'nullable',
            'dob' => 'required', // date of birth
            'email' => 'nullable',
            'member_status_id' => 'required|exists:member_status,id',
            'name' => 'required',
            'nic' => 'required',
            'rank_id' => 'required|exists:rank,id',
            'regiment_id' => 'required|exists:regiment,id',
            'regimental_number' => 'required|unique:membership,regimental_number',
            'retirement_date' => 'nullable',
            'serial_number' => 'nullable',
            'telephone_home' => 'nullable',
            'telephone_mobile' => 'nullable',
            'type' => 'required', //Regular or Volunteer
            'unit_id' => 'required|exists:unit,id',
            'address1' => 'nullable',
            'address2' => 'nullable',
            'address3' => 'nullable',
            'district_id' => 'required|exists:district,id',
            'address' => 'nullable', //working place address
            'loan10month' => 'nullable', //have or not
            'suwasahana' => 'nullable', //have or not
            'account_no' => 'required', //bank account number
            'enumber' => 'required|unique:membership,enumber',
            'bank_code' => 'required|exists:bank,id',
            'bank_name' => 'required|exists:bank,bank_name',
            'branch_code' => 'required|exists:bank_branch,branch_code',
            'branch_name' => 'required|exists:bank_branch,bank_branch_name',
        ]);


        $validatedData['version'] = '0';
        $validatedData['edit_nominee_contribution'] = 0;
        $validatedData['date_of_member_profile_created'] = now();
        $validatedData['last_modified_date'] = now();
        $validatedData['accepted_officer_user_namel1'] = 'Assigned to OC';
        $validatedData['accepted_officer_user_namel2'] = 'Assigned to OC';
        $validatedData['acceptedl1'] = 0; //if 0 pending member approval
        $validatedData['acceptedl2'] = 0; //if 0 pending member approval
        $validatedData['rejectedl2'] = 0;
        $validatedData['altering'] = 0;
        $validatedData['created_system'] = 'AFMS';
        $validatedData['currentuser'] = Auth::user()->id;

        $membership = Membership::create($validatedData);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);
        $validatedAssign['membership_id'] = $membership->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Registered a new member';
        $validatedAssign['fwd_to_reason'] = 'For Approval';
        $membershipAssign = MembershipAssign::create($validatedAssign);

        return redirect()->route('memberships.show',$membership->id)
            ->with('success', 'Member created successfully');

    }
    public function indexAssigns()
    {
        $memberships = Membership::with('ranks', 'regiments', 'units', 'category', 'status', 'district')
            ->where('acceptedl1', 0)
            ->where('rejectedl2', 0)
            ->where('altering', 0)
            ->get();
//        dd($memberships);
        return view('membershipAssigns.index',compact('memberships'));
    }
    public function indexChanges()
    {
        $membershipChanges = Membership::with('ranks', 'regiments', 'units', 'category', 'status', 'district')
            ->where('acceptedl1', 0)
            ->where('rejectedl2', 0)
            ->where('altering', 1)
            ->get();
        //dd($membershipChanges);
        return view('membershipAssigns.changes',compact('membershipChanges'));
    }
    public function indexRejects()
    {
        $memberships = Membership::with('ranks', 'regiments', 'units', 'category', 'status', 'district')
            ->where('acceptedl1', 0)
            ->where('rejectedl2', 1)
            ->where('altering', 0)
            ->get();
        return view('membershipAssigns.rejects',compact('memberships'));
    }
    public function apporvalView($id)
    {
        $membership = Membership::find($id);
        $memberEdit = MemberEdit::where('membership_id', $id)->first();
//        dd($memberEdit);
        $ranks = Rank::all();
        $regiments = Regiment::all();
        $units = Unit::all();
        $categories = MemberCategory::all();
        $status = MemberStatus::all();
        $districts = District::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $users = User::all();

        return view('membershipAssigns.edit',compact('membership','ranks', 'memberEdit',
            'categories','status', 'regiments', 'units', 'districts',
            'banks', 'branches', 'branchCodes', 'users'));
    }
    public function edit($id)
    {
        $membership = Membership::findOrFail($id);
        $ranks = Rank::all();
        $regiments = Regiment::all();
        $units = Unit::all();
        $categories = MemberCategory::all();
        $status = MemberStatus::all();
        $districts = District::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();
        $branchCodes = BankBranch::select('branch_code')->distinct()->get();
        $users = User::all();

        return view('memberships.edit',compact('membership','ranks',
            'categories','status', 'regiments', 'units', 'districts',
            'banks', 'branches', 'branchCodes', 'users'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'army_id' => 'nullable',
            'category_id' => 'nullable',
            'comment' => 'nullable',
            'contribution_amount' => 'nullable',
            'dateabfenlisted' => 'nullable',
            'date_army_enlisted' => 'nullable',
            'decorations' => 'nullable',
            'dob' => 'nullable',
            'email' => 'nullable',
            'member_status_id' => 'nullable',
            'name' => 'nullable',
            'nic' => 'nullable',
            'rank_id' => 'nullable',
            'regiment_id' => 'nullable',
            'regimental_number' => 'nullable',
            'retirement_date' => 'nullable',
            'serial_number' => 'nullable',
            'telephone_home' => 'nullable',
            'telephone_mobile' => 'nullable',
            'type' => 'nullable', //Regular or Volunteer
            'unit_id' => 'nullable',
            'address1' => 'nullable',
            'address2' => 'nullable',
            'address3' => 'nullable',
            'district_id' => 'nullable',
            'address' => 'nullable',
            'loan10month' => 'nullable',
            'suwasahana' => 'nullable',
            'account_no' => 'nullable',
            'enumber' => 'nullable',
            'bank_code' => 'nullable',
            'bank_name' => 'nullable',
            'branch_code' => 'nullable',
            'branch_name' => 'nullable',
        ]);

        $validatedData['version'] = '0';
        $validatedData['edit_nominee_contribution'] = 0;
        $validatedData['last_modified_date'] = now();
        $validatedData['acceptedl1'] = 0;
        $validatedData['acceptedl2'] = 0;
        $validatedData['rejectedl2'] = 0;
        $validatedData['altering'] = 1; //details were edited
        $validatedData['currentuser'] = Auth::user()->id;

        $membership = Membership::findOrFail($id);

        $oldCategoryId = $membership->category_id;
        $oldRankId = $membership->rank_id;
        $oldRegimentId = $membership->regiment_id;
        $oldRegiNumber = $membership->regimental_number;
        $oldSerialNum = $membership->serial_number;
        $oldStatusId = $membership->member_status_id;
        $oldTypeId = $membership->type;
        $oldUnitId = $membership->unit_id;

        $data = [];
        $data['membership_id'] = $membership->id;
        $data['currentuser'] = Auth::user()->name;

        if (isset($validatedData['army_id']) && $membership->army_id !== $validatedData['army_id']) {
            $data['army_id_edited'] = true;
        }
        if (isset($validatedData['comment']) && $membership->comment !== $validatedData['comment']) {
            $data['comment_edited'] = true;
        }
        if (isset($validatedData['dateabfenlisted']) && $membership->dateabfenlisted !== $validatedData['dateabfenlisted']) {
            $data['dateabfenlisted_edited'] = true;
        }
        if (isset($validatedData['date_army_enlisted']) && $membership->date_army_enlisted !== $validatedData['date_army_enlisted']) {
            $data['date_army_enlisted_edited'] = true;
        }
        if (isset($validatedData['decorations']) && $membership->decorations !== $validatedData['decorations']) {
            $data['decorations_edited'] = true;
        }
        if (isset($validatedData['dob']) && $membership->dob !== $validatedData['dob']) {
            $data['dob_edited'] = true;
        }
        if (isset($validatedData['email']) && $membership->email !== $validatedData['email']) {
            $data['email_edited'] = true;
        }
        if (isset($validatedData['member_status_id']) && $membership->member_status_id !== $validatedData['member_status_id']) {
            $data['member_status_id_edited'] = true;
        }
        if (isset($validatedData['name']) && $membership->name !== $validatedData['name']) {
            $data['name_edited'] = true;
        }
        if (isset($validatedData['nic']) && $membership->nic !== $validatedData['nic']) {
            $data['nic_edited'] = true;
        }
        if (isset($validatedData['rank_id']) && $membership->rank_id !== $validatedData['rank_id']) {
            $data['rank_id_edited'] = true;
        }
        if (isset($validatedData['regiment_id']) && $membership->regiment_id !== $validatedData['regiment_id']) {
            $data['regiment_id_edited'] = true;
        }
        if (isset($validatedData['regimental_number']) && $membership->regimental_number !== $validatedData['regimental_number']) {
            $data['regimental_number_edited'] = true;
        }
        if (isset($validatedData['retirement_date']) && $membership->retirement_date !== $validatedData['retirement_date']) {
            $data['retirement_date_edited'] = true;
        }
        if (isset($validatedData['serial_number']) && $membership->serial_number !== $validatedData['serial_number']) {
            $data['serial_number_edited'] = true;
        }
        if (isset($validatedData['telephone_home']) && $membership->telephone_home !== $validatedData['telephone_home']) {
            $data['telephone_home_edited'] = true;
        }
        if (isset($validatedData['telephone_mobile']) && $membership->telephone_mobile !== $validatedData['telephone_mobile']) {
            $data['telephone_mobile_edited'] = true;
        }
        if (isset($validatedData['type']) && $membership->type !== $validatedData['type']) {
            $data['type_edited'] = true;
        }
        if (isset($validatedData['unit_id']) && $membership->unit_id !== $validatedData['unit_id']) {
            $data['unit_id_edited'] = true;
        }
        if (isset($validatedData['address1']) && $membership->address1 !== $validatedData['address1']) {
            $data['address1_edited'] = true;
        }
        if (isset($validatedData['address2']) && $membership->address2 !== $validatedData['address2']) {
            $data['address2_edited'] = true;
        }
        if (isset($validatedData['rank_id']) && $membership->rank_id !== $validatedData['rank_id']) {
            $data['rank_id_edited'] = true;
        }
        if (isset($validatedData['address3']) && $membership->address3 !== $validatedData['address3']) {
            $data['address3_edited'] = true;
        }
        if (isset($validatedData['district_id']) && $membership->district_id !== $validatedData['district_id']) {
            $data['district_id_edited'] = true;
        }
        if (isset($validatedData['address']) && $membership->address !== $validatedData['address']) {
            $data['address_edited'] = true;
        }
        if (isset($validatedData['loan10month']) && $membership->loan10month !== $validatedData['loan10month']) {
            $data['loan10month_edited'] = true;
        }
        if (isset($validatedData['account_no']) && $membership->account_no !== $validatedData['account_no']) {
            $data['account_no_edited'] = true;
        }
        if (isset($validatedData['enumber']) && $membership->enumber !== $validatedData['enumber']) {
            $data['enumber_edited'] = true;
        }
        if (isset($validatedData['bank_code']) && $membership->bank_code !== $validatedData['bank_code']) {
            $data['bank_code_edited'] = true;
        }
        if (isset($validatedData['bank_name']) && $membership->bank_name !== $validatedData['bank_name']) {
            $data['bank_name_edited'] = true;
        }
        if (isset($validatedData['branch_code']) && $membership->branch_code !== $validatedData['branch_code']) {
            $data['branch_code_edited'] = true;
        }
        if (isset($validatedData['branch_name']) && $membership->branch_name !== $validatedData['branch_name']) {
            $data['branch_name_edited'] = true;
        }

        MemberEdit::create($data);

        $membership->update($validatedData);

        if ($oldCategoryId != $membership->category_id || $oldRankId != $membership->rank_id ||
            $oldRegimentId != $membership->regiment_id || $oldRegiNumber != $membership->regimental_number ||
            $oldSerialNum != $membership->serial_number || $oldStatusId != $membership->member_status_id ||
            $oldTypeId != $membership->type || $oldUnitId != $membership->unit_id) {

            TransferHistory::create([
                'version' => 1,
                'membership_id' => $membership->id,
                'date_of_transfer' => now(),
                'new_category_id' => $membership->category_id,
                'new_rank_id' => $membership->rank_id,
                'new_regiment_id' => $membership->regiment_id,
                'new_regimental_number' => $membership->regimental_number,
                'new_serial_no' => $membership->serial_number,
                'new_status_id' => $membership->member_status_id,
                'new_type' => $membership->type,
                'new_unit_id' => $membership->unit_id,

                'old_category_id' => $oldCategoryId,
                'old_rank_id' => $oldRankId,
                'old_regiment_id' => $oldRegimentId,
                'old_regimental_number' => $oldRegiNumber,
                'old_serial_no' => $oldSerialNum,
                'old_status_id' => $oldStatusId,
                'old_type' => $oldTypeId,
                'old_unit_id' => $oldUnitId,
            ]);

        }

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);
        $validatedAssign['membership_id'] = $membership->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Alter the member details';
        $validatedAssign['fwd_to_reason'] = 'To check & Approve';
        MembershipAssign::create($validatedAssign);

        return redirect()->route('memberships.show', $id)
            ->with('success', 'Member updated details send for approval.');
    }
    public function approveReject(Request $request, $id)
    {
        $membership = Membership::find($id);

        if (!$membership) {
            return redirect()->route('memberships.index')->with('error', 'Membership not found');
        }

        $action = $request->input('approval');

        if ($action === 'approve') {
            $membership->auth_date = now();
            $membership->acceptedl1 = 1;
            $membership->acceptedl2 = 1;
            $membership->rejectedl2 = 0;
            $membership->altering = 0;
            $membership->accepted_officer_user_namel1 = Auth::user()->name;
            $membership->accepted_officer_user_namel2 = Auth::user()->name;
            $membership->last_modified_date = now();
            $membership->date_acceptedl2 = now();
            $membership->save();

            MemberEdit::where('membership_id',$membership->id)->delete();

            return redirect()->route('memberships.index')->with('message', 'Membership approved successfully');
        } elseif ($action === 'reject') {
            $membership->auth_date = now();
            $membership->acceptedl1 = 0;
            $membership->acceptedl2 = 0;
            $membership->altering = 0;
            $membership->rejectedl2 = 1;
            $membership->accepted_officer_user_namel1 = Auth::user()->name;
            $membership->accepted_officer_user_namel2 = Auth::user()->name;
            $membership->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['membership_id'] = $membership->id;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Rejected';

            $membershipAssign = MembershipAssign::create($validatedAssign);

            return redirect()->route('membership-assigns')->with(['success' => 'Member Rejected',
                'membership' => $membership, 'membershipAssign' => $membershipAssign]);

//            return redirect()->route('membership-assigns')->with('message', 'Member Rejected!');


        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function uploadView()
    {
//        return redirect()->route('monthlyDeductions.index')
//            ->with('success', 'Development in progress');
        return view('memberships.upload');
    }
    public function upload(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml',
            'change_type' => 'required',
        ]);

        $xmlFile = $request->file('xml_file');
        $changeType = $request->input('change_type');
        $xmlData = simplexml_load_file($xmlFile);

        $successes = [];
        $failures = [];

        if ($changeType == '1'){
            dd('1');
        } elseif ($changeType == '2'){
            dd('2');
        } elseif ($changeType == '3'){
            foreach ($xmlData->LIST_G_EMP_NO->G_EMP_NO as $statusChanges) {
                $eNo = (int)$statusChanges->E_NO;
                $regNo = (string)$statusChanges->REGTLNO;
                $state = (string)$statusChanges->STATUS;
                $regNoDigits = preg_replace('/\D/', '', $regNo);

                $member = Membership::where('enumber', $eNo)
                    ->where('regimental_number', $regNoDigits)
                    ->first();
                $status = MemberStatus::where('status_name', $state)
                    ->first();
//dd($member);
                if ($member->exists) {
                    $member->name = (string)$statusChanges->NAME;
                    $member->comment = (string)$statusChanges->EMP_NO;
                    $member->enumber = $eNo;
                    $member->member_status_id = $status->id;
                    $member->pnr_status = $state;
                    $member->created_system = 'AFMS';

                    $member->save();

                    $successes[] = [
                        'enumber' => $eNo,
                        'regimentalNo' => (string)$statusChanges->REGTLNO,
                        'rank' => (string)$statusChanges->RANK,
                        'name' => (string)$statusChanges->NAME,
                        'status' => $state,
                    ];

                    TransferHistory::create([
                        'date_of_transfer' => now(),
                        'membership_id' => $member->id,
                        'version' => 0,
                        'old_status_id' => $member->member_status_id,
                        'new_status_id' => $status->id,
                        'currentuser' => Auth::user()->name,
                    ]);
                } else {
                    $failures[] = [
                        'enumber' => $eNo,
                        'regimentalNo' => (string)$statusChanges->REGTLNO,
                        'rank' => (string)$statusChanges->RANK,
                        'name' => (string)$statusChanges->NAME,
                        'status' => $state,
                        'error' => 'Member not found.',
                    ];
                }
            }

            $failuresArray = json_decode(json_encode($failures), true);
            session(['failures' => $failuresArray]);


            return view('memberships.upload-report', compact('successes', 'failures'));
        } elseif ($changeType == '4'){
            foreach ($xmlData->LIST_G_REGTLNO->G_REGTLNO as $numberChanges) {
                $eNo = (int)$numberChanges->E_NO;
                $oldNo = (string)$numberChanges->PREVIOUSE_REGTLNO;
                $rankNew = (string)$numberChanges->RANK;
                $unitNew = (string)$numberChanges->RANK;

                $member = Membership::where('enumber', $eNo)
                    ->where('regimental_number', $oldNo)
                    ->first();
                $rank = Rank::where('pnr_name', $rankNew)
                    ->first();
                $unit = Unit::where('unit_name', $unitNew)
                    ->first();
dd($member);
                if ($member->exists) {
                    $member->regimental_number = (string)$numberChanges->REGTLNO;
                    $member->rank_id = $rank->id;
                    $member->name = (string)$numberChanges->NAME;
                    $member->comment = (string)$numberChanges->EMP_NO;
                    $member->enumber = (string)$numberChanges->E_NO;
                    $member->created_system = 'AFMS';

                    $member->save();

                    $successes[] = [
                        'enumber' => (string)$numberChanges->E_NO,
                        'oldNo' => $oldNo,
                        'regimentalNo' => (string)$numberChanges->REGTLNO,
                        'rank' => (string)$numberChanges->RANK,
                        'name' => (string)$numberChanges->NAME,
                        'unit' => (string)$numberChanges->UNIT,
                    ];
                } else {
                    $failures[] = [
                        'enumber' => (string)$numberChanges->E_NO,
                        'oldNo' => $oldNo,
                        'regimentalNo' => (string)$numberChanges->REGTLNO,
                        'rank' => (string)$numberChanges->RANK,
                        'name' => (string)$numberChanges->NAME,
                        'unit' => (string)$numberChanges->UNIT,
                        'error' => 'Member not found.',
                    ];
                }
            }

            $failuresArray = json_decode(json_encode($failures), true);

            session(['failures' => $failuresArray]);

            return view('memberships.upload-report', compact('successes', 'failures'));
        } elseif ($changeType == '5'){
            dd('5');
        } else{
            dd('error');
        }

        foreach ($xmlData->LIST_G_REGIMENT->G_REGIMENT as $contribution) {
            $eNo = (int)$contribution->E_NO;
            $regNo = (string)$contribution->OFFRSNO ?: (string)$contribution->REGTLNO;
            $amount = (float)$contribution->AMOUNT;

            $member = Membership::where('enumber', $eNo)
                ->where('regimental_number', $regNo)
                ->firstOrNew();

            if (!$member->exists) {
                $member->name = (string)$contribution->NAME;
                $member->regimental_number = (string)$contribution->OFFRSNO ?: (string)$contribution->REGTLNO;
                $member->comment = (string)$contribution->EMP_NO;
                $member->enumber = (string)$contribution->E_NO;
                $member->created_system = 'AFMS';

                $member->save();
            }

            $contributionData = [
                'membership_id' => $member->id,
                'amount' => $amount,
                'manual' => 0,
                'year' => $depositYear,
                'month' => $depositMonth,
                'currentuser' => Auth::user()->name,
                'created_system' => 'AFMS'
            ];

            // Check if a contribution for the same year and month already exists
            $existingContribution = Contribution::where('membership_id', $member->id)
                ->where('year', $depositYear)
                ->where('month', $depositMonth)
                ->first();

            if ($existingContribution) {
                if ($existingContribution['manual'] === 1) {
                    $existingContribution->update([
                        'amount' => $existingContribution->amount + $contributionData['amount'],
                        'version' => $existingContribution->version + 1,
                        'manual' => $contributionData['manual'],
                        'currentuser' => $contributionData['currentuser'],
                        'created_system' => $contributionData['created_system']
                    ]);
                    $successes[] = [
                        'eNo' => $eNo,
                        'unit' => (string)$contribution->UNIT,
                        'regimentalNo' => (string)$contribution->OFFRSNO ?: (string)$contribution->REGTLNO,
                        'rank' => (string)$contribution->RANK,
                        'name' => (string)$contribution->NAME,
                        'amount' => $amount,
                    ];
                } else {
                    $failures[] = [
                        'eNo' => $eNo,
                        'unit' => (string)$contribution->UNIT,
                        'regimentalNo' => (string)$contribution->OFFRSNO ?: (string)$contribution->REGTLNO,
                        'rank' => (string)$contribution->RANK,
                        'name' => (string)$contribution->NAME,
                        'amount' => $amount,
                        'error' => 'Duplicate entry for the month',
                    ];
                }
            } else {
                // If it doesn't exist, create a new contribution
                Contribution::create($contributionData);
                $successes[] = [
                    'eNo' => $eNo,
                    'unit' => (string)$contribution->UNIT,
                    'regimentalNo' => (string)$contribution->OFFRSNO ?: (string)$contribution->REGTLNO,
                    'rank' => (string)$contribution->RANK,
                    'name' => (string)$contribution->NAME,
                    'amount' => $amount,
                    'currentuser' => Auth::user()->name,
                    'created_system' => 'AFMS'
                ];
            }
        }

        $failuresArray = json_decode(json_encode($failures), true);

        session(['failures' => $failuresArray]);

        return view('monthlyDeductions.upload-report', compact('successes', 'failures'));
    }
    public function destroy($id)
    {
        Membership::findOrFail($id)->delete();
        return redirect()->route('membership-rejects')
            ->with('success','Member deleted successfully');
    }
}
