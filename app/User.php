<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'fullname',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function branch () {
        return $this->belongsTo('App\Branch');
    }

    public function company () {
        return $this->belongsTo('App\Company');
    }

    public function accesscharts_map () {
        return $this->belongsToMany('App\AccessChart', 'accesschart_user_maps', 'user_id', 'accesschart_id');
    }

    public function employment () {
        return $this->hasOne('App\UserEmployment');
    }

    public function theme () {
        return $this->hasOne('App\Theme');
    }
    public function dbselection(){
        return $this->hasOne('App\DatabaseSelection', 'id', 'sqldb');
      }
}
