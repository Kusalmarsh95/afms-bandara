<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\BankBranch;
use Illuminate\Http\Request;

class BankBranchController extends Controller
{
    public function index()
    {
        $branches = BankBranch::with('bank')->get();

//        dd($branches);
        return view('bankBranches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $banks = Bank::all();
        return view('bankBranches.create', compact('banks'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'bank_id' => 'required|exists:bank,id',
            'bank_branch_name' => 'required',
            'branch_code' => 'required',
            'contact_details' => ''
        ]);

        $validatedData['version'] = 0;
        $validatedData['created_system'] = "AFMS";

        $bankBranch = BankBranch::create($validatedData);

        return redirect()->route('bank-branches.index', ['bank_branch' => $bankBranch])
            ->with('success', 'Branch Created Successfully');
    }

    public function edit(string $id)
    {
        $branch = BankBranch::with('bank')->find($id);
        return view('bankBranches.edit', compact('branch'));
    }
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'bank_id' => 'required|exists:bank,id',
            'bank_branch_name' => 'required',
            'branch_code' => 'required',
            'contact_details' => '',
            'version' => ''
        ]);

        $validatedData['version'] = $validatedData['version'] + 1;
        $validatedData['created_system'] = "AFMS-updated";
        $branch = BankBranch::find($id);
        $branch->update($validatedData);
        return redirect()->route('bank-branches.index')->with('success', 'Branch Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $branch = BankBranch::find($id);
        if (!$branch) {
            return redirect()->route('bank-branches.index')->with('fail', 'Branch delete failed');
        }
        $branch->delete();
        return redirect()->route('bank-branches.index')->with('success', 'Branch deleted successfully');
    }
}
