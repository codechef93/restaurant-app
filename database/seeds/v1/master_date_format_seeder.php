<?php

use Illuminate\Database\Seeder;
use App\Models\MasterDateFormat;

class master_date_format_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_TIME_FORMAT',
                'date_format_value' => 'd-m-Y H:i',
                'date_format_label' => '01-12-2020 23:00',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_TIME_FORMAT',
                'date_format_value' => 'j-n-Y H:i',
                'date_format_label' => '1-12-2020 23:00',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_TIME_FORMAT',
                'date_format_value' => 'd-m-Y h:i A',
                'date_format_label' => '01-12-2020 01:00 PM',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_TIME_FORMAT',
                'date_format_value' => 'j-n-Y h:i A',
                'date_format_label' => '1-12-2020 01:00 PM',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_FORMAT',
                'date_format_value' => 'd-m-Y',
                'date_format_label' => '01-12-2020',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_FORMAT',
                'date_format_value' => 'j-n-Y',
                'date_format_label' => '1-12-2020',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterDateFormat::firstOrCreate(
            [
                'key' => 'DATE_FORMAT',
                'date_format_value' => 'Y-m-d',
                'date_format_label' => '2020-12-01',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();
    }
}
