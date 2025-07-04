<?php

namespace App\Http\Controllers;

use App\Models\Regiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegimentController extends Controller
{
    public function index()
    {
            $regiments = Regiment::all();
            return view('regiments.index',compact('regiments'));
    }
    public function create()
    {
        return view('regiments.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'regiment_code' => 'required|size:5',
            'regiment_name' => 'required',
        ]);
        $validatedData['version'] = 0;
        $validatedData['currentuser'] = Auth::user()->name;

        $regiment = Regiment::create($validatedData);

        return redirect()->route('regiments.index', ['regiment' => $regiment])
            ->with('message', 'Regiment created successfully');
    }
    public function edit($id)
    {
        $regiment = Regiment::find($id);

        return view('regiments.edit',compact('regiment'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'regiment_code' => 'required|size:5',
            'regiment_name' => 'required',
        ]);
        $validatedData['currentuser'] = Auth::user()->name;

        $regiment = Regiment::find($id);
        $validatedData['version'] = $regiment->version+1;

        $regiment->update($validatedData);

        return redirect()->route('regiments.index')
            ->with(['message' => 'Unit updated successfully', 'regiment' => $regiment]);
    }

    public function destroy($id)
    {
        Regiment::find($id)->delete();
        return redirect()->route('regiments.index')
            ->with('success','Regiment deleted successfully');
    }
}
