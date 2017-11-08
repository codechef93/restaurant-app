<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'slack' => $this->slack,
            'parent_order_product' => ($this->parent_order_product_id > 0)?false:true,
            'product_slack' => $this->product_slack,
            'product_code' => $this->product_code,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'purchase_amount_excluding_tax' => $this->purchase_amount_excluding_tax,
            'price' => $this->sale_amount_excluding_tax,
            'discount_code' => $this->discount_code,
            'discount_percentage' => $this->discount_percentage,
            'tax_code' => $this->tax_code,
            'tax_percentage' => $this->tax_percentage,
            'tax_components' => json_decode($this->tax_components),
            'sub_total_purchase_price_excluding_tax' => $this->sub_total_purchase_price_excluding_tax,
            'sub_total' => $this->sub_total_sale_price_excluding_tax,
            'discount_amount' => $this->discount_amount,
            'total_after_discount' => $this->total_after_discount,
            'tax_amount' => $this->tax_amount,
            'total_price' => $this->total_amount,
            'is_ready_to_serve' => $this->is_ready_to_serve,
            'addon_products' => OrderProductResource::collection($this->addon_products_data),
            'product' => new ProductResource($this->product_data),
            'status' => new MasterStatusResource($this->status_data),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}