<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV52Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('payment_status')->default(0)->after('kitchen_status');
            $table->tinyInteger('order_merged')->default(0)->after('payment_status');
            $table->integer('order_merge_parent_id')->nullable()->after('order_merged');
            $table->tinyInteger('kitchen_screen_dismissed')->default(0)->after('order_merge_parent_id');
            $table->tinyInteger('waiter_screen_dismissed')->default(0)->after('kitchen_screen_dismissed');
            $table->string('customer_name', 250)->nullable()->after('customer_id');
            $table->string('customer_phone', 25)->nullable()->change();
            $table->string('customer_email', 150)->nullable()->change();
            $table->index(['payment_status', 'order_merged', 'order_merge_parent_id']);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->integer('merged_from')->nullable()->after('is_ready_to_serve');
            $table->integer('merged_to')->nullable()->after('merged_from');
            $table->index(['merged_from', 'merged_to']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->tinyInteger('transaction_merged')->default(0)->after('transaction_date');
            $table->integer('merged_from')->nullable()->after('transaction_merged');
            $table->integer('merged_to')->nullable()->after('merged_from');
            $table->index(['merged_from', 'merged_to']);
        });

        Schema::table('payment_methods', function (Blueprint $table) {
            $table->integer('activate_on_digital_menu')->default(0)->after('description');
            $table->index(['activate_on_digital_menu']);
        });

        Artisan::call('db:seed', [
            '--class' => v5_2_overall_seeder::class,
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
