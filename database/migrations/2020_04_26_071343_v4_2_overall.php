<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class V42Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('register_id')->nullable()->after('customer_email');
            $table->index(['register_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('alert_quantity', 8, 2)->default(0)->after('quantity');
        });

        Artisan::call('db:seed', [
            '--class' => v4_2_overall_seeder::class,
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
