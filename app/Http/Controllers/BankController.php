<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::all();
//        dd($banks);
        return view('banks.index', compact('banks'));
    }

    public function create()
    {
        return view('banks.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required|unique:bank,id',
            'bank_name' => 'required|unique:bank,bank_name',
        ]);
        $validatedData['version'] = 0;
        $validatedData['created_system'] = 'AFMS';

        $bank = Bank::create($validatedData);

        return redirect()->route('banks.index', ['bank' => $bank])
            ->with('success', 'Bank created successfully');
    }

    public function edit($id)
    {
        $bank = Bank::find($id);

        return view('banks.edit',compact('bank'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'bank_name' => 'required',
        ]);

        $bank = Bank::find($id);

        $validatedData['version'] = $bank->version+1;
        $validatedData['created_system'] = 'AFMS-Update';

        $bank->update($validatedData);

        return redirect()->route('banks.index')
            ->with(['success' => 'Bank updated successfully', 'bank' => $bank]);
    }

    public function destroy($id)
    {
        Bank::find($id)->delete();
        return redirect()->route('banks.index')
            ->with('success','Bank deleted successfully');
    }
}
