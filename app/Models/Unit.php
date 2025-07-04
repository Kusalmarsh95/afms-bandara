<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $guarded = [''];
    protected $table = 'unit';
    public $timestamps = false;

    public function regiment()
    {
        return $this->belongsTo(Regiment::class);
    }
}
