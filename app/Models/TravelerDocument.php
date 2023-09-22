<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelerDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'traveler_id',
        'type',
        'birth_place',
        'issue_location',
        'issue_date',
        'number',
        'expiry_date',
        'issuance_country',
        'validity_country',
        'nationality'
    ];
}
