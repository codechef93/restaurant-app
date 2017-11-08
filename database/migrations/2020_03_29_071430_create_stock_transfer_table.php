<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateStockTransferTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();

            $table->integer('store_id');

            $table->string('stock_transfer_reference', 30)->unique();

            $table->integer('from_store_id');
            $table->string('from_store_code', 30)->nullable();
            $table->string('from_store_name', 250)->nullable();

            $table->integer('to_store_id');
            $table->string('to_store_code', 30)->nullable();
            $table->string('to_store_name', 250)->nullable();

            $table->text('notes')->nullable();

            $table->tinyInteger('status')->default(0);

            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'from_store_id', 'to_store_id', 'status']);
        });

        //seeders
        Artisan::call('db:seed', [
            '--class' => stock_transfer_table_seeder::class,
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
        Schema::dropIfExists('stock_transfer');
    }
}
