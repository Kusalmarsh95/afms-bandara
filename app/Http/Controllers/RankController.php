<?php

namespace App\Http\Controllers;

use App\Models\MemberCategory;
use App\Models\Rank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankController extends Controller
{
    public function index()
    {
        $ranks = Rank::with('category')->get();
        return view('ranks.index',compact('ranks'));

    }
    public function create()
    {
        $categories = MemberCategory::all();

        return view('ranks.create',compact('categories'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'rank_name' => 'required|unique:rank,rank_name',
            'category_id' => 'required|exists:member_category,id',
        ]);
        $validatedData['version'] = '0';
        $validatedData['currentuser'] = Auth::user()->name;

        $rank = Rank::create($validatedData);

        return redirect()->route('ranks.index', ['rank' => $rank])
            ->with('message', 'Rank created successfully');
    }

    public function edit($id)
    {
        $rank = Rank::find($id);
        $categories = MemberCategory::all();

        return view('ranks.edit',compact('rank','categories'));
    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'rank_name' => 'required',
            'category_id' => 'required|exists:member_category,id',
        ]);

        $rank = Rank::find($id);

        $validatedData['currentuser'] = Auth::user()->name;
        $validatedData['version'] = $rank->version+1;

        $rank->update($validatedData);

        return redirect()->route('ranks.index')
            ->with(['success' => 'Rank updated successfully']);
    }

    public function destroy($id)
    {
        Rank::find($id)->delete();

        return redirect()->route('ranks.index')
            ->with('success','Rank deleted successfully');
    }
}
