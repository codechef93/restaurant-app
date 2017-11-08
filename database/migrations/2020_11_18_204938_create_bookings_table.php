<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');
            $table->string('event_code', 30)->unique();
            $table->string('event_type', 15)->default('BOOKING');
            $table->dateTimeTz('start_date');
            $table->dateTimeTz('end_date');
            $table->string('name', 250)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 15)->nullable();
            $table->text('description')->nullable();
            $table->integer('no_of_persons')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'start_date', 'end_date', 'email', 'phone']);
        });

        Artisan::call('db:seed', [
            '--class' => bookings_seeder::class,
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
        Schema::dropIfExists('bookings');
    }
}
