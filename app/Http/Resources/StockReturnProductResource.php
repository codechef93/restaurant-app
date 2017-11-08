<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockReturnProductResource extends JsonResource
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
            'product_slack' => $this->product_slack,
            'product_code' => $this->product_code,
            'name' => $this->name,
            'quantity' => $this->quantity,
            'amount_excluding_tax' => $this->amount_excluding_tax,
            'subtotal_amount_excluding_tax' => $this->subtotal_amount_excluding_tax,
            'discount_percentage' => $this->discount_percentage,
            'tax_percentage' => $this->tax_percentage,
            'discount_amount' => $this->discount_amount,
            'total_after_discount' => $this->total_after_discount,
            'tax_amount' => $this->tax_amount,
            'tax_components' => $this->tax_components,
            'tax_component_array' => $this->build_tax_array($this->tax_components),
            'total_amount' => $this->total_amount,
            'stock_update' => $this->stock_update,
            'status' => new MasterStatusResource($this->status_data),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }

    private function build_tax_array($tax_components){
        $tax_component_array = [];
        $tax_components = json_decode($tax_components, true);
        if($tax_components != null && count($tax_components)>0){
            foreach($tax_components as $key=>$tax_component){
                $tax_component_array[$tax_component['name']] = $tax_component['tax_amount']." (".$tax_component['tax_percentage']."%)";
            }
        }
        return $tax_component_array;
    }
}