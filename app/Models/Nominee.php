<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nominee extends Model
{
    use HasFactory;

    protected $table = 'nominee';
    protected $guarded = [''];
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
    public function relationship()
    {
        return $this->belongsTo(Relationship::class, 'relationship_id');
    }

    public function details()
    {
        return $this->hasOne(NomineeBank::class, 'nominee_id');
    }
}
