<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use App\Models\Membership;
use App\Models\Nominee;
use App\Models\NomineeAssign;
use App\Models\NomineeBank;
use App\Models\Relationship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NomineeController extends Controller
{
    public function newNominees(){
        $nominees = Nominee::with('relationship')
            ->where('accepted', 0)
            ->get();

        return view('nominees.index',compact('nominees'));
    }
    public function newChanges()
    {
        $nomineeChanges = Nominee::with('relationship')
            ->where('accepted', 3)
            ->get();

        return view('nominees.changes',compact('nomineeChanges'));
    }
    public function newRejects()
    {
        $nomineeRejects = Nominee::with('relationship')
            ->where('accepted', 2)
            ->get();

        return view('nominees.rejects',compact('nomineeRejects'));
    }
    public function create($membership_id)
    {
        $membership = Membership::with('ranks')->find($membership_id);
        $relationships = Relationship::all();
        $users = User::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();

        return view('nominees.create', compact('membership', 'relationships', 'users',
            'banks', 'branches'));
    }
    public function store(Request $request, $membership_id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'nomineenic' => 'required|unique:nominee,nomineenic',
            'relationship_id' => 'required|exists:relationship,id',
            'percentage' => 'required',
            'nominee_address1' => 'nullable',
            'nominee_address2' => 'nullable',
            'nominee_address3' => 'nullable',
        ]);
        $validatedData['version'] = 0;
        $validatedData['membership_id'] = $membership_id;
        $validatedData['accepted'] = 0;
        $validatedData['enabled'] = 1;
        $validatedData['year'] = now();
        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['created_system'] = 'AFMS';

        $nominee = Nominee::create($validatedData);

        $validatedBank = $request->validate([
            'account_number' => 'required',
            'bank_name' => 'required|exists:bank,bank_name',
            'branch_name' => 'required|exists:bank_branch,bank_branch_name',
        ]);
        $validatedBank['version'] = '0';
        $validatedBank['nominee_id'] = $nominee->id;
        NomineeBank::create($validatedBank);

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);
        $validatedAssign['nominee_id'] = $nominee->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Add a new nominee';
        $validatedAssign['fwd_to_reason'] = 'For Approval';
        NomineeAssign::create($validatedAssign);

        return redirect()->route('nominees.newNominees')
            ->with('success', 'Nominee added successfully');
    }
    public function edit($id){

        $nominee = Nominee::with('membership', 'details')->find($id);
        $relationships = Relationship::all();
        $users = User::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();

        return view('nominees.edit',compact('nominee', 'relationships', 'users',
        'banks', 'branches'));

    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'nomineenic' => 'required|unique:nominee,nomineenic',
            'relationship_id' => 'required|exists:relationship,id',
            'percentage' => 'required',
            'nominee_address1' => 'nullable',
            'nominee_address2' => 'nullable',
            'nominee_address3' => 'nullable',
            'enabled' => 'required|in:0,1',
        ]);
        $validatedData['enabled'] = (int) $validatedData['enabled'];
        $validatedData['version'] = 1;
        $validatedData['accepted'] = 3;
        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['created_system'] = 'AFMS-Update';

        $nominee = Nominee::findOrFail($id);

        $nominee->update($validatedData);

        $validatedBank = $request->validate([
            'account_number' => 'required',
            'bank_name' => 'required|exists:bank,bank_name',
            'branch_name' => 'required|exists:bank_branch,bank_branch_name',
        ]);
        $validatedBank['version'] = '0';

        NomineeBank::updateOrCreate(
            ['nominee_id' => $nominee->id],
            $validatedBank
        );

        $validatedAssign = $request->validate([
            'fwd_to' => 'required|exists:users,id',
        ]);
        $validatedAssign['nominee_id'] = $nominee->id;
        $validatedAssign['fwd_by'] = Auth::user()->id;
        $validatedAssign['fwd_by_reason'] = 'Alter the nominee details';
        $validatedAssign['fwd_to_reason'] = 'To check & Approve';
        $nomineeAssign = NomineeAssign::create($validatedAssign);

        return redirect()->route('nominee-changes')
            ->with('success', 'Nominee Edited successfully');
    }
    public function apporvalView($id)
    {
        $nominee = Nominee::with('membership')->find($id);
        $relationships = Relationship::all();
        $users = User::all();
        $banks = Bank::all();
        $branches = BankBranch::select('bank_branch_name')->distinct()->get();

        return view('nominees.approval',compact('nominee', 'relationships', 'users',
            'banks', 'branches'));
    }

    public function approveReject(Request $request, $id)
    {
        $nominee = Nominee::with('membership')->find($id);

        if (!$nominee) {
            return redirect()->route('nominees.newNominees')->with('error', 'Nominee not found');
        }

        $action = $request->input('approval');

        if ($action === 'approve') {
            $nominee->accepted = 1;
            $nominee->currentuser = Auth::user()->name;
            $nominee->save();

            return redirect()->route('nominees.newNominees')->with('success', 'Nominee approved successfully');

        } elseif ($action === 'reject') {
            $nominee->accepted = 2;
            $nominee->currentuser = Auth::user()->name;
            $nominee->save();

            $validatedAssign = $request->validate([
                'fwd_to' => 'required',
                'fwd_to_reason' => 'required',
            ]);
            $validatedAssign['nominee_id'] = $nominee->id;
            $validatedAssign['fwd_by'] = Auth::user()->id;
            $validatedAssign['fwd_by_reason'] = 'Rejected';

            NomineeAssign::create($validatedAssign);

            return redirect()->route('nominees.newNominees')->with(['success' => 'Nominee Rejected']);

        } else {
            return redirect()->back()->with('error', 'Invalid action');
        }
    }
    public function destroy($id)
    {
        Nominee::find($id)->delete();
        return back();
    }
}
