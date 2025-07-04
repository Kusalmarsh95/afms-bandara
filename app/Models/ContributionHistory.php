<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionHistory extends Model
{
    use HasFactory;

    protected $table = 'contribution_history';
    protected $guarded = [''];
    public $timestamps = false;

    public function membership()
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }
}
