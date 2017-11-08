<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'category_code' => $this->category_code,
            'label' => $this->label,
            'description' => $this->description,
            'display_on_pos_screen' => $this->display_on_pos_screen,
            'display_on_qr_menu' => $this->display_on_qr_menu,
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_CATEGORY'], true))?route('category', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser),
            'type' => $this->type,
            'category_order' => $this->category_order
        ];
    }
}
