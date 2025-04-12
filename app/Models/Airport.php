<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'cityCode',
        'cityName',
        'countryName',
        'countryCode',
        'timezone',
        'is_featured',
        'lat',
        'lon',
        'numAirports',
        'city',
        'status'
    ];
}
