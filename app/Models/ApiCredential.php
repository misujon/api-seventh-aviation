<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'api_key',
        'api_secret',
        'base_url',
        'token',
        'status'
    ];
}
