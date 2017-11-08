<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'The :attribute must be accepted.',
    'active_url'           => 'The :attribute is not a valid URL.',
    'after'                => 'The :attribute must be a date after :date.',
    'after_or_equal'       => 'The :attribute must be a date after or equal to :date.',
    'alpha'                => 'The :attribute may only contain letters.',
    'alpha_dash'           => 'The :attribute may only contain letters, numbers, and dashes.',
    'alpha_num'            => 'The :attribute may only contain letters and numbers.',
    'array'                => 'The :attribute must be an array.',
    'before'               => 'The :attribute must be a date before :date.',
    'before_or_equal'      => 'The :attribute must be a date before or equal to :date.',
    'between'              => [
        'numeric' => 'The :attribute must be between :min and :max.',
        'file'    => 'The :attribute must be between :min and :max kilobytes.',
        'string'  => 'The :attribute must be between :min and :max characters.',
        'array'   => 'The :attribute must have between :min and :max items.',
    ],
    'boolean'              => 'The :attribute field must be true or false.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => 'The :attribute is not a valid date.',
    'date_time_format'          => 'The :attribute does not match the format :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => 'The :attribute must be a valid email address.',
    'exists'               => 'The selected :attribute is invalid.',
    'file'                 => 'The :attribute must be a file.',
    'filled'               => 'The :attribute field must have a value.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'The selected :attribute is invalid.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'ipv4'                 => 'The :attribute must be a valid IPv4 address.',
    'ipv6'                 => 'The :attribute must be a valid IPv6 address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file'    => 'The :attribute may not be greater than :max kilobytes.',
        'string'  => 'The :attribute may not be greater than :max characters.',
        'array'   => 'The :attribute may not have more than :max items.',
    ],
    'mimes'                => 'The :attribute must be a file of type: :values.',
    'mimetypes'            => 'The :attribute must be a file of type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => 'The :attribute must be :size.',
        'file'    => 'The :attribute must be :size kilobytes.',
        'string'  => 'The :attribute must be :size characters.',
        'array'   => 'The :attribute must contain :size items.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'uploaded'             => 'The :attribute failed to upload.',
    'url'                  => 'The :attribute format is invalid.',
    'alpha_spaces'         => 'The :attribute may only contain letters, numbers, spaces, - or _.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email' => 'Email',
        'password' => 'Password',

        'fullname' => 'Fullname',
        'phone' => 'Contact No',
        'role' => 'Role',
        'status' => 'Status',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'New Password Confirmation',

        'customer_number' => 'Customer Contact No',
        'customer_email ' => 'Customer Email',

        'description' => 'Description',

        'name' => 'Name',
        'product_code' => 'Product Code',
        'supplier' => 'Supplier',
        'category' => 'Category',
        'tax_code' => 'Tax Code',
        'purchase_price' => 'Purchase Price',
        'sale_price' => 'Sale Price',
        'quantity' => 'Quantity',
        'no_of_barcodes' => 'No of Barcodes per Product',

        'category_name' => 'Category Name',

        'supplier_name' => 'Supplier Name',
        'address' => 'Address',
        'pincode' => 'Pincode',

        'tax_code_name' => 'Tax Code Name',

        'discount_name' => 'Discount Name',
        'discount_code' => 'Discount Code',
        'discount_percentage' => 'Discount Percentage',

        'store_code' => 'Store Code',
        'tax_number' => 'Tax No or GST No.',
        'primary_contact' => 'Primary Contact No',
        'secondary_contact' => 'Secondary Contact No',
        'primary_email' => 'Primary Email',
        'secondary_email' => 'Secondary Email',
        'print_type' => 'Invoice Print Type',
        'currency_code' => 'Currency Code',
        
        'payment_method' => 'Payment Method',
        'business_account' =>  'Business Account',

        'driver' => 'Driver',
        'host' => 'Host',
        'port' => 'Port',
        'username' => 'Username',
        'encryption' => 'Encryption',
        'from_email' => 'From Email',
        'from_email_name' => 'From Email Name',

        'country' => 'Country',
        'company_name' => 'Company Name',
        'app_date_time_format' => 'Date Time format',
        'app_date_format' => 'Date Format',

        'po_number' => 'PO Number',
        'po_reference' => 'PO Reference #',
        'currency' => 'Currency',
        'shipping_charge' => 'Shipping Charges',
        'packing_charge' => 'Packing Charges',

        'bill_to' => 'Bill To',
        'bill_to_slack' => 'Customer or Supplier',
        'invoice_date' => 'Invoice Date',
        'invoice_due_date' => 'Invoice Due Date',
        'amount' => 'Amount',

        'account_name' => 'Account Name',
        'account_type' => 'Account Type',
        'pos_default' => 'POS Default Account',
        'initial_balance' => 'Initial Balance',

        'transaction_date' => 'Transaction Date',
        'account' => 'Account',
        'transaction_type' => 'Transaction Type',

        'table_name' => 'Table Name',
        'no_of_occupants' => 'No of Occupants',

        'restaurant_order_type' => 'Order Type',
        'restaurant_table' => 'Table',
        
        'product_images.*'=> 'Product Image',

        'parent_variant_option' => 'Variant Option for Parent Product',

        'variants.*.variant_option_slack'=> 'Variant Option',

        'printer_id' => 'Printer ID',
        'printer_name' => 'Printer Name'
    ],

];
