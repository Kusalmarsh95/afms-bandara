<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NomineeBank extends Model
{
    use HasFactory;
    protected $table = 'nominee_bank_details';
    protected $guarded = [''];
    public $timestamps = false;

}
