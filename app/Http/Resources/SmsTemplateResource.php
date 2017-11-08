<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsTemplateResource extends JsonResource
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
            'template_key' => $this->template_key,
            'flow_id' => $this->flow_id,
            'message' => $this->message,
            'available_variables' => $this->available_variables,
            'description' => $this->description,
            'detail_link' => (check_access(['A_DETAIL_SMS_TEMPLATE'], true))?route('sms_template', ['slack' => $this->slack]):'',
            'status' => new MasterStatusResource($this->status_data),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
