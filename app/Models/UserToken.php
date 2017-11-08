<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    protected $table = 'user_access_tokens';
    protected $hidden = ['id'];
    protected $fillable = ['user_id', 'access_token', 'session_id'];

    public function scopeGetUserToken($query, $user_id, $access_token)
    {
        return $query->where([
            ['user_id', '=', $user_id],
            ['access_token', '=', $access_token],
        ]);
    }

    /* For view files */

    public function parseDate($date){
        return ($date != null)?Carbon::parse($date)->format(config("app.date_time_format")):null;
    }
}
