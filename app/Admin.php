<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = [
        'id',
        'school_id',
        'name_first',
        'name_last',
        'name_display',
        'primary_email',
        'picture_url',
    ];
}
