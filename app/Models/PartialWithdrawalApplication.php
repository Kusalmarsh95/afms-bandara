<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartialWithdrawalApplication extends Model
{
    use HasFactory;
    protected $table = 'withdrawal_application';
    protected $guarded = [''];
    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function withdrawal()
    {
        return $this->belongsTo(PartialWithdrawal::class, 'application_reg_no', 'withdrawal_id');
    }
    public function membership()
    {
        return $this->belongsTo(Membership::class, 'member_id');
    }
    public function rejectReason()
    {
        return $this->belongsTo(RejectReason::class, 'reject_reason_id');
    }
    public function assigns()
    {
        return $this->hasMany(WithdrawalAssign::class, 'withdrawal_id', 'application_reg_no');
    }

}
