<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use App\Http\Resources\MasterStatusResource;

class StoreResource extends JsonResource
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
            'store_code' => $this->store_code,
            'name' => $this->name,
            'tax_number' => $this->tax_number,
            'final_letter' => $this->final_letter,
            'delivery_tax' => $this->delivery_tax,
            'store_logo' => $this->store_logo,
            'store_logo_base64' => $this->store_logo_base64,
            'address' => $this->address,
            'pincode' => $this->pincode,
            'primary_contact' => $this->primary_contact,
            'secondary_contact' => $this->secondary_contact,
            'primary_email' => $this->primary_email,
            'secondary_email' => $this->secondary_email,
            'country' => new CountryResource($this->country),
            'tax_code' => new TaxcodeResource($this->tax_code),
            'discount_code' => new DiscountcodeResource($this->discount_code),
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_STORE'], true))?route('store', ['slack' => $this->slack]):'',
            'invoice_type' => $this->invoice_print_type,
            'currency_code' => $this->currency_code,
            'currency_name' => $this->currency_name,
            'restaurant_mode' => $this->restaurant_mode,
            'restaurant_waiter_role' => new RoleResource($this->waiter_role_data),
            'restaurant_chef_role' => new RoleResource($this->chef_role_data),
            'restaurant_billing_type' => new MasterBillingTypeResource($this->restaurant_billing_type),
            'enable_customer_popup' => $this->enable_customer_popup,
            'enable_variants_popup' => $this->enable_variants_popup,
            'digital_menu_enabled' => $this->digital_menu_enabled,
            'enable_digital_menu_otp_verification' => $this->enable_digital_menu_otp_verification,
            'digital_menu_send_order_to_kitchen' => $this->digital_menu_send_order_to_kitchen,
            'menu_language' => new LanguageResource($this->menu_language),
            'menu_open_time' => $this->parseTime($this->menu_open_time),
            'menu_close_time' => $this->parseTime($this->menu_close_time),
            'printnode_enabled' => $this->printnode_enabled,
            'printnode_api_key' => $this->printnode_api_key,
            'invoice_printer' => new PrinterResource($this->invoice_printer),
            'kot_printer' => new PrinterResource($this->kot_printer),
            'other_printer' => new PrinterResource($this->other_printer),
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
