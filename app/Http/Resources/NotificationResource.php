<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $store_data = $this->store_data;
        
        return [
            'slack' => $this->slack,
            'notification_text' => $this->notification_text,
            'user' => new UserResource($this->notificationUser),
            'store_name' => (isset($store_data) && !empty($store_data))?$store_data->name:'',
            'store_code' => (isset($store_data) && !empty($store_data))?$store_data->store_code:'',
            'status' => new MasterStatusResource($this->status_data),
            'read' => $this->read,
            'detail_link' => (check_access(['A_DETAIL_NOTIFICATION'], true))?route('notification', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser),
        ];
    }
}
