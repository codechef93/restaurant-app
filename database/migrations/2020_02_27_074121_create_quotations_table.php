<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');
            
            $table->string('quotation_number', 50)->unique();
            $table->string('quotation_reference', 30)->nullable();

            $table->date('quotation_date')->nullable();
            $table->date('quotation_due_date')->nullable();

            $table->text('subject')->nullable();

            $table->string('bill_to', 30);

            $table->integer('bill_to_id');
            $table->string('bill_to_code', 30)->nullable();
            $table->string('bill_to_name', 250);
            $table->string('bill_to_email', 150)->nullable();
            $table->string('bill_to_contact', 15)->nullable();
            $table->text('bill_to_address')->nullable();

            $table->string('currency_name', 50)->nullable();
            $table->string('currency_code', 30)->nullable();

            $table->integer('tax_option_id')->nullable();
            
            $table->decimal('subtotal_excluding_tax', 13, 2)->default(0);
            $table->decimal('total_discount_amount', 13, 2)->default(0);
            $table->decimal('total_after_discount', 13, 2)->default(0);
            $table->decimal('total_tax_amount', 13, 2)->default(0);
            $table->decimal('shipping_charge', 13, 2)->default(0);
            $table->decimal('packing_charge', 13, 2)->default(0);
            $table->decimal('total_order_amount', 13, 2)->default(0);

            $table->text('notes')->nullable();
            
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['store_id', 'quotation_number', 'quotation_reference', 'bill_to', 'bill_to_id', 'status'], 'quotation_indexes');
        });

        Artisan::call('db:seed', [
            '--class' => quotation_requirement_seeder::class,
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
        Schema::dropIfExists('quotations');
    }
}
