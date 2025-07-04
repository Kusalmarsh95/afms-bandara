<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;
    protected $table = 'loan_application';
    protected $guarded = [''];
    public $timestamps = false;

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class, 'application_reg_no', 'loan_id');
    }
    public function directSettlement()
    {
        return $this->belongsTo(DirectSettlment::class, 'application_reg_no', 'direct_settlement_id');
    }
    public function absentSettlement()
    {
        return $this->belongsTo(AbsentSettlement::class, 'application_reg_no', 'settlement_id');
    }

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'member_id', 'id');
    }
    public function product()
    {
        return $this->belongsTo(LoanProduct::class, 'product_id', 'id');
    }
    public function rejectReason()
    {
        return $this->belongsTo(RejectReason::class, 'reject_reason_id');
    }
    public function assigns()
    {
        return $this->hasMany(LoanAssign::class, 'loan_id', 'application_reg_no');
    }
    public function settlementAssigns()
    {
        return $this->hasMany(DirectSettlementAssign::class, 'loan_id', 'application_reg_no');
    }
    public function repayment()
    {
        return $this->hasMany(LoanRecoveryPayment::class, 'loan_id', 'application_reg_no');
    }

}
