<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model
{
    protected $table = 'user_menus';
    protected $hidden = ['id'];
    protected $fillable = ['user_id', 'menu_id', 'created_by'];

    public function user(){
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }

    public function menu(){
        return $this->belongsTo('App\Models\Menu', 'id', 'menu_id');
    }
}