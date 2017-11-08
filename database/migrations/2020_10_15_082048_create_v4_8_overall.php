<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV48Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('setting_app', function (Blueprint $table) {
            $table->string('app_title', 250)->nullable($value = true)->after('company_name');
            $table->string('timezone', 100)->default('UTC')->after('app_title');
            $table->index(['app_title']);
        });

        Schema::table('category', function (Blueprint $table) {
            $table->tinyInteger('display_on_pos_screen')->default(1)->after('description');
            $table->tinyInteger('display_on_qr_menu')->default(1)->after('display_on_pos_screen');
        });

        Schema::table('setting_sms_gateways', function (Blueprint $table) {
            $table->string('gateway_type', 30)->after('slack');
            $table->string('account_id', 150)->nullable()->change();
            $table->string('token', 150)->nullable()->change();
            $table->string('twilio_number', 50)->nullable()->change();
            $table->string('auth_key', 100)->nullable()->after('twilio_number');
            $table->string('sender_id', 20)->nullable()->after('auth_key');
        });

        Artisan::call('db:seed', [
            '--class' => v4_8_overall_seeder::class,
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
