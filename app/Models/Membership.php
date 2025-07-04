<?php

namespace App\Models;

use App\Http\Controllers\DistrictController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $table = 'membership';
    protected $guarded = [''];
    public $timestamps = false;

    public function ranks()
    {
        return $this->belongsTo(Rank::class, 'rank_id');
    }

    public function regiments()
    {
        return $this->belongsTo(Regiment::class, 'regiment_id');
    }

    public function units()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function category()
    {
        return $this->belongsTo(MemberCategory::class, 'category_id');
    }

    public function status()
    {
        return $this->belongsTo(MemberStatus::class, 'member_status_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function transfers()
    {
        return $this->hasMany(UnitTransfer::class, 'membership_id');
    }
    public function nominees()
    {
        return $this->hasMany(Nominee::class, 'membership_id');
    }
    public function contributions()
    {
        return $this->hasMany(Contribution::class, 'membership_id');
    }
    public function contributionsHistory()
    {
        return $this->hasMany(ContributionHistory::class, 'membership_id');
    }
    public function contributionsSummary()
    {
        return $this->hasMany(ContributionSummary::class, 'membership_id');
    }
    public function fullWithdrawalApplication()
    {
        return $this->hasMany(FullWithdrawalApplication::class, 'member_id');
    }
    public function partialWithdrawalApplication()
    {
        return $this->hasMany(PartialWithdrawalApplication::class, 'member_id');
    }
    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class, 'member_id');
    }
    public function suwasahana()
    {
        return $this->hasMany(Suwasahana::class, 'member_id');
    }
    public function absents()
    {
        return $this->hasMany(AbsentHistory::class, 'membership_id');
    }
    /*public function assign()
    {
        return $this->belongsTo(MembershipAssign::class, 'membership_id');
    }*/
}
