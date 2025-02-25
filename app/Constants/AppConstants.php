<?php

namespace App\Constants;

class AppConstants 
{
    public const AMADEUS_API = 'amadeus';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const BOOKING_STATUS_PENDING = 'PENDING';
    public const BOOKING_STATUS_BOOKED = 'BOOKED';
    public const BOOKING_STATUS_TICKETED = 'TICKETED';
    public const BOOKING_STATUS_CANCELLED = 'CANCELLED';

    public static function apiResponse($code, $message, $data = [])
    {
        return [
            'status' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}