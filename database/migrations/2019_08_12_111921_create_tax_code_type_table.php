<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaxCodeTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_code_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('tax_code_id');
            $table->string('tax_type', 50);
            $table->decimal('tax_percentage', 8, 2)->default(0);
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->index(['tax_code_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tax_code_type');
    }
}
