<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    protected $table = 'role_menus';
    protected $hidden = ['id'];
    protected $fillable = ['role_id', 'menu_id', 'created_by', 'updated_by'];

    public function role(){
        return $this->belongsTo('App\Models\Role', 'id', 'role_id');
    }

    public function menu(){
        return $this->belongsTo('App\Models\Menu', 'id', 'menu_id');
    }
}