<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStore extends Model
{
    protected $table = 'user_stores';
    protected $hidden = ['id'];
    protected $fillable = ['user_id', 'store_id', 'created_by'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }

    public function store(){
        return $this->belongsTo('App\Models\Store', 'id', 'store_id');
    }

    public function scopeStoreData($query){
        return $query->leftJoin('stores', function ($join) {
            $join->on('stores.id', '=', 'user_stores.store_id');
            $join->where('stores.status', '=', 1);
        });
    }
}