<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectSettlment extends Model
{
    use HasFactory;

    protected $table = 'direct_settlement';
    protected $guarded = [''];
    public $timestamps = false;

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class, 'direct_settlement_id', 'application_reg_no' );
    }
}
