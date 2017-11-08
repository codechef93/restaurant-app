<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateSettingSmsGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_sms_gateways', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->string('account_id', 150);
            $table->string('token', 150);
            $table->string('twilio_number', 50);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['account_id', 'token', 'twilio_number']);
        });

        Artisan::call('db:seed', [
            '--class' => setting_sms_gateways_table_seeder::class,
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
        Schema::dropIfExists('setting_sms_gateways');
    }
}
