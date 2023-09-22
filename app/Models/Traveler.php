<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traveler extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_id',
        'flight_id',
        'first_name',
        'last_name',
        'gender',
        'dob',
        'email',
        'country_code',
        'phone_number',
    ];
}
