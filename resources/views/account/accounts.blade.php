@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Accounts") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_ACCOUNT'), true))
                    <a href="{{ route('add_account')}}" role="button" class="btn btn-primary">{{ __("New Account") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Account Code") }}</th>
                        <th>{{ __("Account Name") }}</th>
                        <th>{{ __("Account Type") }}</th>
                        <th>{{ __("POS Default Account") }}</th>
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
    <script src="{{ asset('js/pages/accounts.js') }}"></script>
    <script>
        'use strict';
        var accounts = new Accounts();
        accounts.load_listing_table();
    </script>
@endpush