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
        string $tripType = 'oneway') {
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
        $jsonResponse = collect($jsonResponse)->filter(function ($value, $key) {
            return $key !== "meta";
        })->map(function ($value, $valkey) use (&$jsonResponse) {
            if ($valkey === "data") {
                $value = collect($value)->map(function ($item, $itemkey) {
                    // Only process if 'itineraries' key exists
                    if (isset($item['itineraries']) && is_array($item['itineraries'])) {
                        $item['itineraries'] = collect($item['itineraries'])->map(function ($itinerary) {
                            if (isset($itinerary['segments']) && is_array($itinerary['segments'])) {
                                $itinerary['segments'] = collect($itinerary['segments'])->map(function ($segment) {
                                    $segment['carrierData'] = $this->getAirlineByCode($segment['carrierCode']);
                                    return $segment;
                                })->toArray();
                            }
                            return $itinerary;
                        })->toArray();
                    }
        
                    return $item;
                })->toArray();
        
                $jsonResponse[$valkey] = $value;
            }
            else if ($valkey === "dictionaries")
            {
                if (isset($value['carriers']) && is_array($value['carriers'])) {
                    $value['carriers'] = collect($value['carriers'])->map(function ($val, $key) {
                        return $this->getAirlineByCode($key);
                    })->toArray();
                }
            }
        
            return $value;
        });
        
        $jsonResponse->put('searchId', $searchId);
        $jsonResponse->put('searchType', $tripType);
        $jsonResponse->put('paxAdult', $adult);
        $jsonResponse->put('paxChild', $child);
        $jsonResponse->put('paxInfant', $infant);

        // Cache::put('flight-search:'.$searchId, $jsonResponse, now()->addMinutes(30));

        return $jsonResponse;
    }

    private function getAirlineByCode(string $code)
    {
        $airline = Airline::where('iata_code', $code)->first();
        if (!$airline) return null;
        return $airline->toArray();
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
        $searchType = $search['searchType'];
        $searchPaxAdult = $search['paxAdult'];
        $searchPaxChild = $search['paxChild'];
        $searchPaxInfant = $search['paxInfant'];
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

        if ($searchType == "oneway")
            $flightType = "ONE_WAY";
        elseif ($searchType == "roundtrip")
            $flightType = "ROUND_TRIP";
        else
            $flightType = "MULTI_STOP";

        // Saving the pricing data
        $bookingData = [
            'search_id' => $searchId,
            'flight_id' => (isset($flightDetails[0]))?$flightDetails[0]['id']:"None",
            'flight_id_string' => $flightIdString,
            'trip_type' => $flightType,
            'pax_adults' => $searchPaxAdult,
            'pax_childs' => $searchPaxChild,
            'pax_infants' => $searchPaxInfant,
            'price_currency' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['currency']:null,
            'base_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['base']:null,
            'total_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['total']:null,
            'grand_total_price' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['grandTotal']:null,
            'billing_currency' => (isset($flightDetails[0]['price']))?$flightDetails[0]['price']['billingCurrency']:null,
            'last_ticketing_date' => (isset($flightDetails[0]))?$flightDetails[0]['lastTicketingDate']:"None",
            'instant_ticketing' => (isset($flightDetails[0]) && $flightDetails[0]['instantTicketingRequired'] == true)?'TRUE':"FALSE",
            'source' => (isset($flightDetails[0]['source']))?$flightDetails[0]['source']:null,
            'flight_offers' => (isset($flightDetails))?($flightDetails):null,
            'itineraries' => (isset($flightDetails[0]['itineraries']))?($flightDetails[0]['itineraries']):null,
            'pricing' => (isset($flightDetails[0]['price']))?($flightDetails[0]['price']):null,
            'traveler_pricing' => (isset($flightDetails[0]['travelerPricings']))?($flightDetails[0]['travelerPricings']):null,
            'booking_requirements' => ($bookingReq),
            'dictionaries' => (isset($jsonResponse['dictionaries']))?($jsonResponse['dictionaries']):null,
            'fare_rules' => (isset($jsonResponse['included']['detailed-fare-rules']))?($jsonResponse['included']['detailed-fare-rules']):null,
            'total_response' => ($jsonResponse)
        ];

        if (Auth::check()) $bookingData['customer_id'] = Auth::user()->id;
        FlightBooking::updateOrCreate(
            ['search_id' => $searchId, 'flight_id_string' => $flightIdString],
            $bookingData
        );

        return $jsonResponse;
    }

    public function createOrder(string $searchId, int $flightId, array $passengerData)
    {
        $getPricingData = FlightBooking::where('search_id', $searchId)
                            ->where('flight_id_string', md5($searchId.$flightId))
                            ->where('status', AppConstants::BOOKING_STATUS_PENDING)
                            ->first();
        if (!$getPricingData) throw new Exception('Error to find flight data!');

        $auth = $this->authService->auth();
        $client = new Client();

        $headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$auth->token
        ];

        $flightOffersData = $getPricingData->flight_offers;
        $body = [
            'data' => [
                'type' => 'flight-order',
                'flightOffers' => $flightOffersData,
                'travelers' => $passengerData
            ]
        ];
        $body = json_encode($body);

        $getPricingData->customer_email = $passengerData[0]['contact']['emailAddress'];
        $getPricingData->customer_name = $passengerData[0]['name']['firstName'].' '.$passengerData[0]['name']['lastName'];
        $getPricingData->customer_address = $passengerData[0]['documents'][0]['issuanceLocation'].', '.$passengerData[0]['documents'][0]['issuanceCountry'];
        $getPricingData->customer_city = $passengerData[0]['documents'][0]['issuanceLocation'];
        $getPricingData->customer_state = $passengerData[0]['documents'][0]['issuanceLocation'];
        $getPricingData->customer_country = $passengerData[0]['documents'][0]['issuanceCountry'];
        $getPricingData->customer_phone = $passengerData[0]['contact']['phones'][0]['countryCallingCode'].$passengerData[0]['contact']['phones'][0]['number'];
        $getPricingData->customer_product_category = 'Air Ticket';
        $getPricingData->customer_product_profile = 'airline-tickets';
        $getPricingData->save();

        try
        {
            $request = new Request('POST', $auth->base_url.'/v1/booking/flight-orders', $headers, $body);
            $res = $client->sendAsync($request)->wait();
            $jsonResponse = json_decode($res->getBody()->getContents(), true);

            $getPricingData->pnr = $jsonResponse['data']['associatedRecords'][0]['reference'];
            $getPricingData->booking_id = $jsonResponse['data']['id'];
            $getPricingData->booking_office_id = $jsonResponse['data']['queuingOfficeId'];
            $getPricingData->associated_records = $jsonResponse['data']['associatedRecords'];
            $getPricingData->passengers = $jsonResponse['data']['travelers'];
            $getPricingData->booking_response = $jsonResponse;
            $getPricingData->status = AppConstants::BOOKING_STATUS_BOOKED;
            if (Auth::check()) $getPricingData->customer_id = Auth::user()->id;
            $getPricingData->save();

            return [
                'booking_id' => $getPricingData->booking_id,
                'pnr' => $getPricingData->pnr,
                'flight_info' => $getPricingData->flight_offers,
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

    public function makePayment(string $bookingId): array
    {
        $getBookingData = FlightBooking::where('booking_id', urlencode($bookingId))
                            ->where('status', AppConstants::BOOKING_STATUS_BOOKED)
                            ->first();
        if (!$getBookingData) throw new Exception('Error to find flight data!');

        $post_data = [];
        $post_data['total_amount'] = $getBookingData->grand_total_price; # You cant not pay less than 10
        $post_data['currency'] = $getBookingData->billing_currency;
        $post_data['tran_id'] = $getBookingData->booking_id; // tran_id must be unique

        # CUSTOMER INFORMATION
        $post_data['cus_name'] = $getBookingData->customer_name;
        $post_data['cus_email'] = $getBookingData->customer_email;
        $post_data['cus_add1'] = $getBookingData->customer_address;
        $post_data['cus_city'] = $getBookingData->customer_city;
        $post_data['cus_state'] = $getBookingData->customer_state;
        $post_data['cus_country'] = $getBookingData->customer_country;
        $post_data['cus_phone'] = $getBookingData->customer_phone;

        $post_data['shipping_method'] = "NO";
        $post_data['product_name'] = "Flight Ticket: ".$getBookingData->pnr;
        $post_data['product_category'] = $getBookingData->customer_product_category;
        $post_data['product_profile'] = $getBookingData->customer_product_profile;


        // To finding the flight related data
        $departureDate = $getBookingData->itineraries;
        $futureDate = Carbon::parse($departureDate[0]['segments'][0]['departure']['at']);
        $now = Carbon::now();
        $hours = $now->diffInHours($futureDate);

        $fromFlight = $departureDate[0]['segments'][0]['departure']['iataCode'];
        $toFlight = $departureDate[0]['segments'][(count($departureDate[0]['segments'])-1)]['arrival']['iataCode'];

        $post_data['hours_till_departure'] = $hours." hrs";
        $post_data['flight_type'] = (($getBookingData->trip_type === "MULTI_STOP")?"Multistop":(($getBookingData->trip_type === "ROUND_TRIP")?"Return":"Oneway"));
        $post_data['pnr'] = $getBookingData->pnr;
        $post_data['journey_from_to'] = $fromFlight."-".$toFlight;
        $post_data['third_party_booking'] = "YES";

        $sslc = new SslCommerzNotification();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->makePayment($post_data, 'checkout');
        $payment_options = json_decode($payment_options, true);

        if (!is_array($payment_options) && !isset($payment_options['status']) && $payment_options['status'] != 'success')
        {
            throw new Exception('Error to generate payment url!');
        }

        if (Auth::check()) $getBookingData->customer_id = Auth::user()->id;
        $getBookingData->payment_url = $payment_options['data'];
        $getBookingData->payment_status = AppConstants::PAY_STATUS_PROCESSING;
        $getBookingData->save();

        return $payment_options;
    }

    public function searchAirports($query = null): array
    {
        $airports = Airport::where('status', AppConstants::STATUS_ACTIVE);

        if (!empty($query) && !is_null($query))
        {
            $airports = $airports->where(function($q) use ($query) {
                $q->where('name', 'LIKE', '%'.$query.'%')
                    ->orWhere('code', 'LIKE', '%'.$query.'%')
                    ->orWhere('cityName', 'LIKE', '%'.$query.'%')
                    ->orWhere('countryName', 'LIKE', '%'.$query.'%');
            });
        }

        $airports = $airports->orderBy('is_featured')
            ->paginate(30);
    
        return $airports->toArray();
    }
    
    public function myBookings(): array
    {
        $allColumns = Schema::getColumnListing('flight_bookings');
        $excluded = [
            'total_response', 
            'payment_full_response', 
            'flight_offers', 
            'itineraries', 
            'pricing', 
            'traveler_pricing', 
            'dictionaries',
            'passengers'
        ];
        $columns = array_diff($allColumns, $excluded);

        $bookings = FlightBooking::select($columns)
            ->where('customer_email', Auth::user()->email)
            ->orWhere('customer_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        return $bookings->toArray();
    }
}