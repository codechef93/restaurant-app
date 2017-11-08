@extends('layouts.layout')

@section("content")
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h3 class="mb-0">Restaurants</h3>
                        </div>
                        
                        <div class="col-4 text-right">
                            <a href="/tables/create" class="btn btn-sm btn-primary"> Add new area </a>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    @include('partials.flash')
                </div>
                
                @if(count($items))
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                                <th>Name</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <a href="/tables/floorplan/{{ $item->id }}" class="btn btn-success btn-sm"><span class="btn-inner--icon"><i class="ni ni-vector"></i></span> Floor Plan</a>
                                        <a href="/tables/{{ $item->id }}/edit" class="btn btn-primary btn-sm"><span class="btn-inner--icon"><i class="ni ni-ruler-pencil"></i></span> Edit</a>
                                        <a href="{{route('tables_delete',[$item->id])}}" class="btn btn-danger btn-sm"><span class="btn-inner--icon"><i class="ni ni-fat-remove"></i></span> Delete</a>
                                    </td>
                                </tr> 
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div> 

    <br/>
    
    <div class="row">
        <div class="col-md-12">
            
            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">{{ __("Tables") }}</span>
                </div>
                <div class="">
                    @if (check_access(array('A_ADD_RESTAURANT_TABLE'), true))
                        <a href="{{ route('add_table')}}" role="button" class="btn btn-primary">{{ __("New Table") }}</a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table id="listing-table" class="table display nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ __("Table Name") }}</th>
                            <th>{{ __("No of Occupants") }}</th>
                            <th>{{ __("Area") }}</th>
                            <th>{{ __("Waiter") }}</th>
                            <th>{{ __("Status") }}</th>
                            <th>{{ __("Created On") }}</th>
                            <th>{{ __("Updated On") }}</th>
                            <th>{{ __("Created By") }}</th>
                            <th>{{ __("Action") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/datatable.js') }}"></script>
    <script src="{{ asset('js/pages/restaurant_tables.js') }}"></script>
    <script>
        'use strict';
        var tables = new Tables();
        tables.load_listing_table();
    </script>
@endpush