<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductIngredientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ingredient_product = collect();
        $low_stock = 0;
        if(!empty($this->ingredient_product)){
            $ingredient_product = new ProductResource($this->ingredient_product);
            $low_stock = ($ingredient_product->quantity<=$ingredient_product->alert_quantity)?1:0;
        }

        return [
            'slack' => $this->slack,
            'ingredient_product' => $ingredient_product,
            'quantity' => $this->quantity,
            'low_stock' => $low_stock,
            'measurement_unit' => new MeasurementUnitResource($this->measurement_unit),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
