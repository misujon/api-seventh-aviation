<?php

namespace App\Constants;

class AppConstants 
{
    public const AMADEUS_API = 'amadeus';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public static function apiResponse($code, $message, $data = [])
    {
        return [
            'status' => $code,
            'message' => $message,
            'data' => $data
        ];
    }
}