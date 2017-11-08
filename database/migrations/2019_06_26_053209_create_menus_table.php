<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['MAIN_MENU', 'SUB_MENU', 'ACTIONS']);
            $table->string('menu_key', 30)->unique();
            $table->string('label', 50);
            $table->string('route', 200)->nullable();
            $table->integer('parent')->default(0);
            $table->integer('sort_order')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->index(['type', 'menu_key', 'parent', 'status']);
        });

        Artisan::call('db:seed', [
            '--class' => menu_table_seeder::class,
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
        Schema::dropIfExists('menus');
    }
}
