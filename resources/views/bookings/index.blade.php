@extends('layouts.app')

@section('styles')
    <style>
        .details-table tbody tr th, .details-table tbody tr td{
            padding: 5px;
        }
        .details-itinerary{
            font-size: 12px;
            padding: 5px 0;
        }
        html.dark .details-itinerary{
            border-bottom: 1px solid #20222e;
        }
        html:not(.dark) .details-itinerary{
            border-bottom: 1px solid #e3e3e3;
        }

        .details-itinerary:last-child{
            border-bottom: none !important;
        }

        /* Dark mode styles */
        html.dark .details-table-row-dark:hover,
        html.dark .details-table-row-dark:hover td,
        html.dark .details-table-row-dark:hover td .card {
            background-color: #1f212a !important;
        }

        /* Light mode styles */
        html:not(.dark) .details-table-row-dark:hover,
        html:not(.dark) .details-table-row-dark:hover td,
        html:not(.dark) .details-table-row-dark:hover td .card {
            background-color: #f9f9f9 !important; /* Set your desired light color */
        }

    </style>
@endsection

@section('content')
<!-- Content -->
<main class="grow content pt-5" id="content" role="content">
    <!-- Container -->
    <div class="container-fixed" id="content_container">
    </div>
    <!-- End of Container -->

    <div class="container-fluid">
        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
         <div class="flex flex-col justify-center gap-2">
          <h1 class="text-xl font-medium leading-none text-gray-900">
           Flight Reservations at {{ date('M Y') }}
          </h1>
          <div class="flex items-center flex-wrap gap-1.5 font-medium">
           <span class="text-md text-gray-600">
            Total Orders:
           </span>
           <span class="text-md text-gray-800 font-semibold me-2">
            {{ number_format($summary->total_bookings) }}
           </span>
           <span class="text-md text-gray-600">
            Total Grand Total
           </span>
           <span class="text-md text-gray-800 font-semibold">
            {{ number_format($summary->total_revenue) }}
           </span>
          </div>
         </div>
        </div>
       </div>

    <!-- Container -->
    <div class="container-fluid">
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card card-grid min-w-full">
                <form method="GET" action="{{ route('bookings.index') }}" class="">
                    <div class="card-header flex-wrap gap-2">
                        <div class="flex items-center gap-2 order-2 md:order-1 card-header flex-wrap gap-2">
                            Show 
                            <select class="select select-sm w-16 me-3" data-datatable-size="true" name="length">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                            <div class="flex items-center gap-4 order-1 md:order-2">
                                <span data-datatable-info="true">Page {{ $bookings->currentPage() }} of {{ $bookings->lastPage() }}</span>
                                <div class="pagination" data-datatable-pagination="true">
                                    @if ($bookings->hasPages())
                                    <div class="pagination">
                                        @if ($bookings->onFirstPage())
                                            <button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button>
                                        @else
                                            <a href="{{ $bookings->previousPageUrl() }}" class="btn">
                                                <i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i>
                                            </a>
                                        @endif

                                        {{-- @dd($bookings->appends(request()->query())->links()->elements); --}}

                                        @foreach ($bookings->appends(request()->query())->links()->elements as $element)
                                            @if (is_string($element))
                                                <button class="btn">...</button>
                                            @endif

                                            @if (is_array($element))
                                                @foreach ($element as $page => $url)
                                                    @if ($page == $bookings->currentPage())
                                                        <button class="btn active disabled" disabled="">{{ $page }}</button>
                                                    @else
                                                        <a href="{{ $url }}" class="btn">{{ $page }}</a>
                                                    @endif
                                                @endforeach
                                            @endif

                                        @endforeach

                                        @if ($bookings->hasMorePages())
                                            <a href="{{ $bookings->nextPageUrl() }}" class="btn">
                                                <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                                            </a>
                                        @else
                                            <button class="btn disabled" disabled="">
                                                <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                                            </button>
                                        @endif
                                        {{-- <button class="btn"><i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i></button> --}}
                                        {{-- {{ $bookings->appends(request()->query())->links() }} --}}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 lg:gap-5">
                            <div class="flex">
                                <label class="input input-sm">
                                    <i class="ki-filled ki-magnifier"></i>
                                    <input placeholder="Search booking" type="text" value="{{ request('search') }}" name="search">
                                </label>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                <select class="select select-sm w-28" name="status">
                                    <option value="">Booking Status</option>
                                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="BOOKED" {{ request('status') == 'BOOKED' ? 'selected' : '' }}>Booked</option>
                                    <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="TICKETED" {{ request('status') == 'TICKETED' ? 'selected' : '' }}>Ticketed</option>
                                </select>
                                <select class="select select-sm w-28" name="payment_status">
                                    <option value="">Payment Status</option>
                                    <option value="PENDING" {{ request('payment_status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="PROCESSING" {{ request('payment_status') == 'PROCESSING' ? 'selected' : '' }}>Processing</option>
                                    <option value="SUCCESS" {{ request('payment_status') == 'SUCCESS' ? 'selected' : '' }}>Success</option>
                                    <option value="FAILED" {{ request('payment_status') == 'FAILED' ? 'selected' : '' }}>Failed</option>
                                    <option value="COMPLETE" {{ request('payment_status') == 'COMPLETE' ? 'selected' : '' }}>Complete</option>
                                </select>
                                <button class="btn btn-sm btn-outline btn-primary">
                                    <i class="ki-filled ki-setting-4"></i> Filters
                                </button>

                                @if (request()->query('payment_status') || request()->query('status') || request()->query('search'))
                                    <a class="text-danger font-bold text-xs pt-2" href="{{ route('bookings.index') }}">
                                        <i class="ki-filled ki-cross"></i> Reset Filter
                                    </a>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                </form>
                <div class="card-body">
                 <div data-datatable="true" data-datatable-page-size="10" class="datatable-initialized">
                    <div class="scrollable-x-auto">
                        <table class="table table-auto table-border" data-datatable-table="true">
                            <thead>
                            <tr>
                                <th class="min-w-[300px]">
                                    <span class="sort asc">
                                        <span class="sort-label font-normal text-gray-700">
                                            Booking Information
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[300px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Payment Information
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[300px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Trip Information
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[300px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Customer Information
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                            </tr>
                            </thead>
                            
                                    
                                <tbody>
                                    @forelse ($bookings as $bk)
                                    <tr class="cursor-pointer transition-colors duration-200 details-table-row-dark" onclick="window.location='{{ route('bookings.show', $bk->id) }}';">
                                        <td>
                                            <div class="card grow">
                                                <div class="card-body pt-4 pb-3">
                                                    <table class="table details-table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Booking Id
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-warning">
                                                                    {{ $bk->booking_id??"N/A" }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    PNR
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs font-[1000] text-primary">
                                                                    {{ $bk->pnr??"N/A" }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Time
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ date('M d, Y H:iA', strtotime($bk->created_at)) }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Trip Type
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-warning">
                                                                    {{ $bk->trip_type }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Status
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    @if ($bk->status == 'PENDING')
                                                                        <span class="badge badge-xs badge-warning badge-outline shrink-0">Pending</span>
                                                                    @elseif ($bk->status == 'BOOKED')
                                                                        <span class="badge badge-xs badge-success badge-outline shrink-0">Booked</span>
                                                                    @elseif ($bk->status == 'CANCELLED')
                                                                        <span class="badge badge-xs badge-danger badge-outline shrink-0">Cancelled</span>
                                                                    @elseif ($bk->status == 'TICKETED')
                                                                        <span class="badge badge-xs badge-info badge-outline shrink-0">Ticketed</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Pax Info
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    <p>
                                                                        Adults: <span class="badge badge-xs badge-default badge-outline shrink-0">{{ (!empty($bk->pax_adults))?$bk->pax_adults:0 }}</span> &nbsp;
                                                                        Childs: <span class="badge badge-xs badge-default badge-outline shrink-0">{{ (!empty($bk->pax_childs))?$bk->pax_childs:0 }}</span> &nbsp;
                                                                        Kids: <span class="badge badge-xs badge-default badge-outline shrink-0">{{ (!empty($bk->pax_kids))?$bk->pax_kids:0 }}</span> &nbsp;
                                                                        Infants: <span class="badge badge-xs badge-default badge-outline shrink-0">{{ (!empty($bk->pax_infants))?$bk->pax_infants:0 }}</span>
                                                                    </p>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="card grow">
                                                <div class="card-body pt-4 pb-3">
                                                    <table class="table details-table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Base Price
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->billing_currency }} {{ $bk->base_price }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Total
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->billing_currency }} {{ $bk->total_price }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Grand Total
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->billing_currency }} {{ $bk->grand_total_price }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Pay Status
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    @if ($bk->payment_status == 'PENDING')
                                                                        <span class="badge badge-xs badge-warning badge-outline shrink-0">Pending</span>
                                                                    @elseif ($bk->payment_status == 'PROCESSING')
                                                                        <span class="badge badge-xs badge-info badge-outline shrink-0">Processing</span>
                                                                    @elseif ($bk->payment_status == 'SUCCESS')
                                                                        <span class="badge badge-xs badge-info badge-outline shrink-0">Success</span>
                                                                    @elseif ($bk->payment_status == 'FAILED')
                                                                        <span class="badge badge-xs badge-danger badge-outline shrink-0">Failed</span>
                                                                    @elseif ($bk->payment_status == 'COMPLETE')
                                                                        <span class="badge badge-xs badge-success badge-outline shrink-0">Complete</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Payment ID
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-warning">
                                                                    {{  $bk->payment_id??"N/A" }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $itinery = \App\Constants\AppConstants::getDestinationAndTime($bk->itineraries);
                                                foreach ($itinery as $key => $itns)
                                                {
                                                    echo $itns;
                                                }
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="card grow">
                                                <div class="card-body pt-4 pb-3">
                                                    <table class="table details-table">
                                                        <tbody>
                                                            <tr>
                                                                <td class="text-xs text-success min-w-30" colspan="2">
                                                                    {{ $bk->customer_email??"N/A" }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Name
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->customer_name }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Phone
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->customer_phone }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    Passport
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    @if (!empty($bk->passengers) && isset($bk->passengers[0]) && isset($bk->passengers[0]['documents'][0]))
                                                                        {{ $bk->passengers[0]['documents'][0]['number'] }}
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-600 min-w-30">
                                                                    City, Country
                                                                </td>
                                                                <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                    {{ $bk->customer_city }} {{ $bk->customer_country }}
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-xs text-gray-800 min-w-30" colspan="2">
                                                                    Address: {{ $bk->customer_address??"N/A" }}, {{ $bk->customer_postcode }}
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </table>
                    </div>
                  {{-- <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                   
                   
                  </div> --}}
                 </div>
                </div>
               </div>
        </div>
    </div>
    <!-- End of Container -->
   </main>
   <!-- End of Content -->
@endsection

@section('scripts')

@endsection