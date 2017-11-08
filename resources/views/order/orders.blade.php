@extends('layouts.layout')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">{{ __('POS Orders') }}</span>
                </div>
                <div class="">
                    <select id="status_filter" class="form-select form-select-lg mr-3 p-2 btn-primary rounded" aria-label=".form-select-lg example">
                            
                            <option selected>InKitchen</option>
                            <option >Merged</option>
                            <option >Closed</option>
                            <option >All</option>
                    </select>
                    <button id="mergeClick" class="btn btn-primary mr-3">Merge</button>
                    @if (check_access(['A_ADD_ORDER'], true))
                        <a href="{{ route('add_order') }}" role="button" class="btn btn-primary">{{ __('New Order') }}</a>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table id="listing-table" class="table display nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ __('Order Number') }}</th>
                            <th>{{ __('Table') }}</th>
                            <th>{{ __('Customer Name') }}</th>
                            <th>{{ __('Customer Phone') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created On') }}</th>
                            <th>{{ __('Updated On') }}</th>
                            <th>{{ __('Created By') }}</th>
                            <th>{{ __('Merge') }}</th>
                            <th>{{ __('Action') }}</th>
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
    <script src="{{ asset('js/pages/orders.js') }}"></script>
    <script src="{{ asset('js/pages/orders_filter.js') }}"></script>
    <script>
        'use strict';
        var orders = new Orders();
        orders.load_listing_table();
    </script>
    <script>
        $(document).ready(function() {
            $("#status_filter").change(function(){
                var filter_by = $(this).val();
                'use strict';
                var orders_filter = new Orders_filter();
                orders_filter.load_listing_table(filter_by);
            })
            $("#mergeClick").click(function() {
                var listing_table = $("#listing-table").DataTable()

                var mergeIdx = []
                $("#listing-table tbody input[type='checkbox']").each(function(index, value) {
                    if ($(this)[0].checked) {
                        var data = listing_table.row($(this).parents("tr")).data();
                        mergeIdx.push(data[0])
                    }
                });
                var parentId = mergeIdx[0]
                mergeIdx.shift()
                $.ajax({
                    url: "/api/merge_order",
                    type: "POST",
                    data: {
                        access_token: window.settings.access_token,
                        parent_order_number: parentId,
                        children_order_numbers: mergeIdx
                    },
                    success: function(result) {
                        location.href = `/edit_order/${result.data.slack}`
                    }
                })
            })

        })
    </script>
@endpush
