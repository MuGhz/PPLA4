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
        'name', 'email', 'password','role','company'
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

    public function scopeApprover($query,$claimer) {
      $approvers = $query->where('company',$claimer->company)->where('role','approver')
                        ->where('id','!=',$claimer->id)
                        ->get();
      // dd($approvers);
      return $approvers[rand(0,count($approvers)-1)];
    }

    public function scopeFinance($query, $claimer) {
      $finances =  $query->where('company',$claimer->company)->where('role','finance')
                        ->where('id','!=',$claimer->id)
                        ->get();
                        
      return $finances[rand(0,count($finances)-1)];
    }
}
