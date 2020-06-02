<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "start_date","hour_date","email"
    ];
}
