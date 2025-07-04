<?php

namespace App\Http\Controllers;

use App\Models\LoanProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanProductController extends Controller
{
    public function index()
    {
        $loanProducts = LoanProduct::all();
        return view('loanProducts.index',compact('loanProducts'));
    }
    public function create()
    {
        return view('loanProducts.create');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'percentage' => 'required|numeric',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'status' => 'required',
        ]);
        $validatedData['created_by'] = Auth::user()->name;
        $validatedData['created_system'] = 'AFMS';

        LoanProduct::create($validatedData);

        return redirect()->route('loan-products.index')
            ->with('success', 'Loan Product created successfully');
    }
    public function edit($id)
    {
        $loanProduct = LoanProduct::find($id);
        return view('loanProducts.edit', compact('loanProduct'));

    }
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'percentage' => 'required',
            'interest_rate' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'nullable',
            'status' => 'required',
        ]);
        $validatedData['created_by'] = Auth::user()->name;
        $validatedData['created_system'] = 'AFMS-Update';

        $loanProduct = LoanProduct::find($id);
        $loanProduct->update($validatedData);

        return redirect()->route('loan-products.index')
            ->with('success', 'Loan Product updated successfully');
    }
    public function destroy($id)
    {
        LoanProduct::find($id)->delete();

        return redirect()->route('loan-products.index')
            ->with('success','Loan product deleted successfully');
    }
}
