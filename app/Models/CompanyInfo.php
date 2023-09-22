<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'company_name',
        'company_email',
        'purpose',
        'device_type',
        'country_calling_code',
        'phone_number',
        'address_street',
        'address_postal',
        'address_city',
        'address_country_code'
    ];
}
