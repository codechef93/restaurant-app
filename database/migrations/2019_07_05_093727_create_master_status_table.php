<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMasterStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 25);
            $table->string('value', 15);
            $table->string('value_constant', 25)->nullable();
            $table->string('label',100);
            $table->string('color',100);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->index(['key', 'value', 'value_constant', 'status']);
        });

        Artisan::call('db:seed', [
            '--class' => master_status_table_seeder::class,
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
        Schema::dropIfExists('master_status');
    }
}
