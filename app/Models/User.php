<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'forward_roles' => 'array',
        'reject_roles' => 'array',
    ];

    public function canForwardRole($role)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($role, $this->forward_roles ?? []);
    }

    public function canRejectRole($role)
    {
        if ($this->isAdmin()) {
            return true;
        }

        return in_array($role, $this->reject_roles ?? []);
    }

    public function isAdmin()
    {
        return $this->hasRole('Super Admin');
    }
}
