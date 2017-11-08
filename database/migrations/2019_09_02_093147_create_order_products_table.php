<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('order_id');
            $table->integer('product_id');
            $table->string('product_slack', 30);
            $table->string('product_code', 30);
            $table->string('name', 250);

            $table->decimal('quantity', 8, 2);
            $table->decimal('purchase_amount_excluding_tax', 13, 2);
            $table->decimal('sale_amount_excluding_tax', 13, 2);

            $table->integer('discount_code_id')->nullable();
            $table->string('discount_code', 30)->nullable();
            $table->decimal('discount_percentage', 8, 2)->default(0);

            $table->integer('tax_code_id')->nullable();
            $table->string('tax_code', 30)->nullable();
            $table->decimal('tax_percentage', 8, 2)->default(0);
            $table->text('tax_components')->nullable();

            $table->decimal('sub_total_purchase_price_excluding_tax', 13, 2);
            $table->decimal('sub_total_sale_price_excluding_tax', 13, 2);
            $table->decimal('discount_amount', 13, 2)->default(0);
            $table->decimal('total_after_discount', 13, 2)->default(0);
            $table->decimal('tax_amount', 13, 2)->default(0);
            $table->decimal('total_amount', 13, 2);

            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'product_id', 'product_code', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
