<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\FlightBooking;
use GuzzleHttp\Client;
use Exception;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Library\SslCommerz\SslCommerzNotification;

class FlightService
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
        $jsonResponse = collect($jsonResponse)->filter(function($value, $key){
            return $key !== "meta";
        });
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
            'flight_offers' => (isset($flightDetails))?json_encode($flightDetails):null,
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

    public function createOrder(string $searchId, int $flightId, array $passengerData)
    {
        $getPricingData = FlightBooking::where('search_id', $searchId)->where('flight_id_string', md5($searchId.$flightId))->first();
        if (!$getPricingData) throw new Exception('Error to find flight data!');

        $auth = $this->authService->auth();
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$auth->token
        ];

        $flightOffersData = json_decode($getPricingData->flight_offers, true);
        $body = [
            'data' => [
                'type' => 'flight-order',
                'flightOffers' => $flightOffersData,
                'travelers' => $passengerData
            ]
        ];
        $body = json_encode($body);

        try
        {
            $request = new Request('POST', $auth->base_url.'/v1/booking/flight-orders', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $jsonResponse = json_decode($res->getBody()->getContents(), true);

            $getPricingData->pnr = $jsonResponse['data']['associatedRecords'][0]['reference'];
            $getPricingData->booking_id = $jsonResponse['data']['id'];
            $getPricingData->booking_office_id = $jsonResponse['data']['queuingOfficeId'];
            $getPricingData->associated_records = json_encode($jsonResponse['data']['associatedRecords']);
            $getPricingData->passengers = json_encode($jsonResponse['data']['travelers']);
            $getPricingData->booking_response = json_encode($jsonResponse);
            $getPricingData->status = AppConstants::BOOKING_STATUS_BOOKED;
            $getPricingData->save();

            return [
                'booking_id' => $getPricingData->booking_id,
                'pnr' => $getPricingData->pnr,
                'flight_info' => json_decode($getPricingData->flight_offers, true),
                'source' => $getPricingData->source,
                'total_price' => $getPricingData->grand_total_price,
                'currency' => $getPricingData->billing_currency,
                'instant_ticketing' => $getPricingData->instant_ticketing,
                'status' => $getPricingData->status,
                'created_at' => $getPricingData->updated_at
            ];
        }
        catch(Exception $e)
        {
            throw new Exception($e->getMessage());
        }
    }

    public function makePayment(string $searchId, int $flightId): array
    {
        $getBookingData = FlightBooking::where('search_id', $searchId)
                            ->where('flight_id_string', md5($searchId.$flightId))
                            ->where('status', AppConstants::BOOKING_STATUS_BOOKED)
                            ->first();
        if (!$getBookingData) throw new Exception('Error to find flight data!');

        // dd($getBookingData->passengers);

        $post_data = [];
        $post_data['total_amount'] = $getBookingData->grand_total_price; # You cant not pay less than 10
        $post_data['currency'] = $getBookingData->billing_currency;
        $post_data['tran_id'] = $getBookingData->booking_id; // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $getBookingData->passengers[0]['name']['firstName'].' '.$getBookingData->passengers[0]['name']['lastName'];
        $post_data['cus_email'] = $getBookingData->passengers[0]['contact']['emailAddress'];
        $post_data['cus_add1'] = $getBookingData->passengers[0]['documents'][0]['issuanceLocation'].', '.$getBookingData->passengers[0]['documents'][0]['issuanceCountry'];
        $post_data['cus_city'] = $getBookingData->passengers[0]['documents'][0]['issuanceLocation'];
        $post_data['cus_state'] = $getBookingData->passengers[0]['documents'][0]['issuanceLocation'];
        $post_data['cus_country'] = "Bangladesh";
        $post_data['cus_phone'] = $getBookingData->passengers[0]['contact']['phones'][0]['countryCallingCode'].$getBookingData->passengers[0]['contact']['phones'][0]['number'];

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Flight Ticket: ".$getBookingData->pnr;
        $post_data['product_category'] = "Air Ticket";
        $post_data['product_profile'] = "general";

        $post_data['success_url'] = "http://localhost:8000/success";
        $post_data['fail_url'] = "http://localhost:8000/fail";
        $post_data['cancel_url'] = "http://localhost:8000/cancel";

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout');

        return json_decode($payment_options, true);
    }
}