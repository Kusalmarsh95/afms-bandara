<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $table = 'rank';
    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(MemberCategory::class);
    }

    public function memberships()
    {
        return $this->belongsToMany(Membership::class);
    }

}
