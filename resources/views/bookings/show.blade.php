@extends('layouts.app')

@section('styles')
    <style>
        .details-table tbody tr th, .details-table tbody tr td{
            padding: 5px;
        }
        .details-itinerary{
            font-size: 12px;
            padding: 5px 0;
            border-bottom: 1px solid #20222e;
        }
        .details-itinerary:last-child{
            border-bottom: none;
        }

        .details-table-row:hover, .details-table-row:hover td, .details-table-row:hover td .card{
            background: #1f212a
        }

        .mi-details-view-table > tbody > tr > td.custom-td{
            padding: 0 !important;
        }

        .mi-details-view-table > tbody > tr > td.custom-td > .card{
            border-radius: 0 !important;
        }

        .overflow-wrap{
            overflow-wrap: anywhere;
            max-width: 350px;
        }

        .font-bold{
            font-weight: bold;
        }

        .additional-details-table tbody tr td, .additional-details-table tbody tr th {
            padding: 5px 15px;
            overflow-wrap: anywhere;
            max-width: 250px;
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

    <div class="container-fixed">
        <div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
            <div class="flex flex-col justify-center gap-2">
                <h1 class="text-xl font-medium leading-none text-gray-900">
                    Flight Booking Id: {{ strtoupper($booking->booking_id) }}
                </h1>
                <div class="flex items-center flex-wrap gap-1.5 font-medium">
                    <span class="text-md text-gray-600">
                        Booking Time:
                    </span>
                    <span class="text-md text-gray-800 font-semibold me-2">
                            {{ date('M d, Y H:i A', strtotime($booking->created_at)) }}
                    </span>
                    <span class="text-md text-gray-600">
                            Status
                    </span>
                    <span class="text-md text-gray-800 font-semibold">
                            @if ($booking->status == 'PENDING')
                                <span class="badge badge-sm badge-warning badge-outline shrink-0">Pending</span>
                            @elseif ($booking->status == 'BOOKED')
                                <span class="badge badge-sm badge-success badge-outline shrink-0">Booked</span>
                            @elseif ($booking->status == 'CANCELLED')
                                <span class="badge badge-sm badge-danger badge-outline shrink-0">Cancelled</span>
                            @elseif ($booking->status == 'TICKETED')
                                <span class="badge badge-sm badge-info badge-outline shrink-0">Ticketed</span>
                            @endif
                    </span>
                </div>
            </div>

            <div class="flex">
                <button class="btn btn-primary btn-outline me-2 btn-sm">
                    Download Booking <i class="ki-filled ki-document"></i>
                </button>
                <button class="btn btn-success btn-outline btn-sm">
                    Download Ticket <i class="ki-filled ki-cheque"></i>
                </button>
            </div>
        </div>
       </div>

    <!-- Container -->
    <div class="container-fixed">
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card card-grid min-w-full">
                <div class="card-body">
                    <div data-datatable="true" data-datatable-page-size="10" class="datatable-initialized">
                        <div class="scrollable-x-auto">
                            <table class="table table-auto table-border mi-details-view-table" data-datatable-table="true">
                                    <tbody>
                                        @php
                                            $bk = $booking;
                                            // dd($bk);
                                        @endphp

                                        <tr>
                                            <th class="min-w-[300px]">
                                                <span class="sort asc">
                                                    <span class="sort-label font-bold text-gray-700">
                                                        Booking Information
                                                    </span>
                                                </span>
                                            </th>
                                            <th class="min-w-[300px]">
                                                <span class="sort">
                                                    <span class="sort-label font-bold text-gray-700">
                                                        Payment Information
                                                    </span>
                                                </span>
                                            </th>
                                        </tr>

                                        <tr>
                                            <td class="custom-td">
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
                                                                        Booking Office Id
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-warning">
                                                                        {{ $bk->booking_office_id??"N/A" }}
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
                                            <td class="custom-td">
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
                                                                        Price Currency
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                        {{ $bk->price_currency }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-xs text-gray-600 min-w-30">
                                                                        Billing Currency
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                        {{ $bk->billing_currency }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-xs text-gray-600 min-w-30">
                                                                        Product Category
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                        {{ $bk->customer_product_category }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-xs text-gray-600 min-w-30">
                                                                        Product Profile
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                        {{ $bk->customer_product_profile }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td class="text-xs text-gray-600 min-w-30">
                                                                        Payment Url
                                                                    </td>
                                                                    <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                        <span class="overflow-wrap">{{ $bk->payment_url }}</span>
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
                                        </tr>

                                        <tr>
                                    
                                            <th class="min-w-[300px]">
                                                <span class="sort-label font-bold text-gray-700">
                                                    Trip Information
                                                </span>
                                            </th>
                                            <th class="min-w-[300px]">
                                                <span class="sort-label font-bold text-gray-700">
                                                    Customer Information
                                                </span>
                                            </th>
                                        </tr>

                                        <tr>
                                            <td>
                                                @php
                                                    $itinery = \App\Constants\AppConstants::getDestinationAndTime($bk->itineraries, true);
                                                @endphp

                                                @foreach ($itinery as $key => $itns)
                                                    <div class="flex items-center justify-between flex-wrap border border-gray-200 rounded-xl gap-2 px-4 py-4 mb-2 bg-secondary-clarity">
                                                        <div class="flex items-center gap-3.5">
                                                            <div class="relative size-[50px] shrink-0">
                                                                <img class="w-15" src="{{ $itns['airline']['logo'] }}"/>
                                                            </div>
                                                            <div class="flex flex-col">
                                                                <p>
                                                                    {{-- <strong>Destination:</strong>  --}}
                                                                    <span class='badge badge-sm badge-warning badge-outline shrink-0'>{{ $itns['departure']['iataCode'] }} ({{ $itns['departure']['cityName'] }}, {{ $itns['departure']['country'] }})</span>
                                                                    &nbsp;<i class="ki-filled ki-black-right text-sm text-gray-600"></i>&nbsp;
                                                                    <span class='badge badge-sm badge-primary badge-outline shrink-0'>{{ $itns['arrival']['iataCode'] }} ({{ $itns['arrival']['cityName'] }}, {{ $itns['departure']['country'] }})</span>
                                                                </p>
                                                                <p class="text-xs mt-1">
                                                                    <strong>Departure: </strong> 
                                                                    {{ $itns['departure']['at'] }}
                                                                    &nbsp;<i class="ki-filled ki-watch"></i>&nbsp;
                                                                    <strong>Arrival: </strong> 
                                                                    {{ $itns['arrival']['at'] }}
                                                                </p>
                                                                <p class="text-xs mt-1">
                                                                    <strong>Carrier: </strong>
                                                                    {{ $itns['airline']['name'] }} ({{ $itns['airline']['iata'] }})
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </td>
                                            <td class="custom-td">
                                                @foreach ($bk->passengers as $customer)
                                                    <div class="card grow">
                                                        <div class="card-body pt-4 pb-3">
                                                            <table class="table details-table">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Email
                                                                        </td>
                                                                        <td class="text-xs text-success min-w-30" colspan="2">
                                                                            {{ $customer['contact']['emailAddress']??"N/A" }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Name
                                                                        </td>
                                                                        <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                            {{ $customer['name']['firstName']??"" }} {{ $customer['name']['lastName']??"" }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Phone
                                                                        </td>
                                                                        <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                            {{ $customer['contact']['phones'][0]['countryCallingCode']??"" }}-{{ $customer['contact']['phones'][0]['number']??"" }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            City
                                                                        </td>
                                                                        <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                            {{ $bk->customer_city }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Country
                                                                        </td>
                                                                        <td class="flex items-center gap-2.5 text-xs text-gray-800">
                                                                            {{ $bk->customer_country }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Postal Code
                                                                        </td>
                                                                        <td class="text-xs text-gray-800 min-w-30">
                                                                            {{ $bk->customer_postcode }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Address
                                                                        </td>
                                                                        <td class="text-xs text-gray-800 min-w-30">
                                                                            {{ $bk->customer_address??"N/A" }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-xs text-gray-600 min-w-30">
                                                                            Passport
                                                                        </td>
                                                                        <td class="text-xs text-gray-800">
                                                                            @foreach ($customer['documents'] as $doc)
                                                                                <p><span class="font-bold">Document Type: </span>{{ $doc['documentType'] }}</p>
                                                                                <p><span class="font-bold">Number: </span>{{ $doc['number'] }}</p>
                                                                                <p><span class="font-bold">Issue Date: </span>{{ $doc['issuanceDate'] }}</p>
                                                                                <p><span class="font-bold">Expiry Date: </span>{{ $doc['expiryDate'] }}</p>
                                                                                <p><span class="font-bold">Issue Country: </span>{{ $doc['issuanceCountry'] }}</p>
                                                                                <p><span class="font-bold">Issue Location: </span>{{ $doc['issuanceLocation'] }}</p>
                                                                                <p><span class="font-bold">Nationality: </span>{{ $doc['nationality'] }}</p>
                                                                                <p><span class="font-bold">Birth Place: </span>{{ $doc['birthPlace'] }}</p>
                                                                            @endforeach
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                
                                            </td>
                                        </tr>
                                    </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div data-accordion="true" data-accordion-expand-all="true">
                        <div class="accordion-item [&amp;:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true" aria-expanded="false">
                            <button class="accordion-toggle py-4" data-accordion-toggle="#faq_1_content">
                                <span class="text-base text-gray-900">
                                    <i class="ki-filled ki-notepad"></i> Additional Booking Information
                                </span>
                                <i class="ki-filled ki-plus text-gray-600 text-sm accordion-active:hidden block"></i>
                                <i class="ki-filled ki-minus text-gray-600 text-sm accordion-active:block hidden"></i>
                            </button>
                            <div class="accordion-content hidden" id="faq_1_content" style="height: 0px;">
                                <ul class="p-3 border-bottom">
                                    {!! \App\Constants\AppConstants::renderArrayAsTable($booking->booking_response['data']??[]) !!}
                                </ul>
                            </div>
                        </div>

                        <div class="accordion-item [&amp;:not(:last-child)]:border-b border-b-gray-200" data-accordion-item="true" aria-expanded="false">
                            <button class="accordion-toggle py-4" data-accordion-toggle="#faq_2_content">
                                <span class="text-base text-gray-900">
                                    <i class="ki-filled ki-credit-cart"></i> Additional Payment Information
                                </span>
                                <i class="ki-filled ki-plus text-gray-600 text-sm accordion-active:hidden block"></i>
                                <i class="ki-filled ki-minus text-gray-600 text-sm accordion-active:block hidden"></i>
                            </button>
                            <div class="accordion-content hidden" id="faq_2_content" style="height: 0px;">
                                <ul class="p-3 border-bottom">
                                    {!! \App\Constants\AppConstants::renderArrayAsTable($booking->payment_full_response??[]) !!}
                                </ul>
                            </div>
                        </div>
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
    <script src="{{ asset('assets/js/layouts/demo1.js') }}"></script>
@endsection