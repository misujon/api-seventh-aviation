<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_id',
        'flight_id',
        'flight_id_string',
        'customer_id',
        'customer_email',
        'pnr',
        'booking_id',
        'booking_office_id',
        'associated_records',
        'price_currency',
        'base_price',
        'total_price',
        'grand_total_price',
        'billing_currency',
        'last_ticketing_date',
        'instant_ticketing',
        'source',
        'flight_offers',
        'itineraries',
        'pricing',
        'traveler_pricing',
        'booking_requirements',
        'dictionaries',
        'fare_rules',
        'total_response',
        'passengers',
        'booking_response',
        'status',
        'cancellation_note'
    ];
}
