<?php

namespace App\Http\Middleware;

use Closure;
use Exception;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\App;

use App\Models\User as UserModel;
use App\Models\Menu as MenuModel;
use App\Models\Store as StoreModel;
use App\Models\UserStore as UserStoreModel;
use App\Models\Language as LanguageModel;
use App\Models\Notification as NotificationModel;

class AuthenticateUser
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
        $api = ($request->segment(1) == "api") ? true : false;
        try {
            $token = ($api) ? $request->input("access_token") : session()->get("access_token");
            if (!isset($token) || is_null($token)) {
                throw new Exception("Token missing");
            }
            
            $token_decode = (new Controller())->jwt_decode($token, env('JWT_KEY', config('aconfig.jwt_key')), ['HS256']);
            $decoded_data = $token_decode->sub;
            $user_id = $decoded_data->user_id;
            $user_slack = $decoded_data->user_slack;
            
            $user_exists = UserModel::select("users.*")
            ->join('user_access_tokens', 'user_access_tokens.user_id', '=', 'users.id')
            ->where(['users.id' => $user_id, "users.slack" => $user_slack , "user_access_tokens.user_id" => $user_id, "user_access_tokens.access_token" => $token])
            ->active()
            ->first();

            if (!empty($user_exists)) {

                $request->logged_user_id        = $user_id;
                $request->logged_user_slack     = $user_exists->slack;
                $request->logged_user_role_id   = $user_exists->role_id;
                $request->is_super_admin        = ($user_exists->role_id == 1)?true:false;
                
                $menus = $this->get_user_menu($request, $user_id);
                $request->logged_user_menus = $menus;
                
                $user_stores = $this->get_available_stores($request, $user_id);
                $selected_store = $this->check_store($request, $user_id);

                $language_array = $this->get_selected_languages($user_id);

                App::setLocale($language_array['language_code']);
                
                if(!empty($selected_store)){
                    $request->logged_user_store_id = $user_exists->store_id;
                    $request->logged_user_store_slack = $selected_store->store_slack;
                    $request->logged_user_store_code = $selected_store->store_code;
                    $request->logged_user_store_name = $selected_store->name;
                    $request->logged_user_store_restaurant_mode = $selected_store->restaurant_mode;
                    $request->logged_user_store_currency = $selected_store->currency_code;
                }else{
                    if(!$request->route()->named('select_store') && !$request->route()->named('add_store') && $api == false){
                        return redirect('select_store');
                    }
                }
                
                $languages = $this->get_languages();
                $unread_notifications = $this->unread_notification($request->logged_user_id);

                View::share('logged_user_data', [
                    "fullname"          => $user_exists->fullname,
                    "profile_image"     => get_profile_photo($user_exists->profile_image),
                    "user_stores"       => $user_stores,
                    "selected_store"    => $selected_store,
                    
                    "languages"         => $languages,
                    "selected_language" => $language_array['language_label'],
                    "selected_language_code" => $language_array['language_code'],

                    "unread_notifications"  => $unread_notifications,
                    "all_notifications_link" => route('notifications'),

                    "new_order_access" => check_access(['A_ADD_ORDER'] ,true),
                    "new_order_link" => route('add_order'),

                    "profile_link" => route('profile', ['slack' => $request->logged_user_slack]),
                    "logout_link" => route('logout'),
                ]);

                return $next($request);
            }else{
                throw new Exception("Invalid token");
            }

        }catch(ExpiredException $e){
            if ($api) {
                return response()->json((new Controller())->generate_response(
                    array(
                        "message" => $e->getMessage(),
                        "status_code" => $e->getCode()
                    )
                ));
            }else{
                return redirect()->route('logout');
            }
        }catch(Exception $e){
            if ($api) {
                return response()->json((new Controller())->generate_response(
                    array(
                        "message" => $e->getMessage(),
                        "status_code" => $e->getCode()
                    )
                ));
            }else{
                return redirect()->route('logout');
            }
        }
    }

    public function get_user_menu($request, $user_id){
        $menus = [];
        if($request->logged_user_role_id == 1){
            $menus = MenuModel::select('id')
            ->active()
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()->pluck('id')->toArray();
        }else{
            $menus = DB::table('user_menus')
            ->select('menus.id')
            ->leftJoin('menus', function ($join) {
                $join->on('menus.id', '=', 'user_menus.menu_id');
                $join->where('menus.status', '=', 1);
            })
            ->where('user_menus.user_id', $user_id)
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->get()->pluck('id')->toArray();
        }
        return $menus;
    }

    public function get_available_stores($request, $user_id){
        $user_stores = [];
        if($request->logged_user_role_id == 1){
            $user_stores = StoreModel::select('slack as store_slack','store_code', 'name', 'address')
            ->active()
            ->orderBy('store_code', 'ASC')
            ->get();
        }else{
            $user_stores = UserStoreModel::select('stores.slack as store_slack','store_code', 'name', 'address')
            ->where([
                ['user_stores.user_id', '=', $user_id ]
            ])
            ->storeData()
            ->orderBy('store_code', 'ASC')
            ->get();
        }
        return (object) $user_stores;
    }

    public function check_store($request, $user_id){

        $store_data = [];

        $user_data = UserModel::select("users.store_id")
        ->where([
            ['users.id', '=', $user_id]
        ])
        ->first();

        $store_id = $user_data->store_id;
        
        if($store_id != ''){
            
            $user_stores = UserStoreModel::where('user_id', '=', $user_id)
            ->pluck('store_id')
            ->toArray();
            (count($user_stores) >0 )?sort($user_stores):$user_stores;
                
            if(!in_array($store_id, $user_stores) && $request->logged_user_role_id != 1){
                $user_update_array = [        
                    "store_id" => NULL,
                ];
                $data = UserModel::where('id', $user_id)
                ->update($user_update_array);
            }else{
                $store_data = StoreModel::select('stores.slack as store_slack','store_code', 'name', 'address', 'restaurant_mode', 'currency_code')
                ->where('id', '=', trim($store_id))
                ->active()
                ->first();

                if(empty($store_data)){
                    $user_update_array = [        
                        "store_id" => NULL,
                    ];
                    $data = UserModel::where('id', $user_id)
                    ->update($user_update_array);
                }
            }
        }

        return $store_data;
    }

    public function get_selected_languages($user_id){
        $user_language_data = UserModel::select('language_id')->where('id', $user_id)->first();
        
        if(empty($user_language_data->language_id)){
            
            $language_array = [
                'language_code' => 'en',
                'language_label' => "English - en"
            ];

        }else{
            $language = LanguageModel::select("language_code", "language")->where('id', $user_language_data->language_id)->active()->first();

            $language_array = [
                'language_code' => $language->language_code,
                'language_label' => $language->language. " - ".$language->language_code
            ];
        }
        
        return $language_array;
    }

    public function get_languages(){
        $languages = LanguageModel::select("*")->active()->orderBy("language", "asc")->get();
        return $languages;
    }

    public function unread_notification($user_id){
        $unread_count = NotificationModel::select("slack")->active()->where("read", 0)->where('user_id', $user_id)->get()->count();
        return (($unread_count>0)?true:false);
    }
}
