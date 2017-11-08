<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
   
    public function toArray($request)
    {
        $product = (new ProductResource($this->product_variant))->block_recurring_variants(true);
        $variant_option = $this->variant_option;

        return [
            'slack' => $this->slack,
            'variant_code' => $this->variant_code,
            'product' => $product,
            'variant_option' => $variant_option,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
