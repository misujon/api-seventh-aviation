<?php

namespace App\Http\Controllers;

use App\Models\Airline;
use Illuminate\Http\Request;

class AirlineController extends Controller
{
    public function index(Request $request)
    {
        // Get input from request
        $search        = $request->input('search');
        $featuredStatus = $request->input('is_featured'); // e.g. pending, confirmed, cancelled
        $pageLength    = $request->input('length', 15); // default page length
        
        // Build query
        $query = Airline::query();

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('iata_code', 'like', "%{$search}%")
                ->orWhere('lcc', 'like', "%{$search}%")
                ->orWhere('name', 'like', "%{$search}%")
                ->orWhere('logo', 'like', "%{$search}%")
                ->orWhere('is_featured', 'like', "%{$search}%");
            });
        }

        if (!empty($featuredStatus)) $query->where('is_featured', $featuredStatus);

        // Paginate the results
        $airlines = $query->latest()->paginate($pageLength);
        $airlines->appends($request->all());

        $count = Airline::count();

        return view('airlines.index', compact('airlines', 'count'));

    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'iata_code' => 'nullable|string|max:255',
            'lcc' => 'nullable|string|max:255',
            // 'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_featured' => 'nullable|in:TRUE,FALSE',
        ]);

        $airline = Airline::findOrFail($id);

        // if ($request->hasFile('logo')) 
        // {
        //     $file = $request->file('logo');
        //     $filename = time() . '.' . $file->getClientOriginalExtension();
        //     $file->move(public_path('images/airlines'), $filename);
        //     $airline->logo = $filename;
        // }

        if (!empty($request->input('name'))) $airline->name =  $request->input('name');
        if (!empty($request->input('iata_code'))) $airline->iata_code =  $request->input('iata_code');
        if (!empty($request->input('lcc'))) $airline->lcc =  $request->input('lcc');
        if (!empty($request->input('is_featured'))) $airline->is_featured =  $request->input('is_featured');
        $airline->save();
        
        if ($request->ajax()) 
        {
            return response()->json(['success' => 'Airline updated successfully.']);
        }
        else
        {
            return redirect()->route('airlines.index')->with('success', 'Airline updated successfully.');
        }
    }
}
