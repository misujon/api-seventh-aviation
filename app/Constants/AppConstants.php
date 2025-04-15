<?php

namespace App\Constants;

use App\Models\Airline;

class AppConstants 
{
    public const AMADEUS_API = 'amadeus';
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_INACTIVE = 'INACTIVE';

    public const BOOKING_STATUS_PENDING = 'PENDING';
    public const BOOKING_STATUS_BOOKED = 'BOOKED';
    public const BOOKING_STATUS_TICKETED = 'TICKETED';
    public const BOOKING_STATUS_CANCELLED = 'CANCELLED';

    public const PAY_STATUS_PENDING = 'PENDING';
    public const PAY_STATUS_PROCESSING = 'PROCESSING';
    public const PAY_STATUS_SUCCESS = 'SUCCESS';
    public const PAY_STATUS_FAILED = 'FAILED';
    public const PAY_STATUS_COMPLETE = 'COMPLETE';

    public static function apiResponse($code, $message, $data = [])
    {
        return [
            'status' => $code,
            'message' => $message,
            'data' => $data
        ];
    }

    public static function getDestinationAndTime(array $segment): array
    {
        $data = [];
        foreach ($segment[0]['segments'] as $key => $val)
        {
            $airports = "<div class='details-itinerary'>";
            $airports .= "<p><strong>Destination:</strong> <span class='badge badge-xs badge-warning badge-outline shrink-0'>".$val['departure']['iataCode'] . "</span> - <span class='badge badge-xs badge-primary badge-outline shrink-0'>" . $val['arrival']['iataCode'] . "</span></p>";
            $airports .= "<strong>Departure At:</strong> ".date('d/m/Y H:iA', strtotime($val['departure']['at'])) . "<br/>";
            $airports .= "<strong>Arrival At:</strong>".date('d/m/Y H:iA', strtotime($val['arrival']['at'])) . "<br/>";
            $airports .= "<strong>Carrier:</strong> ".self::getAirlineByCode($val['carrierCode'])['name'];
            $airports .= "</div>";
            $data[] = $airports;
        }

        return $data;
    }

    public static function getAirlineByCode(string $code)
    {
        $airline = Airline::where('iata_code', $code)->first();
        if (!$airline) return null;
        return $airline->toArray();
    }
}