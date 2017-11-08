<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMasterTaxOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('master_tax_option', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tax_option_constant', 50)->unique();
            $table->string('label', 250);
            $table->integer('component_count')->default(1);
            $table->string('component_1', 150)->nullable();
            $table->string('component_2', 150)->nullable();
            $table->string('component_3', 150)->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['status']);
        });

        Artisan::call('db:seed', [
            '--class' => master_tax_option_seeder::class,
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
        Schema::dropIfExists('master_tax_option');
    }
}
