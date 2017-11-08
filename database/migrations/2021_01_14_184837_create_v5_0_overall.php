<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateV50Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            $table->integer('waiter_user_id')->nullable()->after('no_of_occupants');
            $table->index(['waiter_user_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_addon_product')->default(0)->after('is_ingredient_price');
            $table->index(['is_addon_product', 'is_ingredient']);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->integer('parent_order_product_id')->nullable()->after('order_id');
            $table->index(['parent_order_product_id']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('digital_menu_send_order_to_kitchen')->default(0)->after('enable_digital_menu_otp_verification');
            $table->index(['digital_menu_send_order_to_kitchen']);
        });

        Artisan::call('db:seed', [
            '--class' => v5_0_overall_seeder::class,
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
