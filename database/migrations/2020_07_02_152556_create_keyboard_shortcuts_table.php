<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateKeyboardShortcutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keyboard_shortcuts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keyboard_constant', 50)->unique();
            $table->string('keyboard_shortcut', 15);
            $table->string('keyboard_shortcut_label', 100);
            $table->text('description')->nullable();
            $table->integer('sort_order');
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['status', 'keyboard_constant']);
        });

        Artisan::call('db:seed', [
            '--class' => keyboard_shortcuts_table_seeder::class,
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
        Schema::dropIfExists('keyboard_shortcuts');
    }
}
