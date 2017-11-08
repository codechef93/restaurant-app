@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Add-on Groups") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_ADDON_GROUP'), true))
                    <a href="{{ route('add_addon_group')}}" role="button" class="btn btn-primary">{{ __("New Add-on Group") }}</a>
                @endif
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Add-on Group Code") }}</th>
                        <th>{{ __("Add-on Group") }}</th>
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
    <script src="{{ asset('js/pages/addon_groups.js') }}"></script>
    <script>
        'use strict';
        var addongroups = new AddonGroups();
        addongroups.load_listing_table();
    </script>
@endpush