<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
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
            'stock_transfer_reference' => $this->stock_transfer_reference,
            'from_store_code' => $this->from_store_code,
            'from_store_name' => $this->from_store_name,
            'from_store_data' => new StoreResource($this->fromStoreData),
            'to_store_code' => $this->to_store_code,
            'to_store_name' => $this->to_store_name,
            'to_store_data' => new StoreResource($this->toStoreData),
            'notes' => $this->notes,
            'status' => new MasterStatusResource($this->status_data),
            'products' => StockTransferProductResource::collection($this->products),
            'detail_link' => (check_access(['A_DETAIL_STOCK_TRANSFER'], true))?route('stock_transfer', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
