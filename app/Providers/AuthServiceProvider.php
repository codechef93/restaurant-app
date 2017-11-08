<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

use App\Models\User as UserModel;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::viaRequest('user-token', function ($request) {
            
            $token = $request->header('Authorization');
            
            if(empty($token)){
                return;
            }
            
            $token_decode = (new Controller())->jwt_decode($token, env('JWT_KEY', config('aconfig.jwt_key')), ['HS256']);
            $decoded_data = $token_decode->sub;
            $user_id = $decoded_data->user_id;
            $user_slack = $decoded_data->user_slack;

            $user = UserModel::select('users.id', 'users.slack', 'users.role_id')
            ->join('user_access_tokens', 'user_access_tokens.user_id', '=', 'users.id')
            ->where(['users.id' => $user_id, "users.slack" => $user_slack , "user_access_tokens.user_id" => $user_id, "user_access_tokens.access_token" => $token])
            ->active()
            ->first()->makeVisible(['id', 'role_id']);

            return $user;
        });
    }
}
