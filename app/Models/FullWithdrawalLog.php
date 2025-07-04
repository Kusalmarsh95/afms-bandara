<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FullWithdrawalLog extends Model
{
    use HasFactory;
    protected $table = 'full_withdrawal_log';
    protected $guarded = [''];
    public $timestamps = false;
}
