<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            
            $table->integer('store_id');
            $table->string('transaction_code', 30)->unique();

            $table->integer('account_id');

            $table->integer('transaction_type');

            $table->integer('payment_method_id')->nullable();
            $table->string('payment_method', 50)->nullable();

            $table->string('bill_to', 50)->comment('POS_ORDER, INVOICE, CUSTOMER, SUPPLIER')->nullable();
            $table->integer('bill_to_id')->nullable();
            $table->string('bill_to_name', 250)->nullable();
            $table->string('bill_to_contact', 150)->nullable();
            $table->text('bill_to_address')->nullable();
            
            $table->string('currency_code', 30);
            $table->decimal('amount', 13, 2)->default(0);

            $table->text('notes')->nullable();

            $table->string('pg_transaction_id', 250)->nullable();
            $table->string('pg_transaction_status', 100)->nullable();

            $table->date('transaction_date')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

            $table->timestamps();
            $table->index(['store_id', 'account_id', 'transaction_type', 'bill_to', 'bill_to_id'], 'transaction_indexes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
