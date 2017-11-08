<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotation_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();

            $table->integer('quotation_id');

            $table->integer('product_id')->nullable();
            $table->string('product_slack', 30)->nullable();
            $table->string('product_code', 30)->nullable();
            $table->string('name', 250);

            $table->decimal('quantity', 8, 2)->default(0);

            $table->decimal('amount_excluding_tax', 13, 2)->default(0);
            $table->decimal('subtotal_amount_excluding_tax', 13, 2)->default(0);

            $table->decimal('discount_percentage', 8, 2)->default(0);

            $table->decimal('tax_percentage', 8, 2)->default(0);

            $table->decimal('discount_amount', 13, 2)->default(0);
            $table->decimal('total_after_discount', 13, 2)->default(0);
            $table->decimal('tax_amount', 13, 2)->default(0);
            $table->text('tax_components')->nullable();
            
            $table->decimal('total_amount', 13, 2)->default(0);

            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['quotation_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotation_products');
    }
}
