<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_products';
    protected $hidden = ['id', 'order_id'];
    protected $fillable = ['slack', 'order_id', 'parent_order_product_id', 'product_id', 'product_slack', 'product_code', 'name', 'quantity', 'purchase_amount_excluding_tax', 'sale_amount_excluding_tax', 'sub_total_purchase_price_excluding_tax', 'sub_total_sale_price_excluding_tax', 'tax_code_id', 'tax_code', 'tax_percentage', 'tax_amount', 'tax_components', 'discount_code_id', 'discount_code', 'discount_percentage', 'discount_amount', 'total_after_discount', 'total_amount', 'is_ready_to_serve', 'merged_from', 'merged_to', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function scopeProduct($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.id', '=', 'order_products.product_id');
        });
    }

    public function scopeActive($query){
        return $query->where('order_products.status', 1);
    }

    public function scopeFetchParent($query){
        return $query->where('order_products.parent_order_product_id', '')->orWhereNull('order_products.parent_order_product_id');
    }

    public function scopeFetchAddon($query){
        return $query->where('order_products.parent_order_product_id', '!=', '')->orWhereNotNull('order_products.parent_order_product_id');
    }

    public function scopeOrderJoin($query){
        return $query->leftJoin('orders', function ($join) {
            $join->on('orders.id', '=', 'order_products.order_id');
        });
    }

    public function scopeProductJoin($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.id', '=', 'order_products.product_id');
        });
    }

    public function scopeCategoryJoin($query){
        return $query->leftJoin('category', function ($join) {
            $join->on('category.id', '=', 'products.category_id');
        });
    }

    /* For view files */
    
    public function load_addon_product_data(){
        return $this->hasMany('App\Models\OrderProduct', 'parent_order_product_id', 'id');
    }

    public function addon_products_data(){
        return $this->load_addon_product_data()->with('addon_products_data');
    }

    public function product_data(){
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'ORDER_PRODUCT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
