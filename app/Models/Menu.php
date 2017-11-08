<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus';
    protected $hidden = ['id'];

    public function scopeActive($query){
        return $query->where('status', 1);
    }
}