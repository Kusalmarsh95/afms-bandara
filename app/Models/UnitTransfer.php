<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitTransfer extends Model
{
    use HasFactory;

    protected $table = 'transfer_history';
    protected $guarded = [''];
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function newRegiment()
    {
        return $this->belongsTo(Regiment::class, 'new_regiment_id');
    }
    public function oldRegiment()
    {
        return $this->belongsTo(Regiment::class, 'old_regiment_id');
    }
    public function newUnit()
    {
        return $this->belongsTo(Unit::class, 'new_unit_id');
    }
    public function oldUnit()
    {
        return $this->belongsTo(Unit::class, 'old_unit_id');
    }
    public function newRank()
    {
        return $this->belongsTo(Rank::class, 'new_rank_id');
    }
    public function oldRank()
    {
        return $this->belongsTo(Rank::class, 'old_rank_id');
    }

    public function newCategory()
    {
        return $this->belongsTo(MemberCategory::class, 'new_category_id');
    }
    public function oldCategory()
    {
        return $this->belongsTo(MemberCategory::class, 'old_category_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(MemberStatus::class, 'new_status_id');
    }
    public function oldStatus()
    {
        return $this->belongsTo(MemberStatus::class, 'old_status_id');
    }
}
