<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateV54Overall extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_registers', function (Blueprint $table) {
            $table->integer('parent_register_id')->nullable()->after('billing_counter_id');
            $table->dateTime('joining_date')->nullable()->after('closing_date');
            $table->dateTime('exit_date')->nullable()->after('joining_date');
        });

        Schema::create('printers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('slack', 30)->unique();
            $table->integer('store_id');
            $table->string('printer_code', 30);
            $table->string('printer_id', 50)->comment('PrintNode printer ID');
            $table->string('printer_name', 250);
            $table->tinyInteger('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->index(['status', 'store_id', 'printer_code']);
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->tinyInteger('printnode_enabled')->default(0)->after('menu_language_id');
            $table->string('printnode_api_key', 100)->nullable()->after('printnode_enabled');
            $table->integer('pos_printer_id')->nullable()->after('printnode_api_key');
            $table->integer('kot_printer_id')->nullable()->after('pos_printer_id');
            $table->integer('other_printer_id')->nullable()->after('kot_printer_id');
            $table->timeTz('menu_open_time')->nullable()->after('menu_language_id');
            $table->timeTz('menu_close_time')->nullable()->after('menu_open_time');
            $table->tinyInteger('digital_menu_enabled')->default(1)->after('enable_variants_popup');
            $table->index(['pos_printer_id', 'kot_printer_id', 'other_printer_id'], 'printer_index');
        });

        Schema::table('sms_templates', function (Blueprint $table) {
            $table->string('flow_id', 100)->nullable()->after('description')->comment('Flow ID for MSG91');;
        });
        
        Artisan::call('db:seed', [
            '--class' => v5_4_overall_seeder::class,
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
