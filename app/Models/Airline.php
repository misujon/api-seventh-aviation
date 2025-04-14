<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airline extends Model
{
    use HasFactory;

    protected $fillable = [
        'iata_code',
        'lcc',
        'name',
        'logo',
        'large_logo',
        'is_featured'
    ];
}
