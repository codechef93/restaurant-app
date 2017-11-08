<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');
            $table->string('order_number', 30)->unique();
            $table->integer('customer_id');
            $table->string('customer_phone', 15);
            $table->string('customer_email', 150);

            $table->integer('store_level_discount_code_id')->nullable();
            $table->string('store_level_discount_code', 30)->nullable();
            $table->decimal('store_level_total_discount_percentage', 8, 2)->default(0);
            $table->decimal('store_level_total_discount_amount', 13, 2)->default(0);
            $table->decimal('product_level_total_discount_amount', 13, 2)->default(0);

            $table->integer('store_level_tax_code_id')->nullable();
            $table->string('store_level_tax_code', 30)->nullable();
            $table->decimal('store_level_total_tax_percentage', 8, 2)->default(0);
            $table->decimal('store_level_total_tax_amount', 13, 2)->default(0);
            $table->text('store_level_total_tax_components')->nullable();
            $table->decimal('product_level_total_tax_amount', 13, 2)->default(0);

            $table->decimal('purchase_amount_subtotal_excluding_tax', 13, 2)->default(0);
            $table->decimal('sale_amount_subtotal_excluding_tax', 13, 2)->default(0);
            $table->decimal('total_discount_amount', 13, 2)->default(0);
            $table->decimal('total_after_discount', 13, 2)->default(0);
            $table->decimal('total_tax_amount', 13, 2)->default(0);
            $table->decimal('total_order_amount', 13, 2)->default(0);

            $table->integer('payment_method_id')->nullable();
            $table->string('payment_method_slack', 30)->nullable();
            $table->string('payment_method', 50)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['customer_id', 'store_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
