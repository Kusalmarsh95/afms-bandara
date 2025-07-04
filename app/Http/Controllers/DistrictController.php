<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::all();
        return view('districts.index',compact('districts'));

    }
    public function create()
    {
        return view('districts.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'district_name' => 'required|string|unique:district',
        ]);
        $validatedData['version'] = '0';
        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['created_system'] = 'AFMS';

        $district = District::create($validatedData);

        return redirect()->route('districts.index', ['district' => $district])
            ->with('success', 'District created successfully');
    }

    public function edit($id)
    {
        $district = District::find($id);

        return view('districts.edit',compact('district'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'district_name' => 'required',
        ]);

        $district = District::find($id);

        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['version'] = $district->version+1;
        $validatedData['created_system'] = 'AFMS-Update';

        $district->update($validatedData);

        return redirect()->route('districts.index')
            ->with(['success' => 'District updated successfully', 'district' => $district]);
    }

    public function destroy($id)
    {
        District::find($id)->delete();
        return redirect()->route('districts.index')
            ->with('success','District deleted successfully');
    }
}
