<?php

namespace App\Constants;

use App\Models\Airline;
use App\Models\Airport;

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

    public static function getDestinationAndTime(array $segment, bool $is_raw = false): array
    {
        $data = [];
        foreach ($segment[0]['segments'] as $key => $val)
        {
            $airline = self::getAirlineByCode($val['carrierCode']);
            $depAirpot = self::getDetailsByAirport($val['departure']['iataCode']);
            $arrAirpot = self::getDetailsByAirport($val['arrival']['iataCode']);

            if ($is_raw)
            {
                $data[] = [
                    'departure' => [
                        'cityName' => $depAirpot['cityName'],
                        'country' => $depAirpot['countryName'],
                        'iataCode' => $val['departure']['iataCode'],
                        'at' => date('d/m/Y H:iA', strtotime($val['departure']['at']))
                    ],
                    'arrival' => [
                        'cityName' => $arrAirpot['cityName'],
                        'country' => $arrAirpot['countryName'],
                        'iataCode' => $val['arrival']['iataCode'],
                        'at' => date('d/m/Y H:iA', strtotime($val['arrival']['at']))
                    ],
                    'airline' => [
                        'name' => $airline['name'],
                        'logo' => $airline['logo'],
                        'iata' => $airline['iata_code'],
                    ]
                ];
            }
            else
            {
                $airports = "<div class='details-itinerary'>";
                $airports .= "<p><strong>Destination:</strong> <span class='badge badge-xs badge-warning badge-outline shrink-0'>".$depAirpot['cityName']." (".$val['departure']['iataCode'] . ")</span> - <span class='badge badge-xs badge-primary badge-outline shrink-0'>" . $arrAirpot['cityName']." (".$val['arrival']['iataCode'] . ")</span></p>";
                $airports .= "<strong>Departure At:</strong> ".date('d/m/Y H:iA', strtotime($val['departure']['at'])) . "<br/>";
                $airports .= "<strong>Arrival At: </strong>".date('d/m/Y H:iA', strtotime($val['arrival']['at'])) . "<br/>";
                $airports .= "<p class='flex'><label><strong>Carrier: </strong> ".$airline['name']."</label> <img class='w-8 ps-2' src='".$airline['logo']."'/></p>";
                $airports .= "</div>";
                $data[] = $airports;
            }
            
        }

        return $data;
    }

    public static function getAirlineByCode(string $code)
    {
        $airline = Airline::where('iata_code', $code)->first();
        if (!$airline) return null;
        return $airline->toArray();
    }

    public static function getDetailsByAirport(string $code)
    {
        $airline = Airport::where('code', $code)->first();
        if (!$airline) return null;
        return $airline->toArray();
    }

    public static function renderArrayAsTable($data, $prefix = '')
    {
        $html = "";
        foreach ($data as $key => $value) {
            $fullKey = $prefix ? $prefix . '.' . $key : $key;
    
            if (is_array($value)) {
                // If it's associative or has nested arrays
                $html .= "<li class='flex justify-between border-b pb-1'><strong>{$fullKey}</strong></li>";
                $html .= self::renderArrayAsTable($value, $fullKey); // Append recursive results
            } else {
                if (strlen($value) < 100)
                {
                    
                }
                $html .= "<li class='flex justify-between border-b pb-1'><span class='font-bold'>{$fullKey}:</span> &nbsp;<span class='overflow-wrap'>{$value}</span></li>";
            }
        }

        return $html;
    }
}