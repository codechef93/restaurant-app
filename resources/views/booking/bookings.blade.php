@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Bookings & Events") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_BOOKING'), true))
                    <a href="{{ route('add_booking')}}" role="button" class="btn btn-primary">{{ __("New Booking or Event") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Event Type") }}</th>
                        <th>{{ __("Event Code") }}</th>
                        <th>{{ __("Start Date") }}</th>
                        <th>{{ __("End Date") }}</th>
                        <th>{{ __("Name") }}</th>
                        <th>{{ __("Email") }}</th>
                        <th>{{ __("Contact No") }}</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/datatable.js') }}"></script>
    <script src="{{ asset('js/pages/bookings.js') }}"></script>
    <script>
        'use strict';
        var bookings = new Bookings();
        bookings.load_listing_table();
    </script>
@endpush