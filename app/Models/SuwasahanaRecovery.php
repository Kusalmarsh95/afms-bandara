<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuwasahanaRecovery extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $table = 'suwasahana_loan_recovery';
    public $timestamps = false;

}
