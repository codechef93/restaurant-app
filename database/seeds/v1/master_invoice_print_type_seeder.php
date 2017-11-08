<?php

use Illuminate\Database\Seeder;
use App\Models\MasterInvoicePrintType;

class master_invoice_print_type_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterInvoicePrintType::firstOrCreate(
            [
                'print_type_value' => 'A4',
                'print_type_label' => 'A4 Sheet',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        MasterInvoicePrintType::firstOrCreate(
            [
                'print_type_value' => 'SMALL',
                'print_type_label' => 'Small Sheet',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();
    }
}
