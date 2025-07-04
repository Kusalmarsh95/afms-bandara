<?php

namespace App\Http\Controllers;

use App\Models\ContributionInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionInterestController extends Controller
{
    public function index()
    {
        $contributionInterests = ContributionInterest::all();
        return view('contributionInterests.index',compact('contributionInterests'));
    }
    public function create()
    {
        return view('contributionInterests.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'year' => 'required',
            'icp_id' => 'required',
            'interest_rate' => 'required',
            'status' => 'required',
        ]);
        $validatedData['version'] = 0;
        $validatedData['created_system'] = 'AFMS';
        $validatedData['created_by'] = Auth::user()->name;

        ContributionInterest::create($validatedData);

        return redirect()->route('contribution-interests.index')
            ->with('message', 'Interest rate created successfully');
    }
    public function edit($id)
    {
        $contributionInterest = ContributionInterest::find($id);
        return view('contributionInterests.edit',compact('contributionInterest'));
    }

    public function update(Request $request, $id)
    {
        $contributionInterest = ContributionInterest::find($id);

        $validatedData = $request->validate([
            'year' => 'required',
            'icp_id' => 'required',
            'interest_rate' => 'required',
            'status' => 'required',
        ]);
        $validatedData['version'] = $contributionInterest->version + 1;
        $validatedData['created_system'] = 'AFMS';
        $validatedData['created_by'] = Auth::user()->name;

        $contributionInterest->update($validatedData);

        return redirect()->route('contribution-interests.index')
            ->with('message', 'Interest rate created successfully');
    }

    public function destroy($id)
    {
        ContributionInterest::find($id)->delete();

        return redirect()->route('contribution-interests.index')
            ->with('success','Interest rate deleted successfully');
    }
}
