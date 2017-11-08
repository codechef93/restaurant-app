<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateV49Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_type', 30)->nullable()->change();
            $table->string('order_origin', 25)->default('POS_WEB')->after('waiter_id')->comment('POS_WEB, DIGITAL_MENU');
            $table->index(['order_origin']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('enable_digital_menu_otp_verification')->default(1)->after('enable_customer_popup');
            $table->integer('menu_language_id')->nullable()->after('enable_digital_menu_otp_verification');
            $table->index(['enable_digital_menu_otp_verification', 'menu_language_id'], 'menu_otp_language_index');
        });

        Artisan::call('db:seed', [
            '--class' => v4_9_overall_seeder::class,
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
