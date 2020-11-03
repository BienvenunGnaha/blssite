<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pronostic extends Model
{
    protected $fillable = ['details','button','button_fr', 'conseil', 'conseil_fr'];
}
