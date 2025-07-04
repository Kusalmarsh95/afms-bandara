<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContributionFailures extends Model
{
    use HasFactory;

    protected $table = 'contribution_failures';
    protected $guarded = [''];
}
