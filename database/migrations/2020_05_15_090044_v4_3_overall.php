<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class V43Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('waiter_id')->nullable()->after('table_number');
            $table->integer('bill_type_id')->nullable()->after('restaurant_mode');
            $table->string('bill_type', 150)->nullable($value = true)->after('bill_type_id');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->integer('restaurant_waiter_role_id')->nullable()->after('restaurant_mode');
            $table->integer('restaurant_billing_type_id')->nullable()->after('restaurant_waiter_role_id');
        });

        Artisan::call('db:seed', [
            '--class' => v4_3_overall_seeder::class,
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
