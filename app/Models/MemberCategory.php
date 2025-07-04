<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberCategory extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $table = 'member_category';

    public function ranks()
    {
        return $this->hasMany(Rank::class);
    }
}
