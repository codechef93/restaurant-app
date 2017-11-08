<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MasterTaxOptionResource extends JsonResource
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
            'tax_option_constant' => $this->tax_option_constant,
            'label' => $this->label,
            'component_count' => $this->component_count,
            'component_1' => $this->component_1,
            'component_2' => $this->component_2,
            'component_3' => $this->component_3,
            'component_array' => $this->build_tax_component_array($this->component_1, $this->component_2, $this->component_3),
            'description' => $this->description,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
        ];
    }

    private function build_tax_component_array($component_1, $component_2, $component_3){
        $tax_component_array = [];
        if($component_1 != ''){
            $tax_component_array[] = $component_1;
        }
        if($component_2 != ''){
            $tax_component_array[] = $component_2;
        }
        if($component_3 != ''){
            $tax_component_array[] = $component_3;
        }
        return $tax_component_array;
    }
}
