<?php

namespace App\Http\Controllers;

use App\Models\AbsentHistory;
use App\Models\Membership;
use Illuminate\Http\Request;

class AbsentHistoryController extends Controller
{
    public function create($membership_id)
    {
        $membership = Membership::with('ranks')->find($membership_id);
        return view('absents.create', compact('membership'));

    }
    public function store(Request $request, $membership_id)
    {
        $validatedData = $request->validate([
            'from' => 'required',
            'to' => 'required',
            'days' => 'required',
        ]);
        $validatedData['membership_id'] = $membership_id;

        AbsentHistory::create($validatedData);

        return redirect()->route('memberships.show', $membership_id)
            ->with('success', 'Absent details added successfully');
    }
    public function edit($id)
    {
        $absent = AbsentHistory::with('membership')->find($id);
        return view('absents.edit', compact('absent'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'from' => 'required',
            'to' => 'required',
            'days' => 'required',
        ]);

        $absent = AbsentHistory::find($id);

        $absent->update($validatedData);

        return redirect()->route('memberships.show', $absent->membership_id)
            ->with('success', 'Absent details updated successfully');
    }
    public function destroy(string $id)
    {
        AbsentHistory::find($id)->delete();
        return back();
    }
}
