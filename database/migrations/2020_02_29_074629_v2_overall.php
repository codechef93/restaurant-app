<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class v2Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('master_status', function (Blueprint $table) {
            $table->string('key', 50)->change();
            $table->string('value_constant', 50)->change();
        });

        DB::statement('ALTER TABLE `menus` CHANGE `type` `type` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL');
        Schema::table('menus', function (Blueprint $table) {
            $table->string('menu_key', 50)->change();
            $table->string('label', 100)->change();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->integer('country_id')->after('address');
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->string('payment_constant', 30)->nullable($value = true)->after('slack');
            $table->text('key_1')->nullable($value = true)->after('label');
            $table->text('key_2')->nullable($value = true)->after('key_1');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->text('terms')->nullable($value = true)->after('total_order_amount');
            $table->integer('tax_option_id')->nullable($value = true)->after('currency_code');
        });

        Schema::table('purchase_order_products', function (Blueprint $table) {
            $table->text('tax_components')->nullable($value = true)->after('tax_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_order_amount_rounded', 13, 0)->default(0)->after('total_order_amount');
            $table->string('currency_name', 50)->nullable($value = true)->after('payment_method');
            $table->string('currency_code', 30)->nullable($value = true)->after('currency_name');
            $table->integer('business_account_id')->nullable($value = true)->after('currency_code');
        });

        //seeders
        Artisan::call('db:seed', [
            '--class' => v2_overall_seeder::class,
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
