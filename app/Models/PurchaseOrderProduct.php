<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderProduct extends Model
{
    protected $table = 'purchase_order_products';
    protected $hidden = ['id', 'purchase_order_id'];
    protected $fillable = ['slack', 'purchase_order_id', 'product_id', 'product_slack', 'product_code', 'name', 'quantity', 'amount_excluding_tax', 'discount_percentage', 'tax_type', 'tax_percentage', 'discount_amount', 'total_after_discount', 'tax_amount', 'total_amount', 'stock_update', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function scopeProduct($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.id', '=', 'purchase_order_products.product_id');
        });
    }

    public function scopeActive($query){
        return $query->where('purchase_order_products.status', 1);
    }

    /* For view files */
    
    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'PURCHASE_ORDER_PRODUCT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
