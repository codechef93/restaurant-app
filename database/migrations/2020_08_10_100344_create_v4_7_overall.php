<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateV47Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_app', function (Blueprint $table) {
            $table->renameColumn('company_logo', 'invoice_print_logo');
        });

        Schema::table('setting_app', function (Blueprint $table) {
            $table->text('company_logo')->nullable()->after('invoice_print_logo');
            $table->text('navbar_logo')->nullable()->after('company_logo');
            $table->text('favicon')->nullable()->after('navbar_logo');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('enable_customer_popup')->default(0)->after('restaurant_billing_type_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('is_ingredient')->default(0)->after('sale_amount_excluding_tax');
            $table->tinyInteger('is_ingredient_price')->default(0)->after('is_ingredient');
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->tinyInteger('is_restaurant_menu')->default(0)->after('icon');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->tinyInteger('is_ready_to_serve')->default(0)->after('total_amount');
        });

        Artisan::call('db:seed', [
            '--class' => v4_7_overall_seeder::class,
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
