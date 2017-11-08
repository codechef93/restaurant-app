<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class StockTransferProduct extends Model
{
    protected $table = 'stock_transfer_products';
    protected $hidden = ['id', 'stock_transfer_id', 'product_id', 'destination_product_id'];
    protected $fillable = ['slack', 'stock_transfer_id', 'product_id', 'product_slack', 'product_code', 'product_name', 'quantity', 'inward_type', 'accepted_quantity', 'destination_product_id', 'destination_product_slack', 'destination_product_code', 'destination_product_name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    public function scopeProduct($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.id', '=', 'stock_transfer_products.product_id');
        });
    }

    public function scopeVerifiable($query){
        return $query->where('stock_transfer_products.status', 0);
    }

    public function scopeQuantityCheck($query, $quantity){
        return $query->where('stock_transfer_products.quantity', '>=', $quantity);
    }

    /* For view files */
    
    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'STOCK_TRANSFER_PRODUCT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
