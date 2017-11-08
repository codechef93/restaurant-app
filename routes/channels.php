<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

use Illuminate\Support\Facades\Log;

use App\Models\Order as OrderModel;
use App\Models\Store as StoreModel;
use App\Models\UserStore as UserStoreModel;

Broadcast::channel('new-order-chef.{store_slack}', function ($user, $store_slack) {
    
    $store_data = StoreModel::select('id', 'restaurant_chef_role_id')
    ->where([
        ['slack', '=', $store_slack],
    ])
    ->first()->makeVisible(['id']);
    if(empty($store_data)){
        return;
    }
    
    $user_store_access = UserStoreModel::select('user_stores.id')
    ->where([
        ['user_stores.user_id', '=', $user->id],
        ['user_stores.store_id', '=', $store_data->id]
    ])
    ->count();

    if($store_data->restaurant_chef_role_id == $user->role_id && $user_store_access>0){
        return true;
    }

    return false;
});

Broadcast::channel('new-order-waiter.{store_slack}.{waiter_slack}', function ($user, $store_slack, $waiter_slack) {  
    $store_data = StoreModel::select('id', 'restaurant_waiter_role_id')
    ->where([
        ['slack', '=', $store_slack],
    ])
    ->first()->makeVisible(['id']);
    if(empty($store_data)){
        return;
    }

    $user_store_access = UserStoreModel::select('user_stores.id')
    ->where([
        ['user_stores.user_id', '=', $user->id],
        ['user_stores.store_id', '=', $store_data->id]
    ])
    ->count();

    $waiter_role = $store_data->restaurant_waiter_role_id == $user->role_id;
    $waiter_user = $waiter_slack == $user->slack;

    if($waiter_role && $waiter_user && $user_store_access>0){
        return true;
    }
    return false;
});