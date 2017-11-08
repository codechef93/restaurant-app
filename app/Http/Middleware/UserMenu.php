<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Menu as MenuModel;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class UserMenu
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
        $menu_array = array();
        $logged_in_user = $request->logged_user_id;
        $restaurant_mode = $request->logged_user_store_restaurant_mode;

        if($request->logged_user_role_id == 1){
            $menus = MenuModel::select('*')
            ->active()
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->when($restaurant_mode == 0, function ($menus) {
                $menus->where('menus.is_restaurant_menu', 0);
            })
            ->get();
        }else{
            $menus = DB::table('user_menus')
            ->select('menus.*')
            ->join('menus', 'menus.id', '=', 'user_menus.menu_id')
            ->where('user_menus.user_id', $logged_in_user)
            ->orderByRaw('FIELD(type , "MAIN_MENU", "SUB_MENU") ASC')
            ->orderBy('sort_order', 'ASC')
            ->when($restaurant_mode == 0, function ($menus) {
                $menus->where('menus.is_restaurant_menu', 0);
            })
            ->get();
        }
        
        foreach($menus as $menu){
            if($menu->type == "MAIN_MENU"){
                $menu_array[$menu->id] = [
                    "menu_key" => $menu->menu_key,
                    "label" => $menu->label,
                    "route" => ($menu->route != '')?route($menu->route):'#',
                    "icon"  => $menu->icon,
                    "sort_order"  => $menu->sort_order
                ];
            }else if($menu->type == "SUB_MENU"){
                if(isset($menu_array[$menu->parent])){
                    unset($menu_array[$menu->parent]["route"]);
                    $menu_array[$menu->parent]['sub_menu'][] = [
                        "sub_menu_id" => $menu->id,
                        "menu_key" => $menu->menu_key,
                        "label" => $menu->label,
                        "route" => ($menu->route != '')?route($menu->route):'#',
                        "sort_order"  => $menu->sort_order
                    ];
                }
            }
        }
        
        View::share('menus', $menu_array);

        $quick_links = [];
        if(check_access(array('A_ADD_NOTIFICATION'), true)){
            $quick_links[] = [
                'label' => 'New Notification',
                'route' => route('add_notification')
            ];
        }
        if(check_access(array('SM_RESTAURANT_KITCHEN'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Kitchen View',
                'route' => route('kitchen')
            ];
        }
        if(check_access(array('SM_RESTAURANT_BAR'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Bar View',
                'route' => route('bar')
            ];
        }
        if(check_access(array('SM_RESTAURANT_Kitchen1'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Kitchen1 View',
                'route' => route('kitchen1')
            ];
        }
        if(check_access(array('SM_RESTAURANT_Kitchen2'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Kitchen2 View',
                'route' => route('kitchen2')
            ];
        }
        if(check_access(array('SM_RESTAURANT_Kitchen3'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Kitchen3 View',
                'route' => route('kitchen3')
            ];
        }
        if(check_access(array('SM_RESTAURANT_WAITER'), true) && $request->logged_user_store_restaurant_mode == 1){
            $quick_links[] = [
                'label' => 'Waiter View',
                'route' => route('waiter')
            ];
        }
        if(check_access(array('A_ADD_CUSTOMER'), true)){
            $quick_links[] = [
                'label' => 'New Customer',
                'route' => route('add_customer')
            ];
        }
        if(check_access(array('A_ADD_ORDER'), true)){
            $quick_links[] = [
                'label' => 'New Order',
                'route' => route('add_order')
            ];
        }
        if(check_access(array('A_ADD_TRANSACTION'), true)){
            $quick_links[] = [
                'label' => 'New Transaction',
                'route' => route('add_transaction')
            ];
        }
        if(check_access(array('A_ADD_INVOICE'), true)){
            $quick_links[] = [
                'label' => 'New Invoice',
                'route' => route('add_invoice')
            ];
        }
        if(check_access(array('A_ADD_PURCHASE_ORDER'), true)){
            $quick_links[] = [
                'label' => 'New Purchase Order',
                'route' => route('add_purchase_order')
            ];
        }
        if(check_access(array('A_ADD_QUOTATION'), true)){
            $quick_links[] = [
                'label' => 'New Quotation',
                'route' => route('add_quotation')
            ];
        }
        if(check_access(array('A_ADD_BOOKING'), true)){
            $quick_links[] = [
                'label' => 'New Booking',
                'route' => route('add_booking')
            ];
        }

        View::share('quick_links', $quick_links);

        return $next($request);
    }
}
