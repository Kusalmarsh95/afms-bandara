<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferHistory extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $table = 'transfer_history';
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
}
