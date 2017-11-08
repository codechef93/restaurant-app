<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $request->request->add(['blocking_recurring_data_in_transaction' => true]);

        return [
            'slack' => $this->slack,
            'return_number' => $this->return_number,
            'return_date' => $this->parseDateOnly($this->return_date),
            'return_date_raw' => $this->return_date,
            'bill_to' => $this->bill_to,
            'bill_to_code' => $this->bill_to_code,
            'bill_to_name' => $this->bill_to_name,
            'bill_to_email' => $this->bill_to_email,
            'bill_to_contact' => $this->bill_to_contact,
            'bill_to_address' => $this->bill_to_address,
            'supplier' => ($this->bill_to == "SUPPLIER")? new SupplierResource($this->supplier):'',
            'customer' => ($this->bill_to == "CUSTOMER")?new CustomerResource($this->customer):'',
            'currency_name' => $this->currency_name,
            'currency_code' => $this->currency_code,
            'subtotal_excluding_tax' => $this->subtotal_excluding_tax,
            'total_discount_amount' => $this->total_discount_amount,
            'total_after_discount' => $this->total_after_discount,
            'total_tax_amount' => $this->total_tax_amount,
            'shipping_charge' => $this->shipping_charge,
            'packing_charge' => $this->packing_charge,
            'total_order_amount' => $this->total_order_amount,
            'tax_option_data' => new MasterTaxOptionResource($this->tax_option_data),
            'update_stock' => $this->update_stock,
            'notes' => $this->notes,
            'products' => InvoiceProductResource::collection($this->products),
            'store' => new StoreResource($this->storeData),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_STOCK_RETURN'], true))?route('stock_return_detail', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
