<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferProductResource extends JsonResource
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
            'product_name' => $this->product_name,
            'quantity' => $this->quantity,
            
            'inward_type' => $this->inward_type,
            'accepted_quantity' => $this->accepted_quantity,
            'destination_product_slack' => $this->destination_product_slack,
            'destination_product_code' => $this->destination_product_code,
            'destination_product_name' => $this->destination_product_name,

            'status' => new MasterStatusResource($this->status_data),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}