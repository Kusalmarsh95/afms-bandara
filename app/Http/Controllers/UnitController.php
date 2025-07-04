<?php

namespace App\Http\Controllers;

use App\Models\Regiment;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function index()
    {

        $units = Unit::with('regiment')->get();
        return view('units.index',compact('units'));
    }

    public function create()
    {
        $regiments = Regiment::all();

        return view('units.create',compact('regiments'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'regiment_id' => 'required|exists:regiment,id',
            'unit_code' => 'required',
            'unit_name' => 'required',
        ]);
        $validatedData['version'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;

        $unit = Unit::create($validatedData);

        return redirect()->route('units.index', ['unit' => $unit])
            ->with('message', 'Unit created successfully');
    }

    public function edit($id)
    {
        $unit = Unit::find($id);
        $regiments = Regiment::all();

        return view('units.edit',compact('unit','regiments'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'regiment_id' => 'required|exists:regiment,id',
            'unit_name' => 'required',
        ]);
        $validatedData['unit_code'] = $request->unit_name;
        $validatedData['currentuser'] = Auth::user()->name;

        $unit = Unit::find($id);

        $unit->update($validatedData);

        return redirect()->route('units.index')
            ->with(['message' => 'Unit updated successfully', 'unit' => $unit]);
    }

    public function destroy($id)
    {
        Unit::find($id)->delete();
        return redirect()->route('units.index')
            ->with('success','Unit deleted successfully');
    }
}
