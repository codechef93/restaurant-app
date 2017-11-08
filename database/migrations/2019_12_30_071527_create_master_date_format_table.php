<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMasterDateFormatTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_date_format', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 50);
            $table->string('date_format_value', 50);
            $table->string('date_format_label', 250);
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
        });

        Artisan::call('db:seed', [
            '--class' => master_date_format_seeder::class,
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
        Schema::dropIfExists('master_date_format');
    }
}
