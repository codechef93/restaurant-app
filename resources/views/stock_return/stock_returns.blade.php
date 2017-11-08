@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Stock Returns") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_STOCK_RETURN'), true))
                    <a href="{{ route('add_stock_return')}}" role="button" class="btn btn-primary">{{ __("New Stock Return") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Stock Return Number") }}</th>
                        <th>{{ __("Bill To") }}</th>
                        <th>{{ __("Bill To Name") }}</th>
                        <th>{{ __("Return Date") }}</th>
                        <th>{{ __("Amount") }}</th>
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
@endsection

@push('scripts')
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('js/datatable.js') }}"></script>
    <script src="{{ asset('js/pages/stock_returns.js') }}"></script>
    <script>
        'use strict';
        var stock_returns = new StockReturns();
        stock_returns.load_listing_table();
    </script>
@endpush