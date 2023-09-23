<?php

namespace App\Services;

use App\Models\FlightBooking;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FlightService
{
    private $authService;

    public function __construct(AuthService $service)
    {
        $this->authService = $service;
    }

    public function search(
        string $from, 
        string $to, 
        string $departure, 
        string $return = null, 
        int $adult = 1, 
        int $child = 0, 
        int $infant = 0,
        string $tripType = 'oneway'
    )
    {
        $searchId = $this->generateSearchId();
        if (Cache::has('flight-search:'.$searchId))
        {
            return Cache::get('flight-search:'.$searchId);
        }

        $auth = $this->authService->auth();
        if ($auth === false) throw new Exception("Error to communicate with amadeus!");
        
        $client = new Client();
        $headers = [
            'Authorization' => 'Bearer '.$auth->token
        ];

        $query = [
            'originLocationCode' => $from,
            'destinationLocationCode' => $to,
            'departureDate' => $departure,
            'adults' => $adult
        ];

        if (!empty($return) && !is_null($return) && $tripType == "roundtrip") $query['returnDate'] = $return;
        if (!empty($child)) $query['children'] = $child;
        if (!empty($infant)) $query['infants'] = $infant;

        $request = new Request('GET', $auth->base_url.'/v2/shopping/flight-offers?'.http_build_query($query), $headers);
        $res = $client->sendAsync($request)->wait();
        $jsonResponse = json_decode($res->getBody()->getContents(), true);
        $jsonResponse = collect($jsonResponse);
        $jsonResponse->put('searchId', $searchId);

        Cache::put('flight-search:'.$searchId, $jsonResponse);

        return $jsonResponse;
    }

    public function generateSearchId()
    {
        $req = request();
        $dataReq = implode('-', $req->all());
        $clientIp = $req->ip();
        return md5($dataReq.'/'.$clientIp);
    }

    public function pricing(string $searchId, int $flightId, bool $bags = false, int $bagsQty = 0, bool $details = false)
    {
        if (!Cache::has('flight-search:'.$searchId))
        {
            throw new Exception('Error to find flight for pricing!');
        }

        $search = Cache::get('flight-search:'.$searchId);
        $search = collect($search['data'])->filter(function($item) use ($flightId) {
            return $item['id'] == $flightId;
        });

        $auth = $this->authService->auth();
        $client = new Client();
        $headers = [
            'Content-Type' => 'application/json',
            'X-HTTP-Method-Override' => 'GET',
            'Authorization' => 'Bearer '.$auth->token
        ];

        // Checking for baggages.
        if ($bagsQty > 0 && $bags)
        {
            $search = $search->map(function($item) use ($bagsQty) {
                if (isset($item['travelerPricings'][0]) && isset($item['travelerPricings'][0]['fareDetailsBySegment']))
                {
                    foreach($item['travelerPricings'][0]['fareDetailsBySegment'] as $key => $segment)
                    {
                        $item['travelerPricings'][0]['fareDetailsBySegment'][$key]['additionalServices'] = [
                            'chargeableCheckedBags' => [
                                'quantity' => $bagsQty
                            ]
                        ];
                    }
                }
    
                return $item;
            });
        }

        $flightData = $search->first();
        $body = [
            'data' => [
                'type' => 'flight-offers-pricing',
                'flightOffers' => [$flightData]
            ]
        ];

        $body = json_encode($body);
        if ($bags && $details)
        {
            $bagsInclude = '?'.http_build_query(['include' => 'bags,detailed-fare-rules']); 
        }
        elseif ($bags)
        {
            $bagsInclude = '?'.http_build_query(['include' => 'bags']); 
        }
        elseif ($details)
        {
            $bagsInclude = '?'.http_build_query(['include' => 'detailed-fare-rules']); 
        }
        else
        {
            $bagsInclude = '';
        }
        
        $request = new Request('POST', $auth->base_url.'/v1/shopping/flight-offers/pricing'.$bagsInclude, $headers, $body);
        $res = $client->sendAsync($request)->wait();
        $jsonResponse = json_decode($res->getBody()->getContents(), true);

        $flightIdString = md5($searchId.$flightId);
        $jsonResponse = collect($jsonResponse)->map(function($item, $key) use($flightIdString){
            if ($key === "data")
            {
                $item['flightOffers'][0]['flightId'] = $flightIdString;
                ksort($item['flightOffers'][0]);
            }
            return $item;
        });

        $flightDetails = $jsonResponse['data']['flightOffers'];
        $bookingReq = $jsonResponse['data']['bookingRequirements'];

        // Saving the pricing data
        $bookingData = [
            'search_id' => $searchId,
            'flight_id' => (isset($flightDetails[0]))?$flightDetails[0]['id']:"None",
            'flight_id_string' => $flightIdString,
            'price_currency' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['currency']:null,
            'base_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['base']:null,
            'total_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['total']:null,
            'grand_total_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['grandTotal']:null,
            'billing_currency' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['billingCurrency']:null,
            'last_ticketing_date' => (isset($flightDetails[0]))?$flightDetails[0]['lastTicketingDate']:"None",
            'instant_ticketing' => (isset($flightDetails[0]) && $flightDetails[0]['instantTicketingRequired'] == true)?'TRUE':"FALSE",
            'source' => (isset($flightDetails[0]['source']))?$flightDetails[0]['source']:null,
            'itineraries' => (isset($flightDetails[0]['itineraries']))?json_encode($flightDetails[0]['itineraries']):null,
            'pricing' => (isset($flightDetails[0]['price']))?json_encode($flightDetails[0]['price']):null,
            'traveler_pricing' => (isset($flightDetails[0]['travelerPricings']))?json_encode($flightDetails[0]['travelerPricings']):null,
            'booking_requirements' => json_encode($bookingReq),
            'dictionaries' => (isset($jsonResponse['dictionaries']))?json_encode($jsonResponse['dictionaries']):null,
            'fare_rules' => (isset($jsonResponse['included']['detailed-fare-rules']))?json_encode($jsonResponse['included']['detailed-fare-rules']):null,
            'total_response' => json_encode($jsonResponse)
        ];

        FlightBooking::updateOrCreate(
            ['search_id' => $searchId, 'flight_id_string' => $flightIdString],
            $bookingData
        );

        return $jsonResponse;
    }

    public function createOrder()
    {

    }
}