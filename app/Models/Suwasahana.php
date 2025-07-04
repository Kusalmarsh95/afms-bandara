<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suwasahana extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $table = 'suwasahana_loan';
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'member_id', 'id');
    }
    public function recovery()
    {
        return $this->hasMany(SuwasahanaRecovery::class, 'suwasahana_id', 'id');
    }
    public function rejectReason()
    {
        return $this->belongsTo(RejectReason::class, 'reject_reason_id');
    }

}
