<div class="dropdown">
    <button class="btn btn-sm btn-outline-primary dropdown-toggle actions-dropdown-btn" type="button" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-h actions-dropdown"></i>
    </button>
    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown">
        @if (check_access(array('A_DETAIL_STOCK_TRANSFER'), true))
            <a href="{{ $stock_transfer['detail_link'] }}" class="dropdown-item">{{ __("View") }}</a>
        @endif
        @if (check_access(array('A_EDIT_STOCK_TRANSFER'), true) && $stock_transfer['from_store_data']['slack'] == $current_selected_store_slack && $stock_transfer['status']['value_constant'] == 'PENDING')
            <a href="edit_stock_transfer/{{ $stock_transfer['slack'] }}" class="dropdown-item">{{ __("Edit") }}</a>
        @endif
        @if (check_access(array('A_EDIT_STOCK_TRANSFER'), true) && $stock_transfer['to_store_data']['slack'] == $current_selected_store_slack && in_array($stock_transfer['status']['value_constant'],['PENDING', 'INITIATED']))
            <a href="verify_stock_transfer/{{ $stock_transfer['slack'] }}" class="dropdown-item">{{ __("Verify Stock Transfer") }}</a>
        @endif
    </div>
</div>