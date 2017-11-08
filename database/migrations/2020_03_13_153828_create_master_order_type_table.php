<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMasterOrderTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_order_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_type_constant', 50)->unique();
            $table->string('label', 250);
            $table->text('description')->nullable();
            $table->tinyInteger('restaurant')->default(0);
            $table->string('icon', 200)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['order_type_constant', 'status']);
        });

        //seeders
        Artisan::call('db:seed', [
            '--class' => master_order_type_seeder::class,
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
        Schema::dropIfExists('master_order_type');
    }
}
