<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function scopeClaimer($query) {
      return $query->where('role','claimer');
    }

    public function scopeApprover($query) {
      return $query->where('role','approver');
    }

    public function scopeFinance($query) {
      return $query->where('role','finance');
    }
}
