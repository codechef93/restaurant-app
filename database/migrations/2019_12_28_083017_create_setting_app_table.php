<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Artisan;

class CreateSettingAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_app', function (Blueprint $table) {
            $table->string('company_name', 250);
            $table->string('app_date_time_format', 50);
            $table->string('app_date_format', 50);
            $table->text('company_logo')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['company_name', 'app_date_format']);
        });

        Artisan::call('db:seed', [
            '--class' => app_setting_seeder::class,
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
        Schema::dropIfExists('setting_app');
    }
}
