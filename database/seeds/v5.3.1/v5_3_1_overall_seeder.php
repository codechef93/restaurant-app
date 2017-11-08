<?php

use Illuminate\Database\Seeder;

use App\Models\Product as ProductModel;
use Illuminate\Support\Facades\DB;

class v5_3_1_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = ProductModel::withoutGlobalScopes()->select('products.slack', 'products.sale_amount_excluding_tax', 'tax_codes.total_tax_percentage', 'tax_codes.tax_type')
        ->join('tax_codes', 'tax_codes.id', '=', 'products.tax_code_id')
        ->get();

        if(!empty($products)){

            DB::beginTransaction();

            foreach($products as $product){
                if($product->tax_type == 'EXCLUSIVE'){
                    $tax_amount = calculate_tax($product->sale_amount_excluding_tax, $product->total_tax_percentage);
                    
                    $sale_amount_including_tax = $product->sale_amount_excluding_tax+$tax_amount;

                    $product_price_array = [
                        'sale_amount_including_tax' => $sale_amount_including_tax
                    ];

                    $action_response = ProductModel::withoutGlobalScopes()->where('slack', $product->slack)
                    ->update($product_price_array);
                }
            }

            DB::commit();
        }
    }
}
