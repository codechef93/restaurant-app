<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaxcodeResource extends JsonResource
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
            'label' => $this->label,
            'tax_code' => $this->tax_code,
            'total_tax_percentage' => $this->total_tax_percentage,
            'description' => $this->description,
            'tax_type' => $this->tax_type,
            'tax_type_label' => ($this->tax_type)?ucfirst(strtolower($this->tax_type)):'-',
            'tax_components' => TaxcodeTypeResource::collection($this->tax_components),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_TAXCODE'], true))?route('tax_code', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
