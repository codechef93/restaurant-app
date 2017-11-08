<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class V3Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('stores', function (Blueprint $table) {
            $table->integer('restaurant_mode')->default(0)->after('currency_code');
            $table->index(['restaurant_mode']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('order_type_id')->nullable($value = true)->after('business_account_id');
            $table->string('order_type', 250)->nullable($value = true)->after('order_type_id');
            $table->integer('restaurant_mode')->default(0)->after('order_type');
            $table->integer('table_id')->nullable($value = true)->after('restaurant_mode');
            $table->string('table_number', 250)->nullable($value = true)->after('table_id');
            $table->integer('kitchen_status')->nullable($value = true)->after('status');
            $table->index(['kitchen_status']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->string('icon', 200)->nullable($value = true)->after('sort_order');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->integer('language_id')->nullable($value = true)->after('store_id');
            $table->index(['language_id']);
        });

        //seeders
        Artisan::call('db:seed', [
            '--class' => v3_overall_seeder::class,
            '--force' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
