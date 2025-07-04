<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberStatus extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $table = 'member_status';
    public $timestamps = false;

    public function memberships()
    {
        return $this->belongsToMany(Membership::class);
    }
}
