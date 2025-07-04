<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBranch extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $table = 'bank_branch';
    public $timestamps = false;

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
