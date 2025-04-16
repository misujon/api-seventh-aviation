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
            Airports we have
          </h1>
          <div class="flex items-center flex-wrap gap-1.5 font-medium">
            <span class="text-md text-gray-600">
                    Total:
            </span>
            <span class="text-md text-gray-800 font-semibold me-2">
                    {{ number_format($count) }}
            </span>
          </div>
         </div>
        </div>
       </div>

    <!-- Container -->
    <div class="container-fluid">
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card card-grid min-w-full">
                <form method="GET" action="{{ route('airports.index') }}" class="">
                    <div class="card-header flex-wrap gap-2">
                        <div class="flex items-center gap-2 order-2 md:order-1 card-header flex-wrap gap-2 text-sm">

                            <div class="flex items-center gap-4 order-1 md:order-2">
                                <span data-datatable-info="true" class="text-sm">Page {{ $airports->currentPage() }} of {{ $airports->lastPage() }}</span>
                                <div class="pagination" data-datatable-pagination="true">
                                    @if ($airports->hasPages())
                                    <div class="pagination">
                                        @if ($airports->onFirstPage())
                                            <button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button>
                                        @else
                                            <a href="{{ $airports->previousPageUrl() }}" class="btn">
                                                <i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i>
                                            </a>
                                        @endif

                                        @foreach ($airports->appends(request()->query())->links()->elements as $element)
                                            @if (is_string($element))
                                                <button class="btn">...</button>
                                            @endif

                                            @if (is_array($element))
                                                @foreach ($element as $page => $url)
                                                    @if ($page == $airports->currentPage())
                                                        <button class="btn active disabled" disabled="">{{ $page }}</button>
                                                    @else
                                                        <a href="{{ $url }}" class="btn">{{ $page }}</a>
                                                    @endif
                                                @endforeach
                                            @endif

                                        @endforeach

                                        @if ($airports->hasMorePages())
                                            <a href="{{ $airports->nextPageUrl() }}" class="btn">
                                                <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                                            </a>
                                        @else
                                            <button class="btn disabled" disabled="">
                                                <i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i>
                                            </button>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 lg:gap-5">
                            <div class="flex gap-2 items-center">
                                <span class="text-sm">Show </span>
                                <select class="select select-sm w-16" data-datatable-size="true" name="length">
                                    <option value="15">15</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <div class="flex">
                                <label class="input input-sm">
                                    <i class="ki-filled ki-magnifier"></i>
                                    <input placeholder="Search booking" type="text" value="{{ request('search') }}" name="search">
                                </label>
                            </div>
                            <div class="flex flex-wrap gap-2.5">
                                <select class="select select-sm w-28" name="is_featured">
                                    <option value="">Featured</option>
                                    <option value="yes" {{ request('is_featured') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ request('is_featured') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                                <select class="select select-sm w-28" name="is_featured">
                                    <option value="">Status</option>
                                    <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                    <option value="INACTIVE" {{ request('status') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <button class="btn btn-sm btn-outline btn-primary">
                                    <i class="ki-filled ki-setting-4"></i> Filters
                                </button>

                                @if (request()->query('status') || request()->query('is_featured') || request()->query('search'))
                                    <a class="text-danger font-bold text-xs pt-2" href="{{ route('airlines.index') }}">
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
                        <table class="table table-xs table-border" data-datatable-table="true">
                            <thead>
                            <tr>
                                <th class="min-w-[200px]">
                                    <span class="sort asc">
                                        <span class="sort-label font-normal text-gray-700">
                                            Airport
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[200px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            City
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[200px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Country
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[200px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Geo Location
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[200px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Others
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[200px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Actions
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                            </tr>
                            </thead>
                            
                                    
                                <tbody>
                                    @forelse ($airports as $arpt)
                                    <tr class="cursor-pointer transition-colors duration-200 details-table-row-dark">
                                        <td>
                                            <p class="text-sm pb-1"><strong>Airport Code: </strong><span class="badge-primary badge-sm badge-outline">{{ $arpt->code }}</span></p>
                                            <span class="text-xs">{{ $arpt->name }}</span>
                                        </td>
                                        <td class="text-xs">
                                            <p class="pb-1"><strong>City Code: </strong> <span class="badge-sm badge-info badge-outline rounded">{{ $arpt->cityCode }}</span></p>
                                            <p><strong>City Name: </strong> {{ $arpt->cityName }}</p>
                                        </td>
                                        <td class="text-xs">
                                            <p class="pb-1 text-sm"><strong>Country Code: </strong> <span class="badge-sm badge-info badge-outline rounded">{{ $arpt->countryCode }}</span></p>
                                            <p><strong>Country Name: </strong> {{ $arpt->countryName }}</p>
                                        </td>
                                        <td class="text-xs">
                                            <p class="pb-1 text-sm"><strong>Latitude: </strong> {{ $arpt->lat }}</p>
                                            <p><strong>Longitude: </strong> {{ $arpt->lon }}</p>
                                        </td>
                                        <td class="text-xs">
                                            <p class="pb-1 text-sm"><strong>Airports: </strong> {{ $arpt->numAirports }}</p>
                                            <p><strong>Timezone: </strong> {{ $arpt->timezone }}</p>
                                        </td>
                                        <td class="text-xs">
                                            <div class="flex items-center gap-3">
                                                <div>
                                                    <label class="text-xs">Featured</label>
                                                    <select class="select select-sm w-20 mi-status-updated" data-id="{{ $arpt->id }}" data-url="{{ route('airports.update', $arpt->id) }}" data-csrf="{{ csrf_token() }}" data-name="is_featured">
                                                        <option value="yes" {{ $arpt->is_featured == 'yes' ? 'selected' : '' }}>Yes</option>
                                                        <option value="no" {{ $arpt->is_featured == 'no' ? 'selected' : '' }}>No</option>
                                                    </select>
                                                </div>
    
                                                <div>
                                                    <label class="text-xs">Status</label>
                                                    <select class="select select-sm w-20 bg-primary text-gray-900 mi-status-updated" data-id="{{ $arpt->id }}" data-url="{{ route('airports.update', $arpt->id) }}" data-csrf="{{ csrf_token() }}" data-name="status">
                                                        <option value="ACTIVE" {{ $arpt->status == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                                        <option value="INACTIVE" {{ $arpt->status == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </table>
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusElements = document.querySelectorAll('.mi-status-updated');
    
        statusElements.forEach(element => {
            element.addEventListener('change', function () {
                const id = element.getAttribute('data-id');
                const url = element.getAttribute('data-url');
                const csrf = element.getAttribute('data-csrf');
                const name = element.getAttribute('data-name');
                const status = element.value;
    
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf
                    },
                    body: JSON.stringify({
                        _method: 'PUT',
                        [name]: status
                    })
                })
                .then(response => response.json())
                .then(data => {
                    Toastify({
                        text: data.success,
                        duration: 3000,
                        close: true,
                        gravity: "top", // "top" or "bottom"
                        position: "right", // "left" or "right"
                        style: {
                            backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                        } // Customize with Tailwind colors
                    }).showToast();
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while updating the status.');
                });
            });
        });
    });
</script>
    
@endsection