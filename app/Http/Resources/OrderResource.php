<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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

        $products = OrderProductResource::collection($this->products);
        $products_data = [];
        $total_items = 0;
        $total_quantity = 0;
        $products = json_decode(json_encode($products),true);
        foreach($products as $key => $product){
            $product['counter'] = $key+1;
            $products_data[] = $product;
            
            if(!empty($product['addon_products'])){
                foreach($product['addon_products'] as $addon_product){
                    $addon_product['counter'] = '';
                    $products_data[] = $addon_product;
                    $total_items += 1;
                    $total_quantity += $addon_product['quantity'];
                }
            }
            $total_items += 1;
            $total_quantity += $product['quantity'];
        }

        $order_balance_amount = ($this->total_order_amount - array_sum(array_column($this->transactions->toArray(), 'amount')));
        $order_difference_exists = ($this->total_order_amount != $order_balance_amount)?(($order_balance_amount<0)?'negative':'positive'):'';

        $customer_data_formatted = [
            'slack' => $this->customer_data->slack,
            'name' => $this->customer_name,
            'phone' => $this->customer_data->phone,
            'email' => $this->customer_data->email,
            'type' => $this->customer_data->customer_type,
        ];

        $merged_orders = $this->merged_orders;
        foreach($merged_orders as $key => $merged_order){
            $merged_orders[$key]['detail_link'] = (check_access(['A_DETAIL_ORDER'], true))?route('order_detail', ['slack' => $merged_order->slack]):'';
        }

        return [
            'slack' => $this->slack,
            'order_number' => $this->order_number,
            'additional_note' => $this->additional_note,
            'restaurant_mode' => $this->restaurant_mode,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'customer_email' => $this->customer_email,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'currency_name' => $this->currency_name,
            'currency_code' => $this->currency_code,
            'order_level_discount_code' => $this->store_level_discount_code,
            'order_level_discount_percentage' => $this->store_level_total_discount_percentage,
            'order_level_discount_amount' => $this->store_level_total_discount_amount,
            'product_level_total_discount' => $this->product_level_total_discount_amount,
            'order_level_tax_code' => $this->store_level_tax_code,
            'order_level_tax_percentage' => $this->store_level_total_tax_percentage,
            'order_level_tax_amount' => $this->store_level_total_tax_amount,
            'order_level_tax_components' => ($this->store_level_total_tax_components != '')?json_decode($this->store_level_total_tax_components):'',
            'product_level_total_tax' => $this->product_level_total_tax_amount,
            'purchase_amount_subtotal_excluding_tax' => $this->purchase_amount_subtotal_excluding_tax,
            'sale_amount_subtotal_excluding_tax' => $this->sale_amount_subtotal_excluding_tax,
            'total_discount_before_additional_discount' => $this->total_discount_before_additional_discount,
            'additional_discount_percentage' => $this->additional_discount_percentage,
            'additional_discount_amount' => $this->additional_discount_amount,
            'total_discount_amount' => $this->total_discount_amount,
            'total_after_discount' => $this->total_after_discount,
            'total_tax_amount' => $this->total_tax_amount,
            'total_order_amount' => $this->total_order_amount,
            'total_order_amount_rounded' => $this->total_order_amount_rounded,
            'order_difference' => abs(round($order_balance_amount, 2)),
            'order_difference_exists' => $order_difference_exists,
            'payment_method' => $this->payment_method,
            'customer' => new CustomerResource($this->customer_data),
            'customer_formatted' => $customer_data_formatted,
            'products' => $products_data,
            'total_items' => $total_items,
            'total_quantity' => $total_quantity,
            'store' => new StoreResource($this->storeData),
            'status' => new MasterStatusResource($this->status_data),
            'payment_status' => new MasterStatusResource($this->payment_status_data),
            'kitchen_status' => new MasterStatusResource($this->kitchen_status_data),
            'detail_link' => (check_access(['A_DETAIL_ORDER'], true))?route('order_detail', ['slack' => $this->slack]):'',
            'edit_link' => (check_access(['A_EDIT_ORDER'], true))?route('edit_order', ['slack' => $this->slack]):'',
            'transactions' => TransactionResource::collection($this->transactions),
            'order_type_data' => new MasterOrderTypeResource($this->order_type_data),
            'order_type' => $this->order_type,
            'restaurant_table_data' => new TableResource($this->restaurant_table_data),
            'table' => $this->table_number,
            'waiter_data' => new UserResource($this->waiterUser),
            'billing_type_data' => new MasterBillingTypeResource($this->billing_type_data),
            'order_origin' => $this->order_origin,
            'payment_links' => $this->generate_public_payment_links(),
            'merged_orders' => $merged_orders,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'create_at_utc' => strtotime($this->created_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}