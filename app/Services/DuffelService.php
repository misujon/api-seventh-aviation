<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Airline;
use App\Models\Airport;
use App\Models\FlightBooking;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Library\SslCommerz\SslCommerzNotification;
use Illuminate\Support\Facades\Schema;
use Misujon\LaravelDuffel\Facades\Duffel;

class DuffelService
{
    private $authService;
    private $payUrl;
    private $payStoreId;
    private $payStorePass;

    public function __construct(AuthService $service)
    {
        $this->authService = $service;
        $this->payStoreId = env('SSLCZ_STORE_ID');
        $this->payStorePass = env('SSLCZ_STORE_PASSWORD');
    }

    public function generateSearchId(): string
    {
        $req = request();
        $result = AppConstants::renderArrayAsKeyValue($req->all());
        $dataReq = implode('-', $result);
        $clientIp = $req->ip();
        return md5($dataReq.'/'.$clientIp);
    }

    public function flightOffers(
        string $from = null, 
        string $to = null, 
        string $departure = null, 
        string $return = null, 
        int $adult = 1, 
        int $child = 0,
        string $tripType = 'oneway',
        array $multistops = null,
        string $cabinClass = 'economy',
    ): array 
    {
        $searchId = $this->generateSearchId();
        if (Cache::has('flight-search-duffel:'.$searchId))
        {
            return Cache::get('flight-search-duffel:'.$searchId);
        }

        $slice = [];
        if ($tripType == "oneway")
        {
            $slice[] = [
                'origin' => $from,
                'destination' => $to,
                'departure_date' => $departure
            ];
        }
        elseif ($tripType == "roundtrip")
        {
            $slice[] = [
                'origin' => $from,
                'destination' => $to,
                'departure_date' => $departure
            ];
            $slice[] = [
                'origin' => $to,
                'destination' => $from,
                'departure_date' => $return
            ];
        }
        elseif ($tripType == "multistop")
        {
            foreach($multistops as $value)
            {
                $from = $value['from'];
                $to = $value['to'];
                $departure = $value['departure'];

                $slice[] = [
                    'origin' => $from,
                    'destination' => $to,
                    'departure_date' => $departure
                ];
            }
        }
        else
        {
            throw new Exception('Error flight search query: trip_type!');
        }

        $passengers = [];
        if ($adult > 0)
        {
            foreach (range(1, $adult) as $i)
            {
                $passengers[] = [
                    'type' => 'adult'
                ];
            }
        }

        if ($child > 0)
        {
            foreach (range(1, $child) as $i)
            {
                $passengers[] = [
                    'type' => 'child'
                ];
            }
        }
        
        $data = [
            "data" => [
                'slices' => $slice,
                'passengers' => $passengers,
                'cabin_class' => $cabinClass,
            ]
        ];

        $result = Duffel::searchFlights($data);
        if ($result && $result['data']) 
        {
            Cache::put('flight-search-duffel:'.$searchId, $result['data'], now()->addMinutes(15));
            return $result['data'];
        }
        
        return [];
    }

    public function flightOffersFetch(string $offerId):array
    {
        $result = Duffel::getFlightOffer($offerId);
        if (!$result || !isset($result['data']) || empty($result['data'])) throw new Exception('Error in flight offer fetch!');

        // dd($result['data']);
        
        return $result['data'];
    }
}