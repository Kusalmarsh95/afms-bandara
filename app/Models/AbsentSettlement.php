<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsentSettlement extends Model
{
    use HasFactory;
    protected $table = 'absent_settlement';
    protected $guarded = [''];
    public $timestamps = false;

}
