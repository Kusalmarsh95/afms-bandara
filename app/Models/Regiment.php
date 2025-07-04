<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regiment extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $table = 'regiment';
    public $timestamps = false;

    public function memberships()
    {
        return $this->belongsToMany(Membership::class);
    }

}
