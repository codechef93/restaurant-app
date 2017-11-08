<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateV44Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_registers', function (Blueprint $table) {
            $table->integer('billing_counter_id')->nullable()->after('user_id');
            $table->tinyInteger('current_register')->default(0)->after('billing_counter_id');
            $table->index(['billing_counter_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_discount_before_additional_discount', 13, 2)->default(0)->after('sale_amount_subtotal_excluding_tax');
            $table->decimal('total_amount_before_additional_discount', 13, 2)->default(0)->after('total_discount_before_additional_discount');
            $table->decimal('additional_discount_percentage', 8, 2)->default(0)->after('total_amount_before_additional_discount');
            $table->decimal('additional_discount_amount', 13, 2)->default(0)->after('additional_discount_percentage');
            $table->index(['payment_method_id']);
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->tinyInteger('update_stock')->default(0)->after('terms');
        });

        Schema::table('purchase_order_products', function (Blueprint $table) {
            $table->tinyInteger('stock_update')->default(0)->after('total_amount');
        });

        Artisan::call('db:seed', [
            '--class' => v4_4_overall_seeder::class,
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
