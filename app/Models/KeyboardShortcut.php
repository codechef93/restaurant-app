<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class KeyboardShortcut extends Model
{
    protected $table = 'keyboard_shortcuts';
    protected $hidden = ['id'];
    protected $fillable = ['keyboard_constant', 'keyboard_shortcut', 'keyboard_shortcut_label', 'description', 'sort_order', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
    }

    public function scopeActive($query){
        return $query->where('keyboard_shortcuts.status', 1);
    }

    public function scopeSortAsc($query){
        return $query->orderBy('keyboard_shortcuts.sort_order', 'asc');
    }

    /* For view files */

    public function createdUser(){
        return $this->hasOne('App\Models\User', 'id', 'created_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }

    public function updatedUser(){
        return $this->hasOne('App\Models\User', 'id', 'updated_by')->select(['slack', 'fullname', 'email', 'user_code']);
    }
    
    public function status_data(){
        return $this->hasOne('App\Models\MasterStatus', 'value', 'status')->where('key', 'KEYBOARD_SHORTCUT_STATUS');
    }

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
