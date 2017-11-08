<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\PaymentMethod as PaymentMethodModel;

use App\Models\Scopes\StoreScope;

class Order extends Model
{
    protected $table = 'orders';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'store_id', 'order_number', 'customer_id', 'customer_name', 'customer_phone', 'customer_email', 'contact_number', 'address', 'register_id', 'store_level_discount_code_id', 'store_level_discount_code', 'store_level_total_discount_percentage', 'store_level_total_discount_amount', 'product_level_total_discount_amount', 'store_level_tax_code_id', 'store_level_tax_code', 'store_level_total_tax_percentage', 'store_level_total_tax_amount', 'store_level_total_tax_components', 'product_level_total_tax_amount', 'purchase_amount_subtotal_excluding_tax', 'sale_amount_subtotal_excluding_tax', 'total_discount_before_additional_discount', 'total_amount_before_additional_discount', 'additional_discount_percentage', 'additional_discount_amount', 'total_discount_amount', 'total_after_discount', 'total_tax_amount', 'total_order_amount', 'total_order_amount_rounded', 'payment_method_id', 'payment_method_slack', 'payment_method', 'currency_name', 'currency_code', 'business_account_id', 'order_type_id', 'order_type', 'restaurant_mode', 'table_id', 'table_number', 'waiter_id', 'bill_type_id', 'bill_type', 'order_origin', 'status', 'kitchen_status', 'payment_status', 'order_merged', 'order_merge_parent_id', 'kitchen_screen_dismissed', 'waiter_screen_dismissed', 'created_by', 'updated_by', 'created_at', 'updated_at', 'type','additional_note'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeFilter($query, $filter_by){
        if($filter_by == 'InKitchen')
            return $query->where([
                ['orders.status', '=', 5]
            ]);
        if($filter_by == 'Closed')
            return $query->where([
                ['orders.status', '=', 1]
            ]);
        if($filter_by == 'Merged')
        return $query->where('orders.order_merged', 1);
    
    }
    public function scopeClosed($query){
        return $query->where([
            ['orders.status', '=', 1]
        ]);
    }
    public function scopeNotClosed($query){
        return $query->where([
            ['orders.status', '!=', 1]
        ]);
    }

    public function scopeHold($query){
        return $query->where('orders.status', 2);
    }

    public function scopeInkitchen($query){
        return $query->where('orders.status', 5);
    }

    public function scopeInkitchenOrClosed($query){
        return $query->where('orders.status', 1)
        ->orWhere('orders.status', 5);
    }

    public function scopeKitchenNonDismissed($query){
        return $query->where('orders.kitchen_screen_dismissed', 0);
    }

    public function scopeWaiterNonDismissed($query){
        return $query->where('orders.waiter_screen_dismissed', 0);
    }

    public function scopeNotMerged($query){
        return $query->where('orders.order_merged', 0);
    }

    public function scopeMerged($query){
        return $query->where('orders.order_merged', 1);
    }

    public function scopeDigitalMenuOrders($query){
        return $query->where([
            ['orders.status', '=', 6],
            ['orders.order_origin', '=', 'DIGITAL_MENU'],
        ]);
    }
    
    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'orders.status');
            $join->where('master_status.key', '=', 'ORDER_STATUS');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'orders.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'orders.updated_by');
        });
    }

   /* For view files */

    public function products(){
        return $this->hasMany('App\Models\OrderProduct', 'order_id', 'id')->where([
            ['order_products.status', '=', 1],
        ])
        ->whereRaw("(order_products.parent_order_product_id IS NULL OR order_products.parent_order_product_id = '')");
    }

    public function storeData(){
        return $this->hasOne('App\Models\Store', 'id', 'store_id');
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function waiterUser(){
        return $this->hasOne('App\Models\User', 'id', 'waiter_id')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function transactions(){
        return $this->hasMany('App\Models\Transaction', 'bill_to_id', 'id')->whereIn('transactions.bill_to',['POS_ORDER'])->orderBy('transactions.transaction_date', 'desc');
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'ORDER_STATUS');
    }

    public function kitchen_status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'kitchen_status')->where('key', 'ORDER_KITCHEN_STATUS');
    }

    public function payment_status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'payment_status')->where('key', 'ORDER_PAYMENT_STATUS');
    }

    public function order_type_data(){
        return $this->hasOne('App\Models\MasterOrderType', 'id', 'order_type_id');
    }

    public function customer_data(){
        return $this->hasOne('App\Models\Customer', 'id', 'customer_id');
    }

    public function restaurant_table_data(){
        return $this->hasOne('App\Models\Table', 'id', 'table_id');
    }

    public function billing_type_data(){
        return $this->hasOne('App\Models\MasterBillingType', 'id', 'bill_type_id');
    }

    public function merged_orders(){
        return $this->hasMany('App\Models\Order', 'order_merge_parent_id', 'id')->select('slack', 'order_number')->merged()->orderBy('id', 'desc');
    }

    public function generate_public_payment_links(){
        $payment_links = [];
        $payment_methods = PaymentMethodModel::select('slack', 'label', 'payment_constant')
        ->active()
        ->activeOnDigitalMenu()
        ->get();
        if(!empty($payment_methods)){
            foreach($payment_methods as $payment_method){
                $forward_link = route('payment_gateway_public', ['type' => strtolower($payment_method->payment_constant), 'slack' => $this->slack, 'store' => $this->storeData->slack, 'table' => (isset($this->restaurant_table_data))?$this->restaurant_table_data->slack:'']);
                $payment_links[$payment_method->payment_constant] = [
                    'slack' => $payment_method->slack,
                    'label' => $payment_method->label,
                    'link' => $forward_link
                ];
            }
        }

        return $payment_links;
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
