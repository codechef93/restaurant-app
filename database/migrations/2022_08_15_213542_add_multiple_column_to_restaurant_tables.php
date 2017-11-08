<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMultipleColumnToRestaurantTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            //
            $table->integer('restoarea_id');
            $table->double('x');
            $table->double('y');
            $table->double('w');
            $table->double('h');
            $table->string('rounded');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('restaurant_tables', function (Blueprint $table) {
            //
            $table->dropColumn(['restoarea_id', 'x', 'y', 'w', 'h', 'rounded']);
        });
    }
}
