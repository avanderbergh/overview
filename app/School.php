<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'id',
        'title',
        'address1',
        'address2',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'phone',
        'user_quota',
        'valid_until',
    ];
}
