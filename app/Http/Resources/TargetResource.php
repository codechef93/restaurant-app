<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TargetResource extends JsonResource
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
            'month' => $this->month,
            'month_label' => $this->parseTargetMonth($this->month),
            'income' => $this->income,
            'expense' => $this->expense,
            'sales' => $this->sales,
            'net_profit' => $this->net_profit,
            'detail_link' => (check_access(['A_DETAIL_TARGET'], true))?route('target', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
