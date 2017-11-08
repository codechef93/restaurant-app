<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->string('store_code', 30)->unique();
            $table->string('name', 250);
            $table->string('tax_number', 250)->nullable();
            $table->integer('tax_code_id')->nullable();
            $table->integer('discount_code_id')->nullable();
            $table->text('address')->nullable();
            $table->string('pincode', 15)->nullable();
            $table->string('primary_contact', 15)->nullable();
            $table->string('secondary_contact', 15)->nullable();
            $table->string('primary_email', 150)->nullable();
            $table->string('secondary_email', 150)->nullable();
            $table->string('invoice_type', 50)->default('SMALL');
            $table->string('currency_name', 50)->nullable();
            $table->string('currency_code', 30)->nullable()->default('USD');
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('store');
    }
}
