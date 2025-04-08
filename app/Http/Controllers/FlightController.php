<?php

namespace App\Http\Controllers;

use App\Http\Requests\PricingRequest;
use Exception;
use Illuminate\Http\Request;
use App\Constants\AppConstants;
use App\Http\Requests\BookingRequest;
use App\Services\FlightService;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\SearchRequest;

class FlightController extends Controller
{
    private $flightService;

    public function __construct(FlightService $service)
    {
        $this->flightService = $service;
    }

    public function search(SearchRequest $request)
    {
        try
        {
            $result = $this->flightService->search(
                $request->from,
                $request->to,
                $request->departure,
                (!empty($request->arrival))?$request->return:null,
                (!empty($request->adult))?$request->adult:1,
                (!empty($request->child))?$request->child:0,
                (!empty($request->infant))?$request->infant:0,
                $request->trip_type
            );

            return AppConstants::apiResponse(200, 'Flight search result!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in search.', [$e]);
            return AppConstants::apiResponse(404, 'No flight found!');
        }
    }

    public function pricing($searchId, $id, PricingRequest $request)
    {
        try
        {
            $result = $this->flightService->pricing(
                $searchId,
                $id,
                ((!empty($request->bags) && $request->bags == 'true')?true:false),
                (!empty($request->bags_qty))?$request->bags_qty:0,
                (!empty($request->fare_details) && $request->fare_details == 'true')?true:false
            );

            return AppConstants::apiResponse(200, 'Flight pricing result!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in pricing.', [$e]);
            return AppConstants::apiResponse(404, 'No flight found!');
        }
    }

    public function createOrder($searchId, $flightId, BookingRequest $request)
    {
        try
        {
            $result = $this->flightService->createOrder(
                $searchId,
                $flightId,
                $request->all()['passengers']
            );
            return AppConstants::apiResponse(200, 'Flight booking successful!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in create order.', [$e]);
            return AppConstants::apiResponse(404, 'Error to book flight! Please try again with valid data.');
        }
    }

    public function makePayment($searchId, $flightId)
    {
        try
        {
            $result = $this->flightService->makePayment(
                $searchId,
                $flightId
            );
            return AppConstants::apiResponse(200, 'Payment request successful!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in make payment.', [$e]);
            return AppConstants::apiResponse(404, 'Error to make payment! Please try again with valid data.');
        }
    }
}
