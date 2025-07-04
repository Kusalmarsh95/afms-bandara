<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;
    protected $table = 'loan';
    protected $guarded = [''];
    public $timestamps = false;

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'loan_id', 'application_reg_no' );
    }

}
