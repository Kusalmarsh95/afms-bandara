<?php

namespace App\Http\Controllers;

use App\Models\Relationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RelationshipController extends Controller
{
    public function index()
    {
        $relationships = Relationship::all();
        return view('relationships.index',compact('relationships'));
    }
    public function create()
    {
        return view('relationships.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'relationship_name' => 'required|unique:relationship,relationship_name',
        ]);
        $validatedData['version'] = '0';
        $validatedData['currentuser'] = Auth::user()->name;

        $relationship = Relationship::create($validatedData);

        return redirect()->route('relationships.index', ['relationship' => $relationship])
            ->with('success', 'Relationship created successfully');

    }

    public function edit($id)
    {
        $relationship = Relationship::find($id);

        return view('relationships.edit',compact('relationship'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'relationship_name' => 'required',
        ]);

        $relationship = Relationship::find($id);

        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['version'] = $relationship->version+1;

        $relationship->update($validatedData);

        return redirect()->route('relationships.index')
            ->with(['success' => 'Relationship updated successfully', 'relationship' => $relationship]);
    }

    public function destroy($id)
    {
        Relationship::find($id)->delete();
        return redirect()->route('relationships.index')
            ->with('success','Relationship deleted successfully');
    }
}
