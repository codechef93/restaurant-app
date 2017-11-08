<?php 

$server_check_link = '/server_check.php';

return [

    'upload' => [
        'profile' => [
            'default' => '/images/profile_default.jpg',
            'dir' => 'profile/',
            'view_path' => '/storage/profile/',
            'upload_path' => 'storage/profile/'
        ],
        'company' => [
            'app_title' => 'Appsthing POS',
            'company_logo_default' => '/images/logo_word_mark.png',
            'invoice_logo_default' => '/images/logo_invoice_print.png',
            'navbar_logo_default' => '/images/logo_small.png',
            'favicon_default' => '/images/favicon_32_32.png',
            'dir' => 'company/',
            'view_path' => '/storage/company/', // ********  '/storage/company/',
            'upload_path' => 'storage/company/'
        ],
        'imports' => [
            'default' => '',
            'dir' => 'imports/',
            'view_path' => '/storage/imports/',
            'upload_path' => 'storage/imports/',
            'user_format' => 'excel_formats/import/user_format.xls',
            'store_format' => 'excel_formats/import/store_format.xls',
            'supplier_format' => 'excel_formats/import/supplier_format.xls',
            'category_format' => 'excel_formats/import/category_format.xls',
            'product_format' => 'excel_formats/import/product_format.xls',
            'ingredient_format' => 'excel_formats/import/ingredient_format.xls',
            'addon_product_format' => 'excel_formats/import/addon_product_format.xls'
        ],
        'updates' => [
            'default' => '',
            'dir' => 'updates/',
            'view_path' => '/storage/updates/',
            'upload_path' => 'storage/updates/',
            'user_format' => 'excel_formats/update/user_format.xls',
            'store_format' => 'excel_formats/update/store_format.xls',
            'supplier_format' => 'excel_formats/update/supplier_format.xls',
            'category_format' => 'excel_formats/update/category_format.xls',
            'product_format' => 'excel_formats/update/product_format.xls',
            'ingredient_format' => 'excel_formats/update/ingredient_format.xls',
            'addon_product_format' => 'excel_formats/update/addon_product_format.xls',
            'product_variant_format' => 'excel_formats/update/product_variant_format.xls'
        ],
        'barcode' => [
            'default' => '',
            'dir' => 'barcode/',
            'view_path' => '/storage/barcode/',
            'upload_path' => 'storage/barcode/'
        ],
        'reports' => [
            'default' => '',
            'dir' => 'reports/',
            'view_path' => '/storage/reports/',
            'upload_path' => 'storage/reports/'
        ],
        'product' => [
            'dir' => 'product/',
            'view_path' => '/storage/product/',
            'upload_path' => 'storage/product/'
        ],
        'order' => [
            'dir' => 'order/',
            'view_path' => '/storage/order/',
            'upload_path' => 'storage/order/'
        ],
        'register' => [
            'default' => '',
            'dir' => 'register/',
            'view_path' => '/storage/register/',
            'upload_path' => 'storage/register/'
        ],

        'invoice' => [
            'dir' => 'invoice/',
            'view_path' => '/storage/invoice/',
            'upload_path' => 'storage/invoice/'
        ],
        'purchase_order' => [
            'dir' => 'purchase_order/',
            'view_path' => '/storage/purchase_order/',
            'upload_path' => 'storage/purchase_order/'
        ],
        'quotation' => [
            'dir' => 'quotation/',
            'view_path' => '/storage/quotation/',
            'upload_path' => 'storage/quotation/'
        ],
        'store' => [
            'dir' => 'store/',
            'view_path' => '/storage/store/',
            'upload_path' => 'storage/store/'
        ],
    ],
    
    'unique_code_start' => [
        'user'          => 100,
        'role'          => 100,
        'order'         => 100,
        'category'      => 100,
        'supplier'      => 100,
        'invoice'       => 100,
        'quotation'     => 100,
        'account'       => 100,
        'transaction'   => 100,
        'stock_transfer'=> 100,
        'stock_return'  => 100,
        'booking'       => 100,
        'addon_group'   => 100,
        'variant_option'=> 100,
        'printer'       => 100
    ],

    'demo_notification' => 'Demo Mode',

    'activation_notification' => "<i class='fas fa-exclamation-circle'></i> Activate Product, <a href='".$server_check_link."' class='text-dark text-bold text-decoration-none'>Click Here</a>"
];