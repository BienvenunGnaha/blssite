<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CouponStripe extends Model
{
    protected $fillable = ['user_id', 'percent_off', 'duration', 'duration_in_months', "amount_off", "max_redemptions", "name", "redeem_by", "times_redeemed", "valid"];
}
