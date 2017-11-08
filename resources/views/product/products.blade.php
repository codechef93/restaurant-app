@extends('layouts.layout')

@section("content")
<div class="row">
    <div class="col-md-12">
        
        <div class="d-flex flex-wrap mb-4">
            <div class="mr-auto">
                <span class="text-title">{{ __("Products") }}</span>
            </div>
            <div class="">
                @if (check_access(array('A_ADD_PRODUCT'), true))
                    <a href="{{ route('add_product')}}" role="button" class="btn btn-primary">{{ __("New Product") }}</a>
                @endif
            </div>
        </div>

        <div class="form-row mb-1">
            <div class="form-group col-md-3">
                <label for="product_type_filter">{{ __("Filter Product") }}</label>
                <select name="product_type_filter" id="product_type_filter" class="form-control form-control-custom custom-select">
                    <option value="all">All</option>
                    <option value="billing_products" selected>Billing Products</option>
                    <option value="addon_products">Add-on Products</option>
                    @if($restaurant_mode)
                    <option value="ingredients">Ingredients</option>
                    @endif
                </select>
            </div>
        </div>

        <div class="table-responsive">
            <table id="listing-table" class="table display nowrap w-100">
                <thead>
                    <tr>
                        <th>{{ __("Product Code") }}</th>
                        <th>{{ __("Name") }}</th>
                        <th>{{ __("Supplier") }}</th>
                        <th>{{ __("Category") }}</th>
                        <th>{{ __("Tax Code") }}</th>
                        <th>{{ __("Discount Code") }}</th>
                        <th>{{ __("Quantity") }}</th>
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
    <script src="{{ asset('js/pages/products.js') }}"></script>
    <script>
        'use strict';
        var products = new Products();
        products.load_listing_table();

        $(document).on('change', '#product_type_filter', function(){
            var product_type = $(this).val();
            event = new CustomEvent("product_type_filter", { "detail": product_type });
            document.dispatchEvent(event);
        });

        $(document).ready(function(){
            var product_type = $('#product_type_filter').val();
            console.log(product_type);
            event = new CustomEvent("product_type_filter", { "detail": product_type });
            document.dispatchEvent(event);
        });
    </script>
@endpush