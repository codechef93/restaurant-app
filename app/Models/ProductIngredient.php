<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    protected $table = 'product_ingredients';
    protected $hidden = ['id'];
    protected $fillable = ['slack', 'product_id', 'ingredient_product_id', 'quantity', 'measurement_unit_id', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeCreatedUser($query){
        return $query->leftJoin('users AS user_created', function ($join) {
            $join->on('user_created.id', '=', 'product_ingredients.created_by');
        });
    }

    public function scopeUpdatedUser($query){
        return $query->leftJoin('users AS user_updated', function ($join) {
            $join->on('user_created.id', '=', 'product_ingredients.updated_by');
        });
    }

    /* For view files */

    public function ingredient_product(){
        return $this->hasOne('App\Models\Product', 'id', 'ingredient_product_id');
    }

    public function measurement_unit(){
        return $this->hasOne('App\Models\MeasurementUnit', 'id', 'measurement_unit_id');
    }

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
