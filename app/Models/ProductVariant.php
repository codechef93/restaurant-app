<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'variant_code', 'product_id', 'variant_option_id', 'parent_product_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'product_variants.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'product_variants.updated_by');
        });
    }

    public function scopeVariantOption($query){
        return $query->leftJoin('variant_options', function ($join) {
            $join->on('variant_options.id', '=', 'product_variants.variant_option_id');
        });
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function product_variant(){
        return $this->hasOne('App\Models\Product', 'id', 'product_id')->withoutGlobalScopes();
    }

    public function variant_option(){
        return $this->hasOne('App\Models\VariantOption', 'id', 'variant_option_id')->withoutGlobalScopes();
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
