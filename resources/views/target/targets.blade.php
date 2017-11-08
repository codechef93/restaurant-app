@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Monthly Targets") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_TARGET'), true))
                    <a href="{{ route('add_target')}}" role="button" class="btn btn-primary">{{ __("New Target") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Month") }}</th>
                        <th>{{ __("Income") }} ({{ $store->currency_code }})</th>
                        <th>{{ __("Expense") }} ({{ $store->currency_code }})</th>
                        <th>{{ __("Sales") }} ({{ $store->currency_code }})</th>
                        <th>{{ __("Net Profit") }} ({{ $store->currency_code }})</th>
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
    <script src="{{ asset('js/pages/targets.js') }}"></script>
    <script>
        'use strict';
        var targets = new Targets();
        targets.load_listing_table();
    </script>
@endpush