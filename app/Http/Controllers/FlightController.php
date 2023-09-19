<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Constants\AppConstants;
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

    public function pricing(SearchRequest $request)
    {
        try
        {
            // $result = $this->flightService->pricing(
            //     $request->from,
            //     $request->to,
            //     $request->departure,
            //     (!empty($request->arrival))?$request->return:null,
            //     (!empty($request->adult))?$request->adult:1,
            //     (!empty($request->child))?$request->child:0,
            //     (!empty($request->infant))?$request->infant:0,
            //     $request->trip_type
            // );

            // return AppConstants::apiResponse(200, 'Flight search result!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in pricing.', [$e]);
            return AppConstants::apiResponse(404, 'No flight found!');
        }
    }
}
