<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// JWT contract
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'address' => 'object',
        'ver_code_send_at' => 'datetime'
    ];

    public function login_logs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function transactions()
    {
        return $this->hasMany(Trx::class);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    // SCOPES

    public function getFullnameAttribute()
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function scopeActive()
    {
        return $this->where('status', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeSmsUnverified()
    {
        return $this->where('sv', 0);
    }


    public function my_level(){
        return $this->hasOne(Plan::class,'id', 'plan_id')->withDefault();
    }

    public function last_login(){
        return $this->hasOne(UserLogin::class, 'user_id','id')->latest()->first();
    }

    public function scopeInactive($query){
        return $query->where('status',0)->latest();
    }

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }


}
