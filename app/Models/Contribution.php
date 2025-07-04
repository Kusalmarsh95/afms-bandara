<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contribution extends Model
{
    use HasFactory;

    protected $table = 'contribution';
    protected $guarded = [''];
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
}
