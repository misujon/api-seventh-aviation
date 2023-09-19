<?php

namespace App\Services;
use GuzzleHttp\Client;
use App\Models\ApiCredential;
use App\Constants\AppConstants;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Log;

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

        return $jsonResponse;
    }
}