<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV45Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_returns', function (Blueprint $table) {
            $table->tinyInteger('update_stock')->default(0)->after('total_order_amount');
        });

        Schema::table('stock_return_products', function (Blueprint $table) {
            $table->tinyInteger('stock_update')->default(0)->after('total_amount');
        });
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
