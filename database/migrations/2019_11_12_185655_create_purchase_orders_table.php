<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');

            $table->string('po_number', 50)->unique();
            $table->string('po_reference', 30)->nullable();

            $table->date('order_date')->nullable();
            $table->date('order_due_date')->nullable();

            $table->integer('supplier_id');
            $table->string('supplier_code', 30);
            $table->string('supplier_name', 250);
            $table->text('supplier_address')->nullable();

            $table->string('currency_name', 50)->nullable();
            $table->string('currency_code', 30)->nullable();
            
            $table->decimal('subtotal_excluding_tax', 13, 2)->default(0);
            $table->decimal('total_discount_amount', 13, 2)->default(0);
            $table->decimal('total_after_discount', 13, 2)->default(0);
            $table->decimal('total_tax_amount', 13, 2)->default(0);
            $table->decimal('shipping_charge', 13, 2)->default(0);
            $table->decimal('packing_charge', 13, 2)->default(0);
            $table->decimal('total_order_amount', 13, 2)->default(0);

            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'po_number', 'supplier_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('purchase_orders');
    }
}
