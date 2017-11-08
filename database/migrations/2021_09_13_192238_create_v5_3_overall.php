<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV53Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('enable_variants_popup')->default(1)->after('enable_customer_popup');
            $table->integer('restaurant_chef_role_id')->nullable()->after('restaurant_mode');
        });

        Schema::table('notifications', function (Blueprint $table) {
            $table->integer('store_id')->nullable()->after('user_id');
        });

        Artisan::call('db:seed', [
            '--class' => v5_3_overall_seeder::class,
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
