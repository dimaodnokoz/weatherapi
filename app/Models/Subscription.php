<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use JetBrains\PhpStorm\ArrayShape;

class Subscription extends Model
{
    // Allowed for mass assignment
    protected $fillable = ['email', 'city', 'frequency'];


    // Not used in this case, because 'confirmed' is not required, thus it is null by default (which is false actually, see casts below)
    /*
    // The model's default values
    protected $attributes = [
        'confirmed' => false,
    ];
    */

    // MySQL may return boolean values as '1' and '0'
    // 'confirmed' is null by default, which means false in our case
    // So we cast it to boolean on model's level to operate with correct boolean in php
    #[ArrayShape(['confirmed' => 'boolean'])]
    protected function casts(): array
    {
        return [
            'confirmed' => 'boolean',
        ];
    }
}
