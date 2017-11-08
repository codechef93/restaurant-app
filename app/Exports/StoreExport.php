<?php

namespace App\Exports;

use App\Models\Store;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\StoreResource;

use Carbon\Carbon;

class StoreExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function collection()
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $status = $this->data['status'];

        $query = Category::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('stores.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('stores.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('stores.status', $status);
        }

        $stores = $query->get();

        return $stores;
    }

    public function headings(): array
    {
        return [
            'STORE CODE',
            'NAME',
            'TAX NUMBER',
            'TAX CODE',
            'TAX PERCENTAGE',
            'DISCOUNT CODE',
            'DISCOUNT PERCENTAGE',
            'ADDRESS',
            'PINCODE',
            'PRIMARY CONTACT',
            'SECONDARY CONTACT',
            'PRIMARY EMAIL',
            'SECONDARY EMAIL',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($store): array
    {
        $store = collect(new StoreResource($store));
        return [
            $store['store_code'],
            $store['name'],
            $store['tax_number'],
            $store['tax_code']['tax_code'],
            $store['tax_code']['total_tax_percentage'],
            $store['discount_code']['discount_code'],
            $store['discount_code']['discount_percentage'],
            $store['address'],
            $store['pincode'], 
            $store['primary_contact'],
            $store['secondary_contact'],
            $store['primary_email'],
            $store['secondary_email'],              
            $store['status']['label'],
            $store['created_at_label'],
            $store['created_by']['fullname'],
            $store['updated_at_label'],
            $store['updated_by']['fullname']
        ];
    }
}
