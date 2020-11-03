<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Twofa extends Model
{
    protected $fillable = ['token'];
}
