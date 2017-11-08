<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingAppResource extends JsonResource
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
            'company_name' => $this->company_name,
            'app_title' => $this->app_title,
            'timezone' => $this->timezone,
            'app_date_time_format' => $this->app_date_time_format,
            'app_date_format' => $this->app_date_format,
            'company_logo' => $this->company_logo,
            'company_logo_path' => ($this->company_logo == '')?config('constants.upload.company.company_logo_default'):config('constants.upload.company.view_path').$this->company_logo,
            'invoice_print_logo' => $this->invoice_print_logo,
            'invoice_print_logo_path' => ($this->invoice_print_logo == '')?config('constants.upload.company.invoice_logo_default'):config('constants.upload.company.view_path').$this->invoice_print_logo,
            'navbar_logo' => $this->navbar_logo,
            'navbar_logo_path' => ($this->navbar_logo == '')?config('constants.upload.company.navbar_logo_default'):config('constants.upload.company.view_path').$this->navbar_logo,
            'favicon' => $this->favicon,
            'favicon_path' => ($this->favicon == '')?config('constants.upload.company.favicon_default'):config('constants.upload.company.view_path').$this->favicon,
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
