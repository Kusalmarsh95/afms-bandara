<?php

namespace App\Http\Controllers;

use App\Models\WithdrawalProduct;
use Illuminate\Http\Request;

class WithdrawalProductController extends Controller
{
    public function index()
    {
        $withdrawalProducts = WithdrawalProduct::all();
        return view('withdrawalProducts.index',compact('withdrawalProducts'));
    }
    public function create()
    {
        return view('withdrawalProducts.create');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'percentage' => 'required|numeric',
            'male_service' => 'nullable',
            'female_service' => 'nullable',
            'status' => 'required',
        ]);
        $validatedData['created_system'] = 'AFMS';

        WithdrawalProduct::create($validatedData);

        return redirect()->route('withdrawal-products.index')
            ->with('success', 'Withdrawal Product created successfully');
    }

    public function edit($id)
    {
        $withdrawalProduct = WithdrawalProduct::find($id);
        return view('withdrawalProducts.edit', compact('withdrawalProduct'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'percentage' => 'required|numeric',
            'male_service' => 'nullable',
            'female_service' => 'nullable',
            'status' => 'required',
        ]);
        $validatedData['created_system'] = 'AFMS';

        $withdrawalProduct = WithdrawalProduct::find($id);

        $withdrawalProduct->update($validatedData);

        return redirect()->route('withdrawal-products.index')
            ->with('success', 'Withdrawal Product updated successfully');
    }

    public function destroy($id)
    {
        WithdrawalProduct::find($id)->delete();

        return redirect()->route('withdrawal-products.index')
            ->with('success','Withdrawal Product deleted successfully');
    }
}
