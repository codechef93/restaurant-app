<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV51Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('contact_number', 15)->nullable()->after('customer_email');
            $table->text('address')->nullable()->after('contact_number');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('address');
        });

        Artisan::call('db:seed', [
            '--class' => v5_1_overall_seeder::class,
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
        //
    }
}
