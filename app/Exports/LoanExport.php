<?php

namespace App\Exports;

use App\Models\Loan;
use Maatwebsite\Excel\Concerns\FromCollection;

class LoanExport implements FromCollection
{
    public function collection()
    {
        return Loan::all();
    }
}
