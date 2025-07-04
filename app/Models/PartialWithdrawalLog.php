<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialWithdrawalLog extends Model
{
    use HasFactory;
    protected $table = 'withdrawal_log';
    protected $guarded = [''];
    public $timestamps = false;
}
