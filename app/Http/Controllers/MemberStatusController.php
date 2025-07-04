<?php

namespace App\Http\Controllers;

use App\Models\MemberStatus;
use Illuminate\Http\Request;

class MemberStatusController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memberStatus = MemberStatus::all();
        return view('memberStatus.index', compact('memberStatus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('memberStatus.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'status_name' => 'required|string|unique:member_status,status_name'
        ]);
        $validatedData['currentuser'] = '(INDIRECT)';
        $memberStatus = MemberStatus::create($validatedData);
        return redirect()->route('member-status.index')->with('success', 'Member Status created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(MemberStatus $memberStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $memberStatus = MemberStatus::find($id);
        return view('memberStatus.edit', compact('memberStatus'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'version' => 'required',
            'status_name' => 'required|string'
        ]);
        $memberStatus = MemberStatus::find($id);
        $validatedData['currentuser'] = "(INDIRECT)";
        $validatedData['version'] = $validatedData['version']  + 1;
        $memberStatus->update($validatedData);
        return redirect()->route('member-status.index')->with('success', 'Member Status updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $memberStatus = MemberStatus::find($id);
        if (!$memberStatus) {
            return redirect()->route('member-status.index')->with('fail', 'Member Status delete failed');
        }
        $memberStatus->delete();
        return redirect()->route('member-status.index')->with('success', 'Member Status deleted successfully');
    }
}
