<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $blocking_recurring_data_in_transaction = (isset($request->blocking_recurring_data_in_transaction))?$request->blocking_recurring_data_in_transaction:false;

        return [
            'slack' => $this->slack,
            'transaction_code' => $this->transaction_code,
            'payment_method' => $this->payment_method,
            'currency_code' => $this->currency_code,
            'amount' => $this->amount,
            'pg_transaction_id' => $this->pg_transaction_id,
            'pg_transaction_status' => $this->pg_transaction_status,
            'payment_method_data' => new PaymentMethodResource($this->payment_method_data),
            'account' => new AccountResource($this->account),
            'transaction_type_data' => new MasterTransactionTypeResource($this->transaction_type_data),
            'bill_to' => $this->bill_to,
            'bill_to_name' => $this->bill_to_name,
            'bill_to_contact' => $this->bill_to_contact,
            'bill_to_address' => $this->bill_to_address,
            'order' => ($this->bill_to == "POS_ORDER" && $blocking_recurring_data_in_transaction == false)?new OrderResource($this->order):'',
            'invoice' => ($this->bill_to == "INVOICE" && $blocking_recurring_data_in_transaction == false)?new InvoiceResource($this->invoice):'',
            'supplier' => ($this->bill_to == "SUPPLIER" && $blocking_recurring_data_in_transaction == false)? new SupplierResource($this->supplier):'',
            'customer' => ($this->bill_to == "CUSTOMER" && $blocking_recurring_data_in_transaction == false)? new CustomerResource($this->customer):'',
            'notes' => $this->notes,
            'transaction_date' => $this->parseDateOnly($this->transaction_date),
            'detail_link' => (check_access(['A_DETAIL_TRANSACTION'], true))?route('transaction', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
