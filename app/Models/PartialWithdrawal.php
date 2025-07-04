<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialWithdrawal extends Model
{
    use HasFactory;
    protected $table = 'withdrawal';
    protected $guarded = [''];
    public $timestamps = false;

    public function partialWithdrawalApplication()
    {
        return $this->belongsTo(PartialWithdrawalApplication::class, 'withdrawal_id', 'application_reg_no' );
    }
}
