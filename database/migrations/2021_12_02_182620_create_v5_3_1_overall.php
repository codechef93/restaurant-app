<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV531Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tax_codes', function (Blueprint $table) {
            $table->enum('tax_type', ['EXCLUSIVE', 'INCLUSIVE'])->default('EXCLUSIVE')->after('store_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('sale_amount_including_tax', 13, 2)->default(0)->after('sale_amount_excluding_tax');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->decimal('sale_amount_including_tax', 13, 2)->default(0)->after('sale_amount_excluding_tax');
        });

        Schema::table('purchase_order_products', function (Blueprint $table) {
            $table->string('tax_type', 15)->default('EXCLUSIVE')->after('discount_percentage');
        });

        Schema::table('invoice_products', function (Blueprint $table) {
            $table->string('tax_type', 15)->default('EXCLUSIVE')->after('discount_percentage');
        });

        Artisan::call('db:seed', [
            '--class' => v5_3_1_overall_seeder::class,
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
        //
    }
}
