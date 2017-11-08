@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Notifications") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_NOTIFICATION'), true))
                    <a href="{{ route('add_notification')}}" role="button" class="btn btn-primary">{{ __("New Notification") }}</a>
                @endif
            </div>
        </div>

        <div class="d-flex flex-wrap mb-3">
            <div class="mr-auto">
                <span>List of all the notifications created by you and notifications for you.</span>
            </div>
        </div>
        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Notification") }}</th>
                        <th>{{ __("User Notified") }}</th>
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
    <script src="{{ asset('js/pages/notifications.js') }}"></script>
    <script>
        'use strict';
        var notifications = new Notifications();
        notifications.load_listing_table();
    </script>
@endpush