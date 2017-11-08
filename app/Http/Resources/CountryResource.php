<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CountryResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'dial_code' => $this->dial_code,
            'currency_name' => $this->currency_name,
            'currency_code' => $this->currency_code,
            'currency_code' => $this->currency_symbol,
        ];
    }
}

