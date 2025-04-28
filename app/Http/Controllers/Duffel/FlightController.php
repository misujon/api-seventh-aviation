<?php

namespace App\Http\Controllers\Duffel;

use App\Constants\AppConstants;
use App\Http\Controllers\Controller;
use App\Http\Requests\Duffel\FlightSearchRequest;
use App\Services\DuffelService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlightController extends Controller
{
    private $flightService;

    public function __construct(DuffelService $service)
    {
        $this->flightService = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FlightSearchRequest $request)
    {
        try
        {
            $result = $this->flightService->flightOffers(
                $request->from,
                $request->to,
                $request->departure,
                (!empty($request->return))?$request->return:null,
                (!empty($request->adult))?$request->adult:1,
                (!empty($request->child))?$request->child:0,
                $request->trip_type,
                $request->multistops,
                $request->cabin_class
            );

            return AppConstants::apiResponse(200, 'Flight search result!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in search.', [$e]);
            return AppConstants::apiResponse(404, 'No flight found!');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $offerId)
    {
        try
        {
            $result = $this->flightService->flightOffersFetch($offerId);

            return AppConstants::apiResponse(200, 'Flight offer fetch!', $result);
        }
        catch(Exception $e)
        {
            Log::error('Error in flight offer.', [$e]);
            return AppConstants::apiResponse(404, 'No flight offer found!');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
