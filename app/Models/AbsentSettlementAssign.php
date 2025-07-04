<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentSettlementAssign extends Model
{
    use HasFactory;
    protected $table = 'absent_settlement_assigns';
    protected $guarded = [''];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'fwd_to');
    }
}
