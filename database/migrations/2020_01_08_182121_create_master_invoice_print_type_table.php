<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMasterInvoicePrintTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_invoice_print_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('print_type_value', 50);
            $table->string('print_type_label', 250);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        
        Artisan::call('db:seed', [
            '--class' => master_invoice_print_type_seeder::class,
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
        Schema::dropIfExists('master_invoice_print_type');
    }
}
