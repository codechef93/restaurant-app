<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Models\Language as LanguageModel;

class language_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'LANGUAGE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'LANGUAGE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'EN',
                "language_code" => 'en',
                "language" => 'English',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
        
        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'DE',
                "language_code" => 'de',
                "language" => 'German',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'AR',
                "language_code" => 'ar',
                "language" => 'Arabic',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'ES',
                "language_code" => 'es',
                "language" => 'Spanish',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'FR',
                "language_code" => 'fr',
                "language" => 'French',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'IT',
                "language_code" => 'it',
                "language" => 'Italian',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'MS',
                "language_code" => 'ms',
                "language" => 'Malay',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();


        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'NO',
                "language_code" => 'no',
                "language" => 'Norwegian',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();


        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'SV',
                "language_code" => 'sv',
                "language" => 'Swedish',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'TH',
                "language_code" => 'th',
                "language" => 'Thai',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'ZH',
                "language_code" => 'zh',
                "language" => 'Chinese',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}
