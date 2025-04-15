<?php

namespace App\Http\Controllers;

use App\Models\FlightBooking;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        // Get input from request
        $search        = $request->input('search');
        $bookingStatus = $request->input('status'); // e.g. pending, confirmed, cancelled
        $paymentStatus = $request->input('payment_status'); // e.g. paid, unpaid
        $pageLength    = $request->input('length', 5); // default page length
        
        // Build query
        $query = FlightBooking::query();

        // Apply search
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('customer_email', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_address', 'like', "%{$search}%")
                ->orWhere('customer_phone', 'like', "%{$search}%")
                ->orWhere('pnr', 'like', "%{$search}%")
                ->orWhere('booking_id', 'like', "%{$search}%")
                ->orWhere('total_price', 'like', "%{$search}%")
                ->orWhere('grand_total_price', 'like', "%{$search}%")
                ->orWhere('base_price', 'like', "%{$search}%")
                ->orWhere('payment_id', 'like', "%{$search}%");
            });
        }

        if (!empty($bookingStatus)) $query->where('status', $bookingStatus);
        if (!empty($paymentStatus)) $query->where('payment_status', $paymentStatus);

        // Paginate the results
        $bookings = $query->latest()->paginate($pageLength);

        // Optionally preserve filters in pagination links
        $bookings->appends($request->all());
        // dd($bookings);

        // Or return to a Blade view
        return view('bookings.index', compact('bookings'));
    }
}
