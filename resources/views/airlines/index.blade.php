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
            Airlines we have
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
                <form method="GET" action="{{ route('airlines.index') }}" class="">
                    <div class="card-header flex-wrap gap-2">
                        <div class="flex items-center gap-2 order-2 md:order-1 card-header flex-wrap gap-2 text-sm">

                            <div class="flex items-center gap-4 order-1 md:order-2">
                                <span data-datatable-info="true" class="text-sm">Page {{ $airlines->currentPage() }} of {{ $airlines->lastPage() }}</span>
                                <div class="pagination" data-datatable-pagination="true">
                                    @if ($airlines->hasPages())
                                    <div class="pagination">
                                        @if ($airlines->onFirstPage())
                                            <button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button>
                                        @else
                                            <a href="{{ $airlines->previousPageUrl() }}" class="btn">
                                                <i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i>
                                            </a>
                                        @endif

                                        @foreach ($airlines->appends(request()->query())->links()->elements as $element)
                                            @if (is_string($element))
                                                <button class="btn">...</button>
                                            @endif

                                            @if (is_array($element))
                                                @foreach ($element as $page => $url)
                                                    @if ($page == $airlines->currentPage())
                                                        <button class="btn active disabled" disabled="">{{ $page }}</button>
                                                    @else
                                                        <a href="{{ $url }}" class="btn">{{ $page }}</a>
                                                    @endif
                                                @endforeach
                                            @endif

                                        @endforeach

                                        @if ($airlines->hasMorePages())
                                            <a href="{{ $airlines->nextPageUrl() }}" class="btn">
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
                                    <option value="TRUE" {{ request('is_featured') == 'TRUE' ? 'selected' : '' }}>Yes</option>
                                    <option value="FALSE" {{ request('is_featured') == 'FALSE' ? 'selected' : '' }}>No</option>
                                </select>
                                <button class="btn btn-sm btn-outline btn-primary">
                                    <i class="ki-filled ki-setting-4"></i> Filters
                                </button>

                                @if (request()->query('is_featured') || request()->query('search'))
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
                                <th class="min-w-[300px]">
                                    <span class="sort asc">
                                        <span class="sort-label font-normal text-gray-700">
                                            Airline
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[300px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Others
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                                <th class="min-w-[300px]">
                                    <span class="sort">
                                        <span class="sort-label font-normal text-gray-700">
                                            Featured
                                        </span>
                                        <span class="sort-icon"></span>
                                    </span>
                                </th>
                            </tr>
                            </thead>
                            
                                    
                                <tbody>
                                    @forelse ($airlines as $als)
                                    <tr class="cursor-pointer transition-colors duration-200 details-table-row-dark">
                                        <td>
                                            <div class="flex items-center gap-2">
                                                <img src="{{ $als->logo }}" width="35"/>
                                                {{ $als->name }}
                                            </div>
                                        </td>
                                        <td class="text-xs">
                                            <p class="pb-1"><strong>IATA Code: </strong> <span class="badge-sm badge-info badge-outline rounded">{{ $als->iata_code }}</span></p>
                                            <p><strong>LCC: </strong> {{ $als->lcc }}</p>
                                        </td>
                                        <td>
                                            <select class="select select-sm w-20 mi-status-updated" data-id="{{ $als->id }}" data-url="{{ route('airlines.update', $als->id) }}" data-csrf="{{ csrf_token() }}">
                                                {{-- <option value="">Featured</option> --}}
                                                <option value="TRUE" {{ $als->is_featured == 'TRUE' ? 'selected' : '' }}>Yes</option>
                                                <option value="FALSE" {{ $als->is_featured == 'FALSE' ? 'selected' : '' }}>No</option>
                                            </select>
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
                        is_featured: status
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