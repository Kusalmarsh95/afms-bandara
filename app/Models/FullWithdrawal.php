<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullWithdrawal extends Model
{
    use HasFactory;
    protected $table = 'full_withdrawal';
    protected $guarded = [''];
    public $timestamps = false;

    public function fullWithdrawalApplication()
    {
        return $this->belongsTo(FullWithdrawalApplication::class,'withdrawal_id', 'application_reg_no');
    }
}
