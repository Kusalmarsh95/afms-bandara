<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecentContribution extends Model
{
    use HasFactory;
    protected $guarded = [''];

    public function regiments()
    {
        return $this->belongsTo(Regiment::class, 'regiment_id');
    }
    public function category()
    {
        return $this->belongsTo(MemberCategory::class, 'category_id');
    }
}
