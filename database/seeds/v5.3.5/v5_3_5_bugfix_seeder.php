<?php

use Illuminate\Database\Seeder;
use App\Models\Country as CountryModel;

class v5_3_5_bugfix_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currency_data = CountryModel::select('id')->where([
            ['code', '=', 'CD'],
        ])  
        ->active()
        ->first();

        if(!empty($currency_data)){
            CountryModel::where([
                ['id', '=', $currency_data->id]
            ])
            ->update([
                'currency_name' => 'Congolese franc',
                'currency_code' => 'CDF',
                'currency_symbol' => 'FC'
            ]);
        }
    }
}
