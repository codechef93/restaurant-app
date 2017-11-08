<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateBusinessRegisterTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_registers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');
            $table->integer('user_id');
            $table->dateTime('opening_date');
            $table->dateTime('closing_date')->nullable();
            $table->decimal('opening_amount', 13, 2)->default(0);
            $table->decimal('closing_amount', 13, 2)->default(0);
            $table->integer('credit_card_slips')->default(0);
            $table->integer('cheques')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'store_id'], 'business_register_indexes');
        });

        Artisan::call('db:seed', [
            '--class' => business_register_table_seeder::class,
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
        Schema::dropIfExists('business_register');
    }
}
