<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingMailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_mail', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->string('type', 50);
            $table->string('driver', 50);
            $table->string('host', 250);
            $table->string('port', 50);
            $table->string('encryption', 50);
            $table->string('username', 250);
            $table->string('password', 250);
            $table->string('from_email', 250);
            $table->string('from_email_name', 250);
            $table->tinyInteger('status')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_mail');
    }
}
