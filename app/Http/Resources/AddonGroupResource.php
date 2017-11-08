<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AddonGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $products = [];
        if(!isset($request->skip_products) && $request->skip_products == false){
            $products = AddonGroupProductResource::collection($this->addon_products);
        }

        return [
            'slack' => $this->slack,
            'label' => $this->label,
            'addon_group_code' => $this->addon_group_code,
            'multiple_selection' => $this->multiple_selection,
            'products' => $products,
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_ADDON_GROUP'], true))?route('addon_group', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
