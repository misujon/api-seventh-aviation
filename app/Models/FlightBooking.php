<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class FlightBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'search_id',
        'flight_id',
        'flight_id_string',
        'trip_type',
        'pax_adults',
        'pax_childs',
        'pax_kids',
        'pax_infants',
        'customer_id',
        'customer_email',
        'customer_name',
        'customer_address',
        'customer_city',
        'customer_state',
        'customer_postcode',
        'customer_country',
        'customer_phone',
        'customer_product_name',
        'customer_product_category',
        'customer_product_profile',
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
        'payment_status',
        'payment_id',
        'payment_url',
        'payment_response',
        'payment_failed_reason',
        'payment_session',
        'payment_full_response',
        'cancellation_note'
    ];

    protected $casts = [
        'flight_offers' => 'array',
        'itineraries' => 'array',
        'pricing' => 'array',
        'traveler_pricing' => 'array',
        'booking_requirements' => 'array',
        'dictionaries' => 'array',
        'fare_rules' => 'array',
        'total_response' => 'array',
        'passengers' => 'array',
        'booking_response' => 'array',
        'associated_records' => 'array',
        'payment_full_response' => 'array',
        'payment_response' => 'array',
        'payment_failed_reason' => 'array',
    ];
}
