<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'event_type' => $this->event_type,
            'event_code' => $this->event_code,
            'start_date_raw' => $this->parseDate($this->start_date),
            'end_date_raw' => $this->parseDate($this->end_date),
            'start_date' => date(config('app.sql_date_format'), strtotime($this->start_date)),
            'end_date' => date(config('app.sql_date_format'), strtotime($this->end_date)),
            'start_time' => date('h:i A', strtotime($this->start_date)),
            'end_time' => date('h:i A', strtotime($this->end_date)),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'description' => $this->description,
            'no_of_persons' => $this->no_of_persons,
            'detail_link' => (check_access(['A_DETAIL_BOOKING'], true))?route('booking', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ]; 
    }
}
