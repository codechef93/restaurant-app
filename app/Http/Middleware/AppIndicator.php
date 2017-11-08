<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;

use App\Models\AppActivation;

use Closure;

class AppIndicator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $activation_data = AppActivation::select('activation_code')->first();
        
        $indicator = '';
        if(App::environment('demo') == true){
            $indicator = Config::get('constants.demo_notification');
        }else if(!isset($activation_data->activation_code)){
            $indicator = Config::get('constants.activation_notification');
        }

        View::share('app_indicator', [
            "indicator" => $indicator
        ]);

        return $next($request);
    }
}
