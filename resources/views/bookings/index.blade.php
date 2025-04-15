@extends('layouts.app')

@section('styles')

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
           Flight Reservations This week
          </h1>
          <div class="flex items-center flex-wrap gap-1.5 font-medium">
           <span class="text-md text-gray-600">
            All Members:
           </span>
           <span class="text-md text-gray-800 font-semibold me-2">
            49,053
           </span>
           <span class="text-md text-gray-600">
            Pro Licenses
           </span>
           <span class="text-md text-gray-800 font-semibold">
            1724
           </span>
          </div>
         </div>
        </div>
       </div>

    <!-- Container -->
    <div class="container-fluid">
        <div class="grid gap-5 lg:gap-7.5">
            <div class="card card-grid min-w-full">
                <div class="card-header flex-wrap gap-2">
                 <h3 class="card-title font-medium text-sm">
                  Showing 10 of 49,053 users
                 </h3>
                 <div class="flex flex-wrap gap-2 lg:gap-5">
                  <div class="flex">
                   <label class="input input-sm">
                    <i class="ki-filled ki-magnifier">
                    </i>
                    <input placeholder="Search users" type="text" value="">
                    
                   </label>
                  </div>
                  <div class="flex flex-wrap gap-2.5">
                   <select class="select select-sm w-28">
                    <option value="1">
                     Active
                    </option>
                    <option value="2">
                     Disabled
                    </option>
                    <option value="2">
                     Pending
                    </option>
                   </select>
                   <select class="select select-sm w-28">
                    <option value="1">
                     Latest
                    </option>
                    <option value="2">
                     Older
                    </option>
                    <option value="3">
                     Oldest
                    </option>
                   </select>
                   <button class="btn btn-sm btn-outline btn-primary">
                    <i class="ki-filled ki-setting-4">
                    </i>
                    Filters
                   </button>
                  </div>
                 </div>
                </div>
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
                        <th class="min-w-[300px]">
                            <span class="sort">
                                <span class="sort-label font-normal text-gray-700">
                                    Payment Information
                                </span>
                                <span class="sort-icon"></span>
                            </span>
                        </th>
                        <th class="min-w-[175px]">
                            <span class="sort-label font-normal text-gray-700">
                                Actions
                            </span>
                        </th>
                     </tr>
                    </thead>
                    
                            
                        <tbody>
                            <tr>
                                <td>

                                    <div class="card grow">
                                        <div class="card-body pt-4 pb-3">
                                         <table class="table-auto">
                                          <tbody>
                                           <tr>
                                            <td class="text-sm text-gray-600 min-w-36">
                                                Booking Id
                                            </td>
                                            <td class="flex items-center gap-2.5 text-sm text-gray-800">
                                                Cloud One Enterprise
                                            </td>
                                           </tr>
                                           <tr>
                                            <td class="text-sm text-gray-600 min-w-36">
                                                PNR
                                            </td>
                                            <td class="flex items-center gap-2.5 text-sm text-gray-800">
                                                6 Aug, 2024
                                            </td>
                                           </tr>
                                           <tr>
                                            <td class="text-sm text-gray-600 min-w-36">
                                                Time
                                            </td>
                                            <td class="flex items-center gap-2.5 text-sm text-gray-800">
                                                6 Aug, 2024
                                            </td>
                                           </tr>
                                           <tr>
                                            <td class="text-sm text-gray-600 min-w-36">
                                                Trip Type
                                            </td>
                                            <td class="flex items-center gap-2.5 text-sm text-gray-800">
                                                One Way
                                            </td>
                                           </tr>
                                          </tbody>
                                         </table>
                                        </div>
                                       </div>

                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                </table>
                  </div>
                  <div class="card-footer justify-center md:justify-between flex-col md:flex-row gap-5 text-gray-600 text-2sm font-medium">
                   <div class="flex items-center gap-2 order-2 md:order-1">
                    Show
                    <select class="select select-sm w-16" data-datatable-size="true" name="perpage"><option value="5">5</option><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="50">50</option></select>
                    per page
                   </div>
                   <div class="flex items-center gap-4 order-1 md:order-2">
                    <span data-datatable-info="true">1-5 of 31</span>
                    <div class="pagination" data-datatable-pagination="true"><div class="pagination"><button class="btn disabled" disabled=""><i class="ki-outline ki-black-left rtl:transform rtl:rotate-180"></i></button><button class="btn active disabled" disabled="">1</button><button class="btn">2</button><button class="btn">3</button><button class="btn">...</button><button class="btn"><i class="ki-outline ki-black-right rtl:transform rtl:rotate-180"></i></button></div></div>
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

@endsection