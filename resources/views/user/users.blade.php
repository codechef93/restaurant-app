@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Users") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_USER'), true))
                    <a href="{{ route('add_user')}}" role="button" class="btn btn-primary">{{ __("New User") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("User Code") }}</th>
                        <th>{{ __("Full Name") }}</th>
                        <th>{{ __("Email") }}</th>
                        <th>{{ __("Phone") }}</th>
                        <th>{{ __("Role") }}</th>
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
    <script src="{{ asset('js/pages/users.js') }}"></script>
    <script>
        'use strict';
        var users = new Users();
        users.load_listing_table();
    </script>
@endpush