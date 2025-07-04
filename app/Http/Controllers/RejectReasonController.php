<?php

namespace App\Http\Controllers;

use App\Models\RejectReason;
use Illuminate\Http\Request;

class RejectReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rejectReasons = RejectReason::all();
        return view('rejectReason.index', compact('rejectReasons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rejectReason.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'reason_name' => 'required|string'
        ]);
        $validatedData['version'] = '0';
        $rejectReason = RejectReason::create($validatedData);
        return redirect()->route('reject-reasons.index')->with('success', 'Reject Reason created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(RejectReason $rejectReason)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $rejectReason = RejectReason::find($id);
        return view('rejectReason.edit', compact('rejectReason'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'version' => 'required|numeric',
            'reason_name' => 'required|string',
        ]);
        $rejectReason = RejectReason::find($id);
        $validatedData['version'] = $rejectReason->version + 1;
        $rejectReason->update($validatedData);
        return redirect()->route('reject-reasons.index')->with('success', 'Reject Reason updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $rejectReason = RejectReason::find($id);
        if (!$rejectReason) {
            return redirect()->route('reject-reasons.index')->with('fail', 'Reject Reason delete failed');
        }
        $rejectReason->delete();
        return redirect()->route('reject-reasons.index')->with('success', 'Reject Reason deleted successfully');
    }
}
