<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Models\Scopes\StoreScope;

class Category extends Model
{
    protected $table = 'category';
    protected $hidden = ['id', 'store_id'];
    protected $fillable = ['slack', 'store_id', 'category_code', 'label', 'description', 'display_on_pos_screen', 'display_on_qr_menu', 'status', 'created_by', 'updated_by','type','category_order'];

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new StoreScope);
    }

    public function scopeActive($query){
        return $query->where('category.status', 1);
    }

    public function scopeSortLabelAsc($query){
        return $query->orderBy('category.label', 'asc');
    }

    public function scopeSortCategoryOrderAsc($query){
        return $query->orderBy('category.category_order', 'asc');
    }

    public function scopeShowPosScreen($query){
        return $query->where('category.display_on_pos_screen', 1);
    }

    public function scopeShowQrMenu($query){
        return $query->where('category.display_on_qr_menu', 1);
    }

    public function scopeStatusJoin($query){
        return $query->leftJoin('master_status', function ($join) {
            $join->on('master_status.value', '=', 'category.status');
            $join->where('master_status.key', '=', 'CATEGORY_STATUS');
        });
    }

    public function scopeProductJoin($query){
        return $query->leftJoin('products', function ($join) {
            $join->on('products.category_id', '=', 'category.id');
        });
    }

    public function scopeOrderProductJoin($query){
        return $query->leftJoin('order_products', function ($join) {
            $join->on('order_products.product_id', '=', 'products.id');
        });
    }

    public function scopeOrderJoin($query){
        return $query->leftJoin('orders', function ($join) {
            $join->on('orders.id', '=', 'order_products.order_id');
        });
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'category.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'category.updated_by');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'CATEGORY_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
