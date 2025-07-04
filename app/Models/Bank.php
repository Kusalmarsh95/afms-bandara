<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;
    protected $guarded = [''];
    protected $table = 'bank';
    public $timestamps = false;
    public function branches() {
        return $this->hasMany(BankBranch::class);
    }
}
