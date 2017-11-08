<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockTransferProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfer_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();

            $table->integer('stock_transfer_id');

            $table->integer('product_id');
            $table->string('product_slack', 30);
            $table->string('product_code', 30);
            $table->string('product_name', 250);

            $table->decimal('quantity', 8, 2)->default(0);

            $table->string('inward_type', 30)->nullable()->comment('MERGE, NEW');
            $table->decimal('accepted_quantity', 8, 2)->nullable();
            $table->integer('destination_product_id')->nullable();
            $table->string('destination_product_slack', 30)->nullable();
            $table->string('destination_product_code', 30)->nullable();
            $table->string('destination_product_name', 250)->nullable();

            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['stock_transfer_id', 'product_id', 'destination_product_id', 'status'], 'stock_transfer_product_indexes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_transfer_products');
    }
}
