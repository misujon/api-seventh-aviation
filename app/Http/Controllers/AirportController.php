<?php

namespace App\Http\Controllers;

use App\Models\Airport;
use Illuminate\Http\Request;

class AirportController extends Controller
{
    public function index(Request $request)
    {
        // Get input from request
        $search        = $request->input('search');
        $status = $request->input('status'); // e.g. pending, confirmed, cancelled
        $featuredStatus = $request->input('is_featured'); // e.g. pending, confirmed, cancelled
        $pageLength    = $request->input('length', 15); // default page length
        
        // Build query
        $query = Airport::query();

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('cityCode', 'like', "%{$search}%")
                ->orWhere('cityName', 'like', "%{$search}%")
                ->orWhere('countryName', 'like', "%{$search}%")
                ->orWhere('countryCode', 'like', "%{$search}%")
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhere('is_featured', 'like', "%{$search}%");
            });
        }

        if (!empty($featuredStatus)) $query->where('is_featured', $featuredStatus);
        if (!empty($status)) $query->where('status', $status);

        // Paginate the results
        $airports = $query->latest()->paginate($pageLength);
        $airports->appends($request->all());

        $count = Airport::count();

        return view('airports.index', compact('airports', 'count'));

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'cityCode' => 'nullable|string|max:255',
            'cityName' => 'nullable|string|max:255',
            'countryName' => 'nullable|string|max:255',
            'countryCode' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:255',
            'lat' => 'nullable|string|max:255',
            'lon' => 'nullable|string|max:255',
            'numAirports' => 'nullable|string|max:255',
            'is_featured' => 'nullable|in:yes,no',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ]);

        $airline = Airport::findOrFail($id);

        // if ($request->hasFile('logo')) 
        // {
        //     $file = $request->file('logo');
        //     $filename = time() . '.' . $file->getClientOriginalExtension();
        //     $file->move(public_path('images/airlines'), $filename);
        //     $airline->logo = $filename;
        // }
        if (!empty($request->input('name'))) $airline->name =  $request->input('name');
        if (!empty($request->input('code'))) $airline->code =  $request->input('code');
        if (!empty($request->input('cityCode'))) $airline->cityCode =  $request->input('cityCode');
        if (!empty($request->input('cityName'))) $airline->cityName =  $request->input('cityName');
        if (!empty($request->input('countryName'))) $airline->countryName =  $request->input('countryName');
        if (!empty($request->input('countryCode'))) $airline->countryCode =  $request->input('countryCode');
        if (!empty($request->input('timezone'))) $airline->timezone =  $request->input('timezone');
        if (!empty($request->input('lat'))) $airline->lat =  $request->input('lat');
        if (!empty($request->input('lon'))) $airline->lon =  $request->input('lon');
        if (!empty($request->input('numAirports'))) $airline->numAirports =  $request->input('numAirports');
        if (!empty($request->input('status'))) $airline->status =  $request->input('status');
        if (!empty($request->input('is_featured'))) $airline->is_featured =  $request->input('is_featured');
        $airline->save();
        
        if ($request->ajax()) 
        {
            return response()->json(['success' => 'Airport updated successfully.']);
        }
        else
        {
            return redirect()->route('airlines.index')->with('success', 'Airport updated successfully.');
        }
    }
}
