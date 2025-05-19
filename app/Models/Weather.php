<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    // Allowed for mass assignment
    protected $fillable = ['temperature', 'humidity', 'description'];
}
