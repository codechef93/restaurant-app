<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtpTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event_type', 30);
            $table->integer('user_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('otp', 10)->nullable();
            $table->integer('generate_counter')->default(0);
            $table->timestamps();
            $table->index(['event_type', 'user_id', 'customer_id', 'otp']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('otp');
    }
}
