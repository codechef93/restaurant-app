<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

use App\Models\Store as StoreModel;
use App\Models\Account as AccountModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use App\Models\Table as TableModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\Target as TargetModel;
use App\Models\User as UserModel;
use App\Models\Notification as NotificationModel;
use App\Models\BillingCounter as BillingCounterModel;
use App\Models\ProductImages as ProductImagesModel;
use App\Models\MeasurementUnit as MeasurementUnitModel;
use App\Models\AddonGroup as AddonGroupModel;
use App\Models\AddonGroupProduct as AddonGroupProductModel;
use App\Models\ProductAddonGroup as ProductAddonGroupModel;
use App\Models\VariantOption as VariantOptionModel;
use App\Models\ProductVariant as ProductVariantModel;

use App\Http\Controllers\API\Product as ProductAPI;

class sample_values_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $base_controller = new Controller;

        $store_1 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE1")),
            "name" => "Appsthing Store 1",
            "tax_number" => "100000000000",
            "address" => $faker->address,
            "country_id" => 230,
            "pincode" => "100111",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "SMALL",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "restaurant_mode" =>1,
            "restaurant_chef_role_id" => 6,
            "restaurant_waiter_role_id" => 5,
            "restaurant_billing_type_id" => 2,
            "enable_digital_menu_otp_verification" => 0,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $store_2 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE2")),
            "name" => "Appsthing Store 2",
            "tax_number" => "100000000001",
            "address" => $faker->address,
            "country_id" => 98,
            "pincode" => "560038",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "A4",
            "currency_code" => "INR",
            "currency_name" => "Indian rupee",
            "restaurant_mode" => 0,
            "restaurant_waiter_role_id" => '',
            "restaurant_billing_type_id" => '',
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $store_3 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE3")),
            "name" => "Appsthing Store 3",
            "tax_number" => "100000000001",
            "address" => $faker->address,
            "country_id" => 230,
            "pincode" => "100222",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "A4",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "restaurant_mode" =>1,
            "restaurant_chef_role_id" => 6,
            "restaurant_waiter_role_id" => 5,
            "restaurant_billing_type_id" => 1,
            "enable_digital_menu_otp_verification" => 0,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $store_4 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE4")),
            "name" => "Appsthing Store 4",
            "tax_number" => "100000000001",
            "address" => $faker->address,
            "country_id" => 230,
            "pincode" => "100222",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "A4",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "restaurant_mode" => 1,
            "restaurant_chef_role_id" => 6,
            "restaurant_waiter_role_id" => 5,
            "restaurant_billing_type_id" => 1,
            "enable_digital_menu_otp_verification" => 0,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $store_5 = DB::table("stores")->insertGetId([
            "slack" => $base_controller->generate_slack("stores"),
            "store_code" => strtoupper(trim("STORE5")),
            "name" => "Appsthing Store 5",
            "tax_number" => "100000000001",
            "address" => $faker->address,
            "country_id" => 230,
            "pincode" => "100222",
            "primary_contact" => $faker->e164PhoneNumber,
            "secondary_contact" => $faker->e164PhoneNumber,
            "primary_email" => $faker->unique()->email,
            "secondary_email" => $faker->unique()->email,
            "invoice_type" => "A4",
            "currency_code" => "USD",
            "currency_name" => "United States dollar",
            "restaurant_mode" =>0,
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $manager_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '100',
            'label' => 'Manager', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $accounts_manager_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '101',
            'label' => 'Accounts Manager', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $cashier_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '102',
            'label' => 'Cashier', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $waiter_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '103',
            'label' => 'Waiter', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $chef_role_id = DB::table("roles")->insertGetId([
            'slack' => $base_controller->generate_slack("roles"),
            'role_code' => '104',
            'label' => 'Chef', 
            'status' => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::insert("INSERT INTO `role_menus` (`id`, `role_id`, `menu_id`, `created_by`, `created_at`, `updated_at`) VALUES
        (NULL, 3, 1, 1, NOW(), NOW()),
        (NULL, 3, 2, 1, NOW(), NOW()),
        (NULL, 3, 7, 1, NOW(), NOW()),
        (NULL, 3, 8, 1, NOW(), NOW()),
        (NULL, 3, 9, 1, NOW(), NOW()),
        (NULL, 3, 10, 1, NOW(), NOW()),
        (NULL, 3, 20, 1, NOW(), NOW()),
        (NULL, 3, 34, 1, NOW(), NOW()),
        (NULL, 3, 35, 1, NOW(), NOW()),
        (NULL, 3, 36, 1, NOW(), NOW()),
        (NULL, 3, 37, 1, NOW(), NOW()),
        (NULL, 3, 57, 1, NOW(), NOW()),
        (NULL, 3, 58, 1, NOW(), NOW()),
        (NULL, 3, 59, 1, NOW(), NOW()),
        (NULL, 3, 70, 1, NOW(), NOW()),
        (NULL, 3, 71, 1, NOW(), NOW()),
        (NULL, 3, 72, 1, NOW(), NOW()),
        (NULL, 3, 73, 1, NOW(), NOW()),
        (NULL, 3, 76, 1, NOW(), NOW()),
        (NULL, 3, 77, 1, NOW(), NOW()),
        (NULL, 3, 78, 1, NOW(), NOW()),
        (NULL, 3, 79, 1, NOW(), NOW()),
        (NULL, 3, 80, 1, NOW(), NOW()),
        (NULL, 3, 81, 1, NOW(), NOW()),
        (NULL, 3, 82, 1, NOW(), NOW()),
        (NULL, 3, 83, 1, NOW(), NOW()),
        (NULL, 3, 84, 1, NOW(), NOW()),
        (NULL, 3, 85, 1, NOW(), NOW()),
        (NULL, 3, 86, 1, NOW(), NOW()),
        (NULL, 3, 87, 1, NOW(), NOW()),
        (NULL, 3, 88, 1, NOW(), NOW()),
        (NULL, 3, 89, 1, NOW(), NOW()),
        (NULL, 3, 90, 1, NOW(), NOW()),
        (NULL, 3, 91, 1, NOW(), NOW()),
        (NULL, 3, 92, 1, NOW(), NOW()),
        (NULL, 3, 93, 1, NOW(), NOW()),
        (NULL, 3, 94, 1, NOW(), NOW()),
        (NULL, 3, 95, 1, NOW(), NOW()),
        (NULL, 3, 96, 1, NOW(), NOW()),
        (NULL, 3, 97, 1, NOW(), NOW()),
        (NULL, 3, 98, 1, NOW(), NOW()),
        (NULL, 3, 99, 1, NOW(), NOW()),
        (NULL, 3, 107, 1, NOW(), NOW()),
        (NULL, 3, 108, 1, NOW(), NOW()),
        (NULL, 3, 109, 1, NOW(), NOW()),
        (NULL, 3, 110, 1, NOW(), NOW()),
        (NULL, 3, 111, 1, NOW(), NOW()),
        (NULL, 3, 118, 1, NOW(), NOW()),
        (NULL, 3, 119, 1, NOW(), NOW()),
        (NULL, 3, 120, 1, NOW(), NOW()),
        (NULL, 3, 121, 1, NOW(), NOW()),
        (NULL, 3, 122, 1, NOW(), NOW()),
        (NULL, 3, 123, 1, NOW(), NOW()),
        (NULL, 3, 124, 1, NOW(), NOW()),
        (NULL, 3, 137, 1, NOW(), NOW()),
        (NULL, 3, 150, 1, NOW(), NOW()),
        (NULL, 3, 151, 1, NOW(), NOW()),
        (NULL, 3, 152, 1, NOW(), NOW()),
        (NULL, 3, 153, 1, NOW(), NOW()),
        (NULL, 3, 154, 1, NOW(), NOW()),
        (NULL, 3, 155, 1, NOW(), NOW()),
        (NULL, 3, 156, 1, NOW(), NOW()),
        (NULL, 3, 167, 1, NOW(), NOW()),
        (NULL, 3, 168, 1, NOW(), NOW()),
        (NULL, 3, 169, 1, NOW(), NOW()),
        (NULL, 3, 170, 1, NOW(), NOW()),
        (NULL, 3, 171, 1, NOW(), NOW()),
        (NULL, 3, 172, 1, NOW(), NOW()),
        (NULL, 3, 173, 1, NOW(), NOW()),
        (NULL, 3, 174, 1, NOW(), NOW()),
        (NULL, 3, 175, 1, NOW(), NOW()),
        (NULL, 3, 187, 1, NOW(), NOW()),
        (NULL, 3, 188, 1, NOW(), NOW()),
        (NULL, 3, 189, 1, NOW(), NOW()),
        (NULL, 5, 100, 1, NOW(), NOW()),
        (NULL, 5, 178, 1, NOW(), NOW()),
        (NULL, 5, 184, 1, NOW(), NOW()),
        (NULL, 6, 100, 1, NOW(), NOW()),
        (NULL, 6, 101, 1, NOW(), NOW()),
        (NULL, 6, 106, 1, NOW(), NOW()),
        (NULL, 6, 134, 1, NOW(), NOW()),
        (NULL, 6, 184, 1, NOW(), NOW()),
        (NULL, 2, 1, 1, NOW(), NOW()),
        (NULL, 2, 2, 1, NOW(), NOW()),
        (NULL, 2, 3, 1, NOW(), NOW()),
        (NULL, 2, 4, 1, NOW(), NOW()),
        (NULL, 2, 5, 1, NOW(), NOW()),
        (NULL, 2, 6, 1, NOW(), NOW()),
        (NULL, 2, 7, 1, NOW(), NOW()),
        (NULL, 2, 8, 1, NOW(), NOW()),
        (NULL, 2, 9, 1, NOW(), NOW()),
        (NULL, 2, 10, 1, NOW(), NOW()),
        (NULL, 2, 11, 1, NOW(), NOW()),
        (NULL, 2, 12, 1, NOW(), NOW()),
        (NULL, 2, 13, 1, NOW(), NOW()),
        (NULL, 2, 14, 1, NOW(), NOW()),
        (NULL, 2, 15, 1, NOW(), NOW()),
        (NULL, 2, 16, 1, NOW(), NOW()),
        (NULL, 2, 17, 1, NOW(), NOW()),
        (NULL, 2, 18, 1, NOW(), NOW()),
        (NULL, 2, 19, 1, NOW(), NOW()),
        (NULL, 2, 20, 1, NOW(), NOW()),
        (NULL, 2, 21, 1, NOW(), NOW()),
        (NULL, 2, 22, 1, NOW(), NOW()),
        (NULL, 2, 23, 1, NOW(), NOW()),
        (NULL, 2, 25, 1, NOW(), NOW()),
        (NULL, 2, 26, 1, NOW(), NOW()),
        (NULL, 2, 27, 1, NOW(), NOW()),
        (NULL, 2, 28, 1, NOW(), NOW()),
        (NULL, 2, 29, 1, NOW(), NOW()),
        (NULL, 2, 30, 1, NOW(), NOW()),
        (NULL, 2, 31, 1, NOW(), NOW()),
        (NULL, 2, 32, 1, NOW(), NOW()),
        (NULL, 2, 33, 1, NOW(), NOW()),
        (NULL, 2, 34, 1, NOW(), NOW()),
        (NULL, 2, 35, 1, NOW(), NOW()),
        (NULL, 2, 36, 1, NOW(), NOW()),
        (NULL, 2, 37, 1, NOW(), NOW()),
        (NULL, 2, 38, 1, NOW(), NOW()),
        (NULL, 2, 39, 1, NOW(), NOW()),
        (NULL, 2, 40, 1, NOW(), NOW()),
        (NULL, 2, 41, 1, NOW(), NOW()),
        (NULL, 2, 42, 1, NOW(), NOW()),
        (NULL, 2, 43, 1, NOW(), NOW()),
        (NULL, 2, 44, 1, NOW(), NOW()),
        (NULL, 2, 45, 1, NOW(), NOW()),
        (NULL, 2, 46, 1, NOW(), NOW()),
        (NULL, 2, 47, 1, NOW(), NOW()),
        (NULL, 2, 48, 1, NOW(), NOW()),
        (NULL, 2, 49, 1, NOW(), NOW()),
        (NULL, 2, 50, 1, NOW(), NOW()),
        (NULL, 2, 51, 1, NOW(), NOW()),
        (NULL, 2, 52, 1, NOW(), NOW()),
        (NULL, 2, 53, 1, NOW(), NOW()),
        (NULL, 2, 54, 1, NOW(), NOW()),
        (NULL, 2, 55, 1, NOW(), NOW()),
        (NULL, 2, 56, 1, NOW(), NOW()),
        (NULL, 2, 57, 1, NOW(), NOW()),
        (NULL, 2, 58, 1, NOW(), NOW()),
        (NULL, 2, 59, 1, NOW(), NOW()),
        (NULL, 2, 60, 1, NOW(), NOW()),
        (NULL, 2, 61, 1, NOW(), NOW()),
        (NULL, 2, 62, 1, NOW(), NOW()),
        (NULL, 2, 63, 1, NOW(), NOW()),
        (NULL, 2, 64, 1, NOW(), NOW()),
        (NULL, 2, 65, 1, NOW(), NOW()),
        (NULL, 2, 66, 1, NOW(), NOW()),
        (NULL, 2, 67, 1, NOW(), NOW()),
        (NULL, 2, 68, 1, NOW(), NOW()),
        (NULL, 2, 69, 1, NOW(), NOW()),
        (NULL, 2, 70, 1, NOW(), NOW()),
        (NULL, 2, 71, 1, NOW(), NOW()),
        (NULL, 2, 72, 1, NOW(), NOW()),
        (NULL, 2, 73, 1, NOW(), NOW()),
        (NULL, 2, 74, 1, NOW(), NOW()),
        (NULL, 2, 76, 1, NOW(), NOW()),
        (NULL, 2, 77, 1, NOW(), NOW()),
        (NULL, 2, 78, 1, NOW(), NOW()),
        (NULL, 2, 79, 1, NOW(), NOW()),
        (NULL, 2, 80, 1, NOW(), NOW()),
        (NULL, 2, 81, 1, NOW(), NOW()),
        (NULL, 2, 82, 1, NOW(), NOW()),
        (NULL, 2, 83, 1, NOW(), NOW()),
        (NULL, 2, 84, 1, NOW(), NOW()),
        (NULL, 2, 85, 1, NOW(), NOW()),
        (NULL, 2, 86, 1, NOW(), NOW()),
        (NULL, 2, 87, 1, NOW(), NOW()),
        (NULL, 2, 88, 1, NOW(), NOW()),
        (NULL, 2, 89, 1, NOW(), NOW()),
        (NULL, 2, 90, 1, NOW(), NOW()),
        (NULL, 2, 91, 1, NOW(), NOW()),
        (NULL, 2, 92, 1, NOW(), NOW()),
        (NULL, 2, 93, 1, NOW(), NOW()),
        (NULL, 2, 94, 1, NOW(), NOW()),
        (NULL, 2, 95, 1, NOW(), NOW()),
        (NULL, 2, 96, 1, NOW(), NOW()),
        (NULL, 2, 97, 1, NOW(), NOW()),
        (NULL, 2, 98, 1, NOW(), NOW()),
        (NULL, 2, 99, 1, NOW(), NOW()),
        (NULL, 2, 100, 1, NOW(), NOW()),
        (NULL, 2, 101, 1, NOW(), NOW()),
        (NULL, 2, 102, 1, NOW(), NOW()),
        (NULL, 2, 103, 1, NOW(), NOW()),
        (NULL, 2, 104, 1, NOW(), NOW()),
        (NULL, 2, 105, 1, NOW(), NOW()),
        (NULL, 2, 106, 1, NOW(), NOW()),
        (NULL, 2, 107, 1, NOW(), NOW()),
        (NULL, 2, 108, 1, NOW(), NOW()),
        (NULL, 2, 109, 1, NOW(), NOW()),
        (NULL, 2, 110, 1, NOW(), NOW()),
        (NULL, 2, 111, 1, NOW(), NOW()),
        (NULL, 2, 112, 1, NOW(), NOW()),
        (NULL, 2, 113, 1, NOW(), NOW()),
        (NULL, 2, 114, 1, NOW(), NOW()),
        (NULL, 2, 115, 1, NOW(), NOW()),
        (NULL, 2, 116, 1, NOW(), NOW()),
        (NULL, 2, 117, 1, NOW(), NOW()),
        (NULL, 2, 118, 1, NOW(), NOW()),
        (NULL, 2, 119, 1, NOW(), NOW()),
        (NULL, 2, 120, 1, NOW(), NOW()),
        (NULL, 2, 121, 1, NOW(), NOW()),
        (NULL, 2, 122, 1, NOW(), NOW()),
        (NULL, 2, 123, 1, NOW(), NOW()),
        (NULL, 2, 124, 1, NOW(), NOW()),
        (NULL, 2, 125, 1, NOW(), NOW()),
        (NULL, 2, 126, 1, NOW(), NOW()),
        (NULL, 2, 127, 1, NOW(), NOW()),
        (NULL, 2, 128, 1, NOW(), NOW()),
        (NULL, 2, 129, 1, NOW(), NOW()),
        (NULL, 2, 130, 1, NOW(), NOW()),
        (NULL, 2, 131, 1, NOW(), NOW()),
        (NULL, 2, 132, 1, NOW(), NOW()),
        (NULL, 2, 133, 1, NOW(), NOW()),
        (NULL, 2, 134, 1, NOW(), NOW()),
        (NULL, 2, 135, 1, NOW(), NOW()),
        (NULL, 2, 136, 1, NOW(), NOW()),
        (NULL, 2, 137, 1, NOW(), NOW()),
        (NULL, 2, 138, 1, NOW(), NOW()),
        (NULL, 2, 139, 1, NOW(), NOW()),
        (NULL, 2, 140, 1, NOW(), NOW()),
        (NULL, 2, 141, 1, NOW(), NOW()),
        (NULL, 2, 142, 1, NOW(), NOW()),
        (NULL, 2, 143, 1, NOW(), NOW()),
        (NULL, 2, 144, 1, NOW(), NOW()),
        (NULL, 2, 145, 1, NOW(), NOW()),
        (NULL, 2, 146, 1, NOW(), NOW()),
        (NULL, 2, 147, 1, NOW(), NOW()),
        (NULL, 2, 148, 1, NOW(), NOW()),
        (NULL, 2, 149, 1, NOW(), NOW()),
        (NULL, 2, 150, 1, NOW(), NOW()),
        (NULL, 2, 151, 1, NOW(), NOW()),
        (NULL, 2, 152, 1, NOW(), NOW()),
        (NULL, 2, 153, 1, NOW(), NOW()),
        (NULL, 2, 154, 1, NOW(), NOW()),
        (NULL, 2, 155, 1, NOW(), NOW()),
        (NULL, 2, 156, 1, NOW(), NOW()),
        (NULL, 2, 157, 1, NOW(), NOW()),
        (NULL, 2, 158, 1, NOW(), NOW()),
        (NULL, 2, 159, 1, NOW(), NOW()),
        (NULL, 2, 160, 1, NOW(), NOW()),
        (NULL, 2, 161, 1, NOW(), NOW()),
        (NULL, 2, 162, 1, NOW(), NOW()),
        (NULL, 2, 163, 1, NOW(), NOW()),
        (NULL, 2, 164, 1, NOW(), NOW()),
        (NULL, 2, 165, 1, NOW(), NOW()),
        (NULL, 2, 166, 1, NOW(), NOW()),
        (NULL, 2, 167, 1, NOW(), NOW()),
        (NULL, 2, 168, 1, NOW(), NOW()),
        (NULL, 2, 169, 1, NOW(), NOW()),
        (NULL, 2, 170, 1, NOW(), NOW()),
        (NULL, 2, 171, 1, NOW(), NOW()),
        (NULL, 2, 172, 1, NOW(), NOW()),
        (NULL, 2, 173, 1, NOW(), NOW()),
        (NULL, 2, 174, 1, NOW(), NOW()),
        (NULL, 2, 175, 1, NOW(), NOW()),
        (NULL, 2, 176, 1, NOW(), NOW()),
        (NULL, 2, 177, 1, NOW(), NOW()),
        (NULL, 2, 178, 1, NOW(), NOW()),
        (NULL, 2, 179, 1, NOW(), NOW()),
        (NULL, 2, 180, 1, NOW(), NOW()),
        (NULL, 2, 181, 1, NOW(), NOW()),
        (NULL, 2, 182, 1, NOW(), NOW()),
        (NULL, 2, 183, 1, NOW(), NOW()),
        (NULL, 2, 184, 1, NOW(), NOW()),
        (NULL, 2, 185, 1, NOW(), NOW()),
        (NULL, 2, 186, 1, NOW(), NOW()),
        (NULL, 2, 187, 1, NOW(), NOW()),
        (NULL, 2, 188, 1, NOW(), NOW()),
        (NULL, 2, 189, 1, NOW(), NOW()),
        (NULL, 2, 190, 1, NOW(), NOW()),
        (NULL, 2, 191, 1, NOW(), NOW()),
        (NULL, 2, 192, 1, NOW(), NOW()),
        (NULL, 2, 193, 1, NOW(), NOW()),
        (NULL, 2, 194, 1, NOW(), NOW()),
        (NULL, 2, 195, 1, NOW(), NOW()),
        (NULL, 2, 196, 1, NOW(), NOW()),
        (NULL, 2, 197, 1, NOW(), NOW()),
        (NULL, 2, 198, 1, NOW(), NOW()),
        (NULL, 2, 199, 1, NOW(), NOW()),
        (NULL, 2, 200, 1, NOW(), NOW()),
        (NULL, 2, 201, 1, NOW(), NOW()),
        (NULL, 2, 202, 1, NOW(), NOW()),
        (NULL, 2, NULL, 1, NOW(), NOW()),
        (NULL, 2, 204, 1, NOW(), NOW()),
        (NULL, 2, 205, 1, NOW(), NOW()),
        (NULL, 2, 206, 1, NOW(), NOW()),
        (NULL, 2, 207, 1, NOW(), NOW()),
        (NULL, 2, 208, 1, NOW(), NOW()),
        (NULL, 2, 209, 1, NOW(), NOW()),
        (NULL, 2, 210, 1, NOW(), NOW()),
        (NULL, 2, 211, 1, NOW(), NOW()),
        (NULL, 2, 212, 1, NOW(), NOW()),
        (NULL, 4, 1, 1, NOW(), NOW()),
        (NULL, 4, 2, 1, NOW(), NOW()),
        (NULL, 4, 9, 1, NOW(), NOW()),
        (NULL, 4, 34, 1, NOW(), NOW()),
        (NULL, 4, 35, 1, NOW(), NOW()),
        (NULL, 4, 36, 1, NOW(), NOW()),
        (NULL, 4, 37, 1, NOW(), NOW()),
        (NULL, 4, 76, 1, NOW(), NOW()),
        (NULL, 4, 77, 1, NOW(), NOW()),
        (NULL, 4, 78, 1, NOW(), NOW()),
        (NULL, 4, 79, 1, NOW(), NOW()),
        (NULL, 4, 80, 1, NOW(), NOW()),
        (NULL, 4, 81, 1, NOW(), NOW()),
        (NULL, 4, 82, 1, NOW(), NOW()),
        (NULL, 4, 91, 1, NOW(), NOW()),
        (NULL, 4, 95, 1, NOW(), NOW()),
        (NULL, 4, 96, 1, NOW(), NOW()),
        (NULL, 4, 97, 1, NOW(), NOW()),
        (NULL, 4, 98, 1, NOW(), NOW()),
        (NULL, 4, 118, 1, NOW(), NOW()),
        (NULL, 4, 119, 1, NOW(), NOW()),
        (NULL, 4, 123, 1, NOW(), NOW()),
        (NULL, 4, 167, 1, NOW(), NOW()),
        (NULL, 4, 174, 1, NOW(), NOW()),
        (NULL, 4, 187, 1, NOW(), NOW()),
        (NULL, 4, 188, 1, NOW(), NOW()),
        (NULL, 4, 189, 1, NOW(), NOW()),
        (NULL, 4, 190, 1, NOW(), NOW()),
        (NULL, 4, 191, 1, NOW(), NOW()),
        (NULL, 4, 192, 1, NOW(), NOW()),
        (NULL, 4, 193, 1, NOW(), NOW()),
        (NULL, 4, 194, 1, NOW(), NOW()),
        (NULL, 4, 195, 1, NOW(), NOW()),
        (NULL, 4, 196, 1, NOW(), NOW()),
        (NULL, 4, 197, 1, NOW(), NOW()),
        (NULL, 4, 205, 1, NOW(), NOW()),
        (NULL, 4, 206, 1, NOW(), NOW())");

        $hashed_password = Hash::make("posuser");

        $manager_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "100",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "manager@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $manager_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $accounts_manager_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "101",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "accounts@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $accounts_manager_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $cashier_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "102",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "cashier@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $cashier_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $waiter_user_id_1 = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "103",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "waiter1@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $waiter_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $waiter_user_id_2 = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "104",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "waiter2@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $waiter_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $waiter_user_id_3 = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "105",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "waiter3@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $waiter_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        $chef_user_id = DB::table("users")->insertGetId([
            "slack" => $base_controller->generate_slack("users"),
            "user_code" => "106",
            "fullname" => $faker->firstName ." ".$faker->lastName,
            "email" => "chef@appsthing.com",
            "password" => $hashed_password,
            "phone" => $faker->e164PhoneNumber,
            "role_id" => $chef_role_id, 
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::insert("INSERT INTO `user_menus` (`id`, `user_id`, `menu_id`, `created_by`, `created_at`, `updated_at`) VALUES
        (NULL, 1, 1, 1, NOW(), NOW()),
        (NULL, 1, 2, 1, NOW(), NOW()),
        (NULL, 1, 3, 1, NOW(), NOW()),
        (NULL, 1, 4, 1, NOW(), NOW()),
        (NULL, 1, 5, 1, NOW(), NOW()),
        (NULL, 1, 6, 1, NOW(), NOW()),
        (NULL, 1, 7, 1, NOW(), NOW()),
        (NULL, 1, 8, 1, NOW(), NOW()),
        (NULL, 1, 9, 1, NOW(), NOW()),
        (NULL, 1, 10, 1, NOW(), NOW()),
        (NULL, 1, 11, 1, NOW(), NOW()),
        (NULL, 1, 12, 1, NOW(), NOW()),
        (NULL, 1, 13, 1, NOW(), NOW()),
        (NULL, 1, 14, 1, NOW(), NOW()),
        (NULL, 1, 15, 1, NOW(), NOW()),
        (NULL, 1, 16, 1, NOW(), NOW()),
        (NULL, 1, 17, 1, NOW(), NOW()),
        (NULL, 1, 18, 1, NOW(), NOW()),
        (NULL, 1, 19, 1, NOW(), NOW()),
        (NULL, 1, 20, 1, NOW(), NOW()),
        (NULL, 1, 21, 1, NOW(), NOW()),
        (NULL, 1, 22, 1, NOW(), NOW()),
        (NULL, 1, 23, 1, NOW(), NOW()),
        (NULL, 1, 24, 1, NOW(), NOW()),
        (NULL, 1, 25, 1, NOW(), NOW()),
        (NULL, 1, 26, 1, NOW(), NOW()),
        (NULL, 1, 27, 1, NOW(), NOW()),
        (NULL, 1, 28, 1, NOW(), NOW()),
        (NULL, 1, 29, 1, NOW(), NOW()),
        (NULL, 1, 30, 1, NOW(), NOW()),
        (NULL, 1, 31, 1, NOW(), NOW()),
        (NULL, 1, 32, 1, NOW(), NOW()),
        (NULL, 1, 33, 1, NOW(), NOW()),
        (NULL, 1, 34, 1, NOW(), NOW()),
        (NULL, 1, 35, 1, NOW(), NOW()),
        (NULL, 1, 36, 1, NOW(), NOW()),
        (NULL, 1, 37, 1, NOW(), NOW()),
        (NULL, 1, 38, 1, NOW(), NOW()),
        (NULL, 1, 39, 1, NOW(), NOW()),
        (NULL, 1, 40, 1, NOW(), NOW()),
        (NULL, 1, 41, 1, NOW(), NOW()),
        (NULL, 1, 42, 1, NOW(), NOW()),
        (NULL, 1, 43, 1, NOW(), NOW()),
        (NULL, 1, 44, 1, NOW(), NOW()),
        (NULL, 1, 45, 1, NOW(), NOW()),
        (NULL, 1, 46, 1, NOW(), NOW()),
        (NULL, 1, 47, 1, NOW(), NOW()),
        (NULL, 1, 48, 1, NOW(), NOW()),
        (NULL, 1, 49, 1, NOW(), NOW()),
        (NULL, 1, 50, 1, NOW(), NOW()),
        (NULL, 1, 51, 1, NOW(), NOW()),
        (NULL, 1, 52, 1, NOW(), NOW()),
        (NULL, 1, 53, 1, NOW(), NOW()),
        (NULL, 1, 54, 1, NOW(), NOW()),
        (NULL, 1, 55, 1, NOW(), NOW()),
        (NULL, 1, 56, 1, NOW(), NOW()),
        (NULL, 1, 57, 1, NOW(), NOW()),
        (NULL, 1, 58, 1, NOW(), NOW()),
        (NULL, 1, 59, 1, NOW(), NOW()),
        (NULL, 1, 60, 1, NOW(), NOW()),
        (NULL, 1, 61, 1, NOW(), NOW()),
        (NULL, 1, 62, 1, NOW(), NOW()),
        (NULL, 1, 63, 1, NOW(), NOW()),
        (NULL, 1, 64, 1, NOW(), NOW()),
        (NULL, 1, 65, 1, NOW(), NOW()),
        (NULL, 1, 66, 1, NOW(), NOW()),
        (NULL, 1, 67, 1, NOW(), NOW()),
        (NULL, 1, 68, 1, NOW(), NOW()),
        (NULL, 1, 69, 1, NOW(), NOW()),
        (NULL, 1, 70, 1, NOW(), NOW()),
        (NULL, 1, 71, 1, NOW(), NOW()),
        (NULL, 1, 72, 1, NOW(), NOW()),
        (NULL, 1, 73, 1, NOW(), NOW()),
        (NULL, 1, 74, 1, NOW(), NOW()),
        (NULL, 1, 75, 1, NOW(), NOW()),
        (NULL, 4, 1, 1, NOW(), NOW()),
        (NULL, 4, 2, 1, NOW(), NOW()),
        (NULL, 4, 7, 1, NOW(), NOW()),
        (NULL, 4, 8, 1, NOW(), NOW()),
        (NULL, 4, 9, 1, NOW(), NOW()),
        (NULL, 4, 10, 1, NOW(), NOW()),
        (NULL, 4, 20, 1, NOW(), NOW()),
        (NULL, 4, 34, 1, NOW(), NOW()),
        (NULL, 4, 35, 1, NOW(), NOW()),
        (NULL, 4, 36, 1, NOW(), NOW()),
        (NULL, 4, 37, 1, NOW(), NOW()),
        (NULL, 4, 57, 1, NOW(), NOW()),
        (NULL, 4, 58, 1, NOW(), NOW()),
        (NULL, 4, 59, 1, NOW(), NOW()),
        (NULL, 4, 70, 1, NOW(), NOW()),
        (NULL, 4, 71, 1, NOW(), NOW()),
        (NULL, 4, 72, 1, NOW(), NOW()),
        (NULL, 4, 73, 1, NOW(), NOW()),
        (NULL, 4, 76, 1, NOW(), NOW()),
        (NULL, 4, 77, 1, NOW(), NOW()),
        (NULL, 4, 78, 1, NOW(), NOW()),
        (NULL, 4, 79, 1, NOW(), NOW()),
        (NULL, 4, 80, 1, NOW(), NOW()),
        (NULL, 4, 81, 1, NOW(), NOW()),
        (NULL, 4, 82, 1, NOW(), NOW()),
        (NULL, 4, 83, 1, NOW(), NOW()),
        (NULL, 4, 84, 1, NOW(), NOW()),
        (NULL, 4, 85, 1, NOW(), NOW()),
        (NULL, 4, 86, 1, NOW(), NOW()),
        (NULL, 4, 87, 1, NOW(), NOW()),
        (NULL, 4, 88, 1, NOW(), NOW()),
        (NULL, 4, 89, 1, NOW(), NOW()),
        (NULL, 4, 90, 1, NOW(), NOW()),
        (NULL, 4, 91, 1, NOW(), NOW()),
        (NULL, 4, 92, 1, NOW(), NOW()),
        (NULL, 4, 93, 1, NOW(), NOW()),
        (NULL, 4, 94, 1, NOW(), NOW()),
        (NULL, 4, 95, 1, NOW(), NOW()),
        (NULL, 4, 96, 1, NOW(), NOW()),
        (NULL, 4, 97, 1, NOW(), NOW()),
        (NULL, 4, 98, 1, NOW(), NOW()),
        (NULL, 4, 99, 1, NOW(), NOW()),
        (NULL, 4, 107, 1, NOW(), NOW()),
        (NULL, 4, 108, 1, NOW(), NOW()),
        (NULL, 4, 109, 1, NOW(), NOW()),
        (NULL, 4, 110, 1, NOW(), NOW()),
        (NULL, 4, 111, 1, NOW(), NOW()),
        (NULL, 4, 118, 1, NOW(), NOW()),
        (NULL, 4, 119, 1, NOW(), NOW()),
        (NULL, 4, 120, 1, NOW(), NOW()),
        (NULL, 4, 121, 1, NOW(), NOW()),
        (NULL, 4, 122, 1, NOW(), NOW()),
        (NULL, 4, 123, 1, NOW(), NOW()),
        (NULL, 4, 124, 1, NOW(), NOW()),
        (NULL, 4, 137, 1, NOW(), NOW()),
        (NULL, 4, 150, 1, NOW(), NOW()),
        (NULL, 4, 151, 1, NOW(), NOW()),
        (NULL, 4, 152, 1, NOW(), NOW()),
        (NULL, 4, 153, 1, NOW(), NOW()),
        (NULL, 4, 154, 1, NOW(), NOW()),
        (NULL, 4, 155, 1, NOW(), NOW()),
        (NULL, 4, 156, 1, NOW(), NOW()),
        (NULL, 4, 167, 1, NOW(), NOW()),
        (NULL, 4, 168, 1, NOW(), NOW()),
        (NULL, 4, 169, 1, NOW(), NOW()),
        (NULL, 4, 170, 1, NOW(), NOW()),
        (NULL, 4, 171, 1, NOW(), NOW()),
        (NULL, 4, 172, 1, NOW(), NOW()),
        (NULL, 4, 173, 1, NOW(), NOW()),
        (NULL, 4, 174, 1, NOW(), NOW()),
        (NULL, 4, 175, 1, NOW(), NOW()),
        (NULL, 4, 187, 1, NOW(), NOW()),
        (NULL, 4, 188, 1, NOW(), NOW()),
        (NULL, 4, 189, 1, NOW(), NOW()),
        (NULL, 6, 100, 1, NOW(), NOW()),
        (NULL, 6, 178, 1, NOW(), NOW()),
        (NULL, 6, 184, 1, NOW(), NOW()),
        (NULL, 7, 100, 1, NOW(), NOW()),
        (NULL, 7, 178, 1, NOW(), NOW()),
        (NULL, 7, 184, 1, NOW(), NOW()),
        (NULL, 8, 100, 1, NOW(), NOW()),
        (NULL, 8, 178, 1, NOW(), NOW()),
        (NULL, 8, 184, 1, NOW(), NOW()),
        (NULL, 9, 100, 1, NOW(), NOW()),
        (NULL, 9, 101, 1, NOW(), NOW()),
        (NULL, 9, 106, 1, NOW(), NOW()),
        (NULL, 9, 134, 1, NOW(), NOW()),
        (NULL, 9, 184, 1, NOW(), NOW()),
        (NULL, 3, 1, 1, NOW(), NOW()),
        (NULL, 3, 2, 1, NOW(), NOW()),
        (NULL, 3, 3, 1, NOW(), NOW()),
        (NULL, 3, 4, 1, NOW(), NOW()),
        (NULL, 3, 5, 1, NOW(), NOW()),
        (NULL, 3, 6, 1, NOW(), NOW()),
        (NULL, 3, 7, 1, NOW(), NOW()),
        (NULL, 3, 8, 1, NOW(), NOW()),
        (NULL, 3, 9, 1, NOW(), NOW()),
        (NULL, 3, 10, 1, NOW(), NOW()),
        (NULL, 3, 11, 1, NOW(), NOW()),
        (NULL, 3, 12, 1, NOW(), NOW()),
        (NULL, 3, 13, 1, NOW(), NOW()),
        (NULL, 3, 14, 1, NOW(), NOW()),
        (NULL, 3, 15, 1, NOW(), NOW()),
        (NULL, 3, 16, 1, NOW(), NOW()),
        (NULL, 3, 17, 1, NOW(), NOW()),
        (NULL, 3, 18, 1, NOW(), NOW()),
        (NULL, 3, 19, 1, NOW(), NOW()),
        (NULL, 3, 20, 1, NOW(), NOW()),
        (NULL, 3, 21, 1, NOW(), NOW()),
        (NULL, 3, 22, 1, NOW(), NOW()),
        (NULL, 3, 23, 1, NOW(), NOW()),
        (NULL, 3, 25, 1, NOW(), NOW()),
        (NULL, 3, 26, 1, NOW(), NOW()),
        (NULL, 3, 27, 1, NOW(), NOW()),
        (NULL, 3, 28, 1, NOW(), NOW()),
        (NULL, 3, 29, 1, NOW(), NOW()),
        (NULL, 3, 30, 1, NOW(), NOW()),
        (NULL, 3, 31, 1, NOW(), NOW()),
        (NULL, 3, 32, 1, NOW(), NOW()),
        (NULL, 3, 33, 1, NOW(), NOW()),
        (NULL, 3, 34, 1, NOW(), NOW()),
        (NULL, 3, 35, 1, NOW(), NOW()),
        (NULL, 3, 36, 1, NOW(), NOW()),
        (NULL, 3, 37, 1, NOW(), NOW()),
        (NULL, 3, 38, 1, NOW(), NOW()),
        (NULL, 3, 39, 1, NOW(), NOW()),
        (NULL, 3, 40, 1, NOW(), NOW()),
        (NULL, 3, 41, 1, NOW(), NOW()),
        (NULL, 3, 42, 1, NOW(), NOW()),
        (NULL, 3, 43, 1, NOW(), NOW()),
        (NULL, 3, 44, 1, NOW(), NOW()),
        (NULL, 3, 45, 1, NOW(), NOW()),
        (NULL, 3, 46, 1, NOW(), NOW()),
        (NULL, 3, 47, 1, NOW(), NOW()),
        (NULL, 3, 48, 1, NOW(), NOW()),
        (NULL, 3, 49, 1, NOW(), NOW()),
        (NULL, 3, 50, 1, NOW(), NOW()),
        (NULL, 3, 51, 1, NOW(), NOW()),
        (NULL, 3, 52, 1, NOW(), NOW()),
        (NULL, 3, 53, 1, NOW(), NOW()),
        (NULL, 3, 54, 1, NOW(), NOW()),
        (NULL, 3, 55, 1, NOW(), NOW()),
        (NULL, 3, 56, 1, NOW(), NOW()),
        (NULL, 3, 57, 1, NOW(), NOW()),
        (NULL, 3, 58, 1, NOW(), NOW()),
        (NULL, 3, 59, 1, NOW(), NOW()),
        (NULL, 3, 60, 1, NOW(), NOW()),
        (NULL, 3, 61, 1, NOW(), NOW()),
        (NULL, 3, 62, 1, NOW(), NOW()),
        (NULL, 3, 63, 1, NOW(), NOW()),
        (NULL, 3, 64, 1, NOW(), NOW()),
        (NULL, 3, 65, 1, NOW(), NOW()),
        (NULL, 3, 66, 1, NOW(), NOW()),
        (NULL, 3, 67, 1, NOW(), NOW()),
        (NULL, 3, 68, 1, NOW(), NOW()),
        (NULL, 3, 69, 1, NOW(), NOW()),
        (NULL, 3, 70, 1, NOW(), NOW()),
        (NULL, 3, 71, 1, NOW(), NOW()),
        (NULL, 3, 72, 1, NOW(), NOW()),
        (NULL, 3, 73, 1, NOW(), NOW()),
        (NULL, 3, 74, 1, NOW(), NOW()),
        (NULL, 3, 76, 1, NOW(), NOW()),
        (NULL, 3, 77, 1, NOW(), NOW()),
        (NULL, 3, 78, 1, NOW(), NOW()),
        (NULL, 3, 79, 1, NOW(), NOW()),
        (NULL, 3, 80, 1, NOW(), NOW()),
        (NULL, 3, 81, 1, NOW(), NOW()),
        (NULL, 3, 82, 1, NOW(), NOW()),
        (NULL, 3, 83, 1, NOW(), NOW()),
        (NULL, 3, 84, 1, NOW(), NOW()),
        (NULL, 3, 85, 1, NOW(), NOW()),
        (NULL, 3, 86, 1, NOW(), NOW()),
        (NULL, 3, 87, 1, NOW(), NOW()),
        (NULL, 3, 88, 1, NOW(), NOW()),
        (NULL, 3, 89, 1, NOW(), NOW()),
        (NULL, 3, 90, 1, NOW(), NOW()),
        (NULL, 3, 91, 1, NOW(), NOW()),
        (NULL, 3, 92, 1, NOW(), NOW()),
        (NULL, 3, 93, 1, NOW(), NOW()),
        (NULL, 3, 94, 1, NOW(), NOW()),
        (NULL, 3, 95, 1, NOW(), NOW()),
        (NULL, 3, 96, 1, NOW(), NOW()),
        (NULL, 3, 97, 1, NOW(), NOW()),
        (NULL, 3, 98, 1, NOW(), NOW()),
        (NULL, 3, 99, 1, NOW(), NOW()),
        (NULL, 3, 100, 1, NOW(), NOW()),
        (NULL, 3, 101, 1, NOW(), NOW()),
        (NULL, 3, 102, 1, NOW(), NOW()),
        (NULL, 3, 103, 1, NOW(), NOW()),
        (NULL, 3, 104, 1, NOW(), NOW()),
        (NULL, 3, 105, 1, NOW(), NOW()),
        (NULL, 3, 106, 1, NOW(), NOW()),
        (NULL, 3, 107, 1, NOW(), NOW()),
        (NULL, 3, 108, 1, NOW(), NOW()),
        (NULL, 3, 109, 1, NOW(), NOW()),
        (NULL, 3, 110, 1, NOW(), NOW()),
        (NULL, 3, 111, 1, NOW(), NOW()),
        (NULL, 3, 112, 1, NOW(), NOW()),
        (NULL, 3, 113, 1, NOW(), NOW()),
        (NULL, 3, 114, 1, NOW(), NOW()),
        (NULL, 3, 115, 1, NOW(), NOW()),
        (NULL, 3, 116, 1, NOW(), NOW()),
        (NULL, 3, 117, 1, NOW(), NOW()),
        (NULL, 3, 118, 1, NOW(), NOW()),
        (NULL, 3, 119, 1, NOW(), NOW()),
        (NULL, 3, 120, 1, NOW(), NOW()),
        (NULL, 3, 121, 1, NOW(), NOW()),
        (NULL, 3, 122, 1, NOW(), NOW()),
        (NULL, 3, 123, 1, NOW(), NOW()),
        (NULL, 3, 124, 1, NOW(), NOW()),
        (NULL, 3, 125, 1, NOW(), NOW()),
        (NULL, 3, 126, 1, NOW(), NOW()),
        (NULL, 3, 127, 1, NOW(), NOW()),
        (NULL, 3, 128, 1, NOW(), NOW()),
        (NULL, 3, 129, 1, NOW(), NOW()),
        (NULL, 3, 130, 1, NOW(), NOW()),
        (NULL, 3, 131, 1, NOW(), NOW()),
        (NULL, 3, 132, 1, NOW(), NOW()),
        (NULL, 3, 133, 1, NOW(), NOW()),
        (NULL, 3, 134, 1, NOW(), NOW()),
        (NULL, 3, 135, 1, NOW(), NOW()),
        (NULL, 3, 136, 1, NOW(), NOW()),
        (NULL, 3, 137, 1, NOW(), NOW()),
        (NULL, 3, 138, 1, NOW(), NOW()),
        (NULL, 3, 139, 1, NOW(), NOW()),
        (NULL, 3, 140, 1, NOW(), NOW()),
        (NULL, 3, 141, 1, NOW(), NOW()),
        (NULL, 3, 142, 1, NOW(), NOW()),
        (NULL, 3, 143, 1, NOW(), NOW()),
        (NULL, 3, 144, 1, NOW(), NOW()),
        (NULL, 3, 145, 1, NOW(), NOW()),
        (NULL, 3, 146, 1, NOW(), NOW()),
        (NULL, 3, 147, 1, NOW(), NOW()),
        (NULL, 3, 148, 1, NOW(), NOW()),
        (NULL, 3, 149, 1, NOW(), NOW()),
        (NULL, 3, 150, 1, NOW(), NOW()),
        (NULL, 3, 151, 1, NOW(), NOW()),
        (NULL, 3, 152, 1, NOW(), NOW()),
        (NULL, 3, 153, 1, NOW(), NOW()),
        (NULL, 3, 154, 1, NOW(), NOW()),
        (NULL, 3, 155, 1, NOW(), NOW()),
        (NULL, 3, 156, 1, NOW(), NOW()),
        (NULL, 3, 157, 1, NOW(), NOW()),
        (NULL, 3, 158, 1, NOW(), NOW()),
        (NULL, 3, 159, 1, NOW(), NOW()),
        (NULL, 3, 160, 1, NOW(), NOW()),
        (NULL, 3, 161, 1, NOW(), NOW()),
        (NULL, 3, 162, 1, NOW(), NOW()),
        (NULL, 3, 163, 1, NOW(), NOW()),
        (NULL, 3, 164, 1, NOW(), NOW()),
        (NULL, 3, 165, 1, NOW(), NOW()),
        (NULL, 3, 166, 1, NOW(), NOW()),
        (NULL, 3, 167, 1, NOW(), NOW()),
        (NULL, 3, 168, 1, NOW(), NOW()),
        (NULL, 3, 169, 1, NOW(), NOW()),
        (NULL, 3, 170, 1, NOW(), NOW()),
        (NULL, 3, 171, 1, NOW(), NOW()),
        (NULL, 3, 172, 1, NOW(), NOW()),
        (NULL, 3, 173, 1, NOW(), NOW()),
        (NULL, 3, 174, 1, NOW(), NOW()),
        (NULL, 3, 175, 1, NOW(), NOW()),
        (NULL, 3, 176, 1, NOW(), NOW()),
        (NULL, 3, 177, 1, NOW(), NOW()),
        (NULL, 3, 178, 1, NOW(), NOW()),
        (NULL, 3, 179, 1, NOW(), NOW()),
        (NULL, 3, 180, 1, NOW(), NOW()),
        (NULL, 3, 181, 1, NOW(), NOW()),
        (NULL, 3, 182, 1, NOW(), NOW()),
        (NULL, 3, 183, 1, NOW(), NOW()),
        (NULL, 3, 184, 1, NOW(), NOW()),
        (NULL, 3, 185, 1, NOW(), NOW()),
        (NULL, 3, 186, 1, NOW(), NOW()),
        (NULL, 3, 187, 1, NOW(), NOW()),
        (NULL, 3, 188, 1, NOW(), NOW()),
        (NULL, 3, 189, 1, NOW(), NOW()),
        (NULL, 3, 190, 1, NOW(), NOW()),
        (NULL, 3, 191, 1, NOW(), NOW()),
        (NULL, 3, 192, 1, NOW(), NOW()),
        (NULL, 3, 193, 1, NOW(), NOW()),
        (NULL, 3, 194, 1, NOW(), NOW()),
        (NULL, 3, 195, 1, NOW(), NOW()),
        (NULL, 3, 196, 1, NOW(), NOW()),
        (NULL, 3, 197, 1, NOW(), NOW()),
        (NULL, 3, 198, 1, NOW(), NOW()),
        (NULL, 3, 199, 1, NOW(), NOW()),
        (NULL, 3, 200, 1, NOW(), NOW()),
        (NULL, 3, 201, 1, NOW(), NOW()),
        (NULL, 3, 202, 1, NOW(), NOW()),
        (NULL, 3, 203, 1, NOW(), NOW()),
        (NULL, 3, 204, 1, NOW(), NOW()),
        (NULL, 3, 205, 1, NOW(), NOW()),
        (NULL, 3, 206, 1, NOW(), NOW()),
        (NULL, 3, 207, 1, NOW(), NOW()),
        (NULL, 3, 208, 1, NOW(), NOW()),
        (NULL, 3, 209, 1, NOW(), NOW()),
        (NULL, 3, 210, 1, NOW(), NOW()),
        (NULL, 3, 211, 1, NOW(), NOW()),
        (NULL, 3, 212, 1, NOW(), NOW()),
        (NULL, 5, 1, 1, NOW(), NOW()),
        (NULL, 5, 2, 1, NOW(), NOW()),
        (NULL, 5, 9, 1, NOW(), NOW()),
        (NULL, 5, 34, 1, NOW(), NOW()),
        (NULL, 5, 35, 1, NOW(), NOW()),
        (NULL, 5, 36, 1, NOW(), NOW()),
        (NULL, 5, 37, 1, NOW(), NOW()),
        (NULL, 5, 76, 1, NOW(), NOW()),
        (NULL, 5, 77, 1, NOW(), NOW()),
        (NULL, 5, 78, 1, NOW(), NOW()),
        (NULL, 5, 79, 1, NOW(), NOW()),
        (NULL, 5, 80, 1, NOW(), NOW()),
        (NULL, 5, 81, 1, NOW(), NOW()),
        (NULL, 5, 82, 1, NOW(), NOW()),
        (NULL, 5, 91, 1, NOW(), NOW()),
        (NULL, 5, 95, 1, NOW(), NOW()),
        (NULL, 5, 96, 1, NOW(), NOW()),
        (NULL, 5, 97, 1, NOW(), NOW()),
        (NULL, 5, 98, 1, NOW(), NOW()),
        (NULL, 5, 118, 1, NOW(), NOW()),
        (NULL, 5, 119, 1, NOW(), NOW()),
        (NULL, 5, 123, 1, NOW(), NOW()),
        (NULL, 5, 167, 1, NOW(), NOW()),
        (NULL, 5, 174, 1, NOW(), NOW()),
        (NULL, 5, 187, 1, NOW(), NOW()),
        (NULL, 5, 188, 1, NOW(), NOW()),
        (NULL, 5, 189, 1, NOW(), NOW()),
        (NULL, 5, 190, 1, NOW(), NOW()),
        (NULL, 5, 191, 1, NOW(), NOW()),
        (NULL, 5, 192, 1, NOW(), NOW()),
        (NULL, 5, 193, 1, NOW(), NOW()),
        (NULL, 5, 194, 1, NOW(), NOW()),
        (NULL, 5, 195, 1, NOW(), NOW()),
        (NULL, 5, 196, 1, NOW(), NOW()),
        (NULL, 5, 197, 1, NOW(), NOW()),
        (NULL, 5, 205, 1, NOW(), NOW()),
        (NULL, 5, 206, 1, NOW(), NOW())");

        DB::table("user_stores")->insert([
            ['user_id' => $manager_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'store_id' => $store_4,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $manager_user_id,'store_id' => $store_5,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],

            ['user_id' => $accounts_manager_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $accounts_manager_user_id,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $accounts_manager_user_id,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $accounts_manager_user_id,'store_id' => $store_4,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $accounts_manager_user_id,'store_id' => $store_5,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            
            ['user_id' => $cashier_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $cashier_user_id,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $cashier_user_id,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],

            ['user_id' => $waiter_user_id_1,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_1,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_1,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_1,'store_id' => $store_4,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_1,'store_id' => $store_5,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],

            ['user_id' => $waiter_user_id_2,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_2,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_2,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_2,'store_id' => $store_4,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_2,'store_id' => $store_5,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],

            ['user_id' => $waiter_user_id_3,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_3,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_3,'store_id' => $store_3,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_3,'store_id' => $store_4,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $waiter_user_id_3,'store_id' => $store_5,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],

            ['user_id' => $chef_user_id,'store_id' => $store_1,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
            ['user_id' => $chef_user_id,'store_id' => $store_2,'created_by' => '1','created_at' => NOW(),'updated_at' => NOW()],
        ]);

        DB::insert("INSERT INTO `discount_codes` VALUES
        (NULL, 'BJG7srxA6sntdyRZlSuAYEhln', 1, 'Discount 5%', 'DISCOUNT5', '5.00', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 'iIHzNpSGsBJG0NJfNE1lAGXcz', 2, 'Discount 5%', 'DISCOUNT5', '5.00', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 'BJG7srxA6sntdyRZlSuAYE2E3', 3, 'Discount 5%', 'DISCOUNT5', '5.00', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 'iIHzNpSGsBJG0NJfNE1lAGiUY', 4, 'Discount 5%', 'DISCOUNT5', '5.00', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 'iIHzNpSGsBJG0NJfNE1lAGiVC', 5, 'Discount 5%', 'DISCOUNT5', '5.00', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14')");

        DB::insert("INSERT INTO `tax_codes` VALUES
        (NULL, 'bZHz4YfWmYRpozFqRdhiiSAko', 1, 'EXCLUSIVE', 'Tax 7.5%', 'TAX75', '7.50', '', 1, 1, NULL, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 'ul9fONoVjmBJWcBGEv7RVTsO0', 2, 'EXCLUSIVE', 'GST Tax', 'GST', '7.00', NULL, 1, 1, 1, '2020-03-10 11:02:14', '2020-03-10 11:04:12'),
        (NULL, 'yaXOLmFhzCUetYRwDtuuK8O8B', 3, 'EXCLUSIVE', 'Tax', 'TAX', '6.50', NULL, 1, 1, NULL, '2020-03-10 11:07:11', '2020-03-10 11:07:11'),
        (NULL, 'JUd4U9ddHa6kAB3d7wDVSoWAA', 4, 'EXCLUSIVE', 'Tax', 'TAX', '5.50', NULL, 1, 1, NULL, '2020-03-10 11:07:40', '2020-03-10 11:07:40'),
        (NULL, 'onyZTAozLdZmzhJcRv9BB1cxq', 5, 'EXCLUSIVE', 'Tax', 'TAX', '10.00', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 'ontZTAozLdZmzhJcRv9BB1cxr', 1, 'EXCLUSIVE', 'Tax 0', 'TAX0', '0', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 'ontZTAozLdZmzhJcRv9BB1cxw', 2, 'EXCLUSIVE', 'Tax 0', 'TAX0', '0', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 'ontZT3ozLdZmzhJcRv9BB1cxq', 3, 'EXCLUSIVE', 'Tax 0', 'TAX0', '0', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 'ontZT2ozLdZmzhJcRv9BB1cxy', 4, 'EXCLUSIVE', 'Tax 0', 'TAX0', '0', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 'ontZT1ozLdZmzhJcRv9BB1cxn', 5, 'EXCLUSIVE', 'Tax 0', 'TAX0', '0', NULL, 1, 1, NULL, '2020-03-10 11:08:13', '2020-03-10 11:08:13')");
        

        DB::insert("INSERT INTO `tax_code_type` VALUES
        (NULL, 1, 'GST', '7.50', 1, '2020-03-10 11:02:14', NULL),
        (NULL, 2, 'CGST', '3.50', 1, '2020-03-10 11:04:12', '2020-03-10 11:04:12'),
        (NULL, 2, 'SGST', '3.50', 1, '2020-03-10 11:04:12', '2020-03-10 11:04:12'),
        (NULL, 3, 'TAX', '6.50', 1, '2020-03-10 11:07:11', '2020-03-10 11:07:11'),
        (NULL, 4, 'TAX', '5.50', 1, '2020-03-10 11:07:40', '2020-03-10 11:07:40'),
        (NULL, 5, 'TAX', '10.00', 1, '2020-03-10 11:08:13', '2020-03-10 11:08:13'),
        (NULL, 6, 'TAX', '0', 1, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 7, 'TAX', '0', 2, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 8, 'TAX', '0', 3, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 9, 'TAX', '0', 4, '2020-03-10 11:02:14', '2020-03-10 11:02:14'),
        (NULL, 10, 'TAX', '0', 5, '2020-03-10 11:02:14', '2020-03-10 11:02:14')");

        
        DB::table("payment_methods")->insert([
            "slack" => $base_controller->generate_slack("payment_methods"),
            "label" => Str::title("Cash"),
            "description" => "Cash Payment",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        DB::table("payment_methods")->insert([
            "slack" => $base_controller->generate_slack("payment_methods"),
            "label" => Str::title("Card"),
            "description" => "Card Payment",
            "status" => 1,
            "created_at" => NOW(),
            "updated_at" => NOW(),
            "created_by" => 1
        ]);

        for ($i = 0; $i < 100; $i++) {
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            DB::table('customers')->insert([
                'slack' => $base_controller->generate_slack("customers"),
                'customer_type' => 'CUSTOM',
                'name' => $firstname ." ".$lastname,
                'email' => $faker->unique()->email,
                'phone' => $faker->e164PhoneNumber,
                'address' => $faker->address,
                'status' => 1,
                'created_by' => 1,
                'created_at' => NOW(),
                'updated_at' => NOW()
            ]);
        }

        $active_stores = StoreModel::select('id')
        ->active()
        ->get();

        if(count($active_stores)>0){
            foreach ($active_stores as $active_store) {

                $account_exists = AccountModel::select('id')
                ->where('store_id', '=', trim($active_store->id))
                ->first();
                if (empty($account_exists)) {
                    
                    $account = [
                        "slack" => $base_controller->generate_slack("accounts"),
                        "store_id" => $active_store->id,
                        "account_code" => Str::random(6),
                        "account_type" => 1,
                        "label" => 'Default Sales Account',
                        "description" => 'Default Sales Account',
                        "initial_balance" => 0,
                        "pos_default" => 1,
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $account_id = AccountModel::create($account)->id;
                    
                    $code_start_config = Config::get('constants.unique_code_start.account');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $account_code = [
                        "account_code" => ($code_start+$account_id)
                    ];

                    AccountModel::withoutGlobalScopes()->where('id', $account_id)
                    ->update($account_code);
                }

                $supplier = [
                    "slack" => $base_controller->generate_slack("suppliers"),
                    "store_id" => $active_store->id,
                    "supplier_code" => Str::random(6),
                    "name" => "Food Mart Co. Ltd.",
                    "address" => $faker->address,
                    "phone" => $faker->e164PhoneNumber,
                    "email" => $faker->unique()->email,
                    "pincode" => '11111',
                    "status" => 1,
                    "created_by" => 1
                ];
                
                $supplier_id = SupplierModel::create($supplier)->id;
    
                $code_start_config = Config::get('constants.unique_code_start.supplier');
                $code_start = (isset($code_start_config))?$code_start_config:100;
                
                $supplier_code = [
                    "supplier_code" => "SUP".($code_start+$supplier_id)
                ];
                SupplierModel::withoutGlobalScopes()->where('id', $supplier_id)
                ->update($supplier_code);
                
                for($j = 0; $j <= 25; $j++){
                    $supplier = [
                        "slack" => $base_controller->generate_slack("suppliers"),
                        "store_id" => $active_store->id,
                        "supplier_code" => Str::random(6),
                        "name" => $faker->catchPhrase,
                        "address" => $faker->address,
                        "phone" => $faker->e164PhoneNumber,
                        "email" => $faker->unique()->email,
                        "pincode" => '11111',
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $supplier_id = SupplierModel::create($supplier)->id;
        
                    $code_start_config = Config::get('constants.unique_code_start.supplier');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $supplier_code = [
                        "supplier_code" => "SUP".($code_start+$supplier_id)
                    ];
                    SupplierModel::withoutGlobalScopes()->where('id', $supplier_id)
                    ->update($supplier_code);
                }

                $target_month = date("Y-01-01");

                for($k = 0; $k < 12; $k++){
                    $target = [
                        "slack" => $base_controller->generate_slack("targets"),
                        "store_id" => $active_store->id,
                        "month" => $target_month,
                        "income" => 99999,
                        "expense" => 99999,
                        "sales" => 99999,
                        "net_profit" => 99999,
                        "created_by" => 1
                    ];
                    
                    $target_id = TargetModel::create($target)->id;

                    $target_month = new Carbon($target_month);
                    $target_month->addMonths(1);
                    $target_month = date("Y-m-d", strtotime($target_month));
                    
                }

                $variant_option = [
                    "slack" => $base_controller->generate_slack("variant_options"),
                    "store_id" => $active_store->id,
                    "variant_option_code" => Str::random(6),
                    "label" => 'Size',
                    "status" => 1,
                    "created_by" => 1
                ];
                
                $variant_option_id = VariantOptionModel::withoutGlobalScopes()->create($variant_option)->id;
                
                $code_start_config = Config::get('constants.unique_code_start.variant_option');
                $code_start = (isset($code_start_config))?$code_start_config:100;
                
                $variant_option_code = [
                    "variant_option_code" => 'VO'.($code_start+$variant_option_id)
                ];
                VariantOptionModel::where('id', $variant_option_id)
                ->update($variant_option_code);

                $categories = [
                    "Veg Pizzas" => [
                        [
                            "item" => "Margherita - Regular",
                            "price" => 6.00,
                            "variants" => [
                                [
                                    "item" => "Margherita - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Margherita - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Double Cheese Margherita - Regular",
                            "price" => 10.00,
                            "variants" => [
                                [
                                    "item" => "Double Cheese Margherita - Medium",
                                    "price" => 12.00,
                                ],
                                [
                                    "item" => "Double Cheese Margherita - Large",
                                    "price" => 14.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Farm House - Regular",
                            "price" => 12.00,
                            "variants" => [
                                [
                                    "item" => "Farm House - Medium",
                                    "price" => 13.00,
                                ],
                                [
                                    "item" => "Farm House - Large",
                                    "price" => 14.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Peppy Paneer - Regular",
                            "price" => 8.25,
                            "variants" => [
                                [
                                    "item" => "Peppy Paneer - Medium",
                                    "price" => 9.00,
                                ],
                                [
                                    "item" => "Peppy Paneer - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Mexican Green Wave - Regular",
                            "price" => 14.00,
                            "variants" => [
                                [
                                    "item" => "Mexican Green Wave - Medium",
                                    "price" => 15.00,
                                ],
                                [
                                    "item" => "Mexican Green Wave - Large",
                                    "price" => 16.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Deluxe Veggie - Regular",
                            "price" => 8.00,
                            "variants" => [
                                [
                                    "item" => "Deluxe Veggie - Medium",
                                    "price" => 9.00,
                                ],
                                [
                                    "item" => "Deluxe Veggie - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Veg Extravaganza - Regular",
                            "price" => 6.00,
                            "variants" => [
                                [
                                    "item" => "Veg Extravaganza - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Veg Extravaganza - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Cheese N Corn - Regular",
                            "price" => 6.00,
                            "variants" => [
                                [
                                    "item" => "Cheese N Corn - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Cheese N Corn - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Veggie Paradise - Regular",
                            "price" => 8.50,
                            "variants" => [
                                [
                                    "item" => "Veggie Paradise - Medium",
                                    "price" => 9.00,
                                ],
                                [
                                    "item" => "Veggie Paradise - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Fresh Veggie - Regular",
                            "price" => 6.50,
                            "variants" => [
                                [
                                    "item" => "Fresh Veggie - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Fresh Veggie - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Indi Tandoori Paneer - Regular",
                            "price" => 7.00,
                            "variants" => [
                                [
                                    "item" => "Indi Tandoori Paneer - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Indi Tandoori Paneer - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                    ],
                    "Non Veg Pizzas" => [
                        [
                            "item" => "Pepper Barbecue Chicken - Regular",
                            "price" => 12.00,
                            "variants" => [
                                [
                                    "item" => "Pepper Barbecue Chicken - Medium",
                                    "price" => 8.00,
                                ],
                                [
                                    "item" => "Pepper Barbecue Chicken - Large",
                                    "price" => 10.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Chicken Sausage",
                            "price" => 14.25,
                            "variants" => [
                                [
                                    "item" => "Chicken Sausage - Medium",
                                    "price" => 15.00,
                                ],
                                [
                                    "item" => "Chicken Sausage - Large",
                                    "price" => 16.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Chicken Golden Delight",
                            "price" => 10.50,
                            "variants" => [
                                [
                                    "item" => "Chicken Golden Delight - Medium",
                                    "price" => 11.00,
                                ],
                                [
                                    "item" => "Chicken Golden Delight - Large",
                                    "price" => 12.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Non Veg Supreme",
                            "price" => 16.60,
                            "variants" => [
                                [
                                    "item" => "Non Veg Supreme - Medium",
                                    "price" => 18.00,
                                ],
                                [
                                    "item" => "Non Veg Supreme - Large",
                                    "price" => 19.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Chicken Dominator",
                            "price" => 11.00,
                            "variants" => [
                                [
                                    "item" => "Chicken Dominator - Medium",
                                    "price" => 12.00,
                                ],
                                [
                                    "item" => "Chicken Dominator - Large",
                                    "price" => 14.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Pepper Barbecue & Onion",
                            "price" => 18.00,
                            "variants" => [
                                [
                                    "item" => "Pepper Barbecue & Onion - Medium",
                                    "price" => 19.00,
                                ],
                                [
                                    "item" => "Pepper Barbecue & Onion - Large",
                                    "price" => 21.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Chicken Fiesta",
                            "price" => 19.00,
                            "variants" => [
                                [
                                    "item" => "Chicken Fiesta - Medium",
                                    "price" => 21.00,
                                ],
                                [
                                    "item" => "Chicken Fiesta - Large",
                                    "price" => 22.00,
                                ],
                            ]
                        ],
                        [
                            "item" => "Indi Chicken Tikka",
                            "price" => 20.00,
                            "variants" => [
                                [
                                    "item" => "Indi Chicken Tikka - Medium",
                                    "price" => 21.00,
                                ],
                                [
                                    "item" => "Indi Chicken Tikka - Large",
                                    "price" => 22.00,
                                ],
                            ]
                        ]
                    ],
                    "Burger Pizza" => [
                        [
                            "item" => "Burger Pizza - Classic Veg",
                            "price" => 8.00
                        ],
                        [
                            "item" => "Burger Pizza - Premium Veg",
                            "price" => 9.00
                        ],
                        [
                            "item" => "Burger Pizza - Classic Non Veg",
                            "price" => 11.00
                        ],
                    ],
                    "Side Orders" => [
                        [
                            "item" => "Garlic Breadsticks",
                            "price" => 2.00
                        ],
                        [
                            "item" => "Stuffed Garlic Bread",
                            "price" => 3.00
                        ],
                    ],
                    "Beverages" => [
                        [
                            "item" => "Lipton Ice Tea",
                            "price" => 4.00
                        ],
                        [
                            "item" => "Pepsi",
                            "price" => 5.00
                        ],
                        [
                            "item" => "Sprite",
                            "price" => 6.00
                        ],
                        [
                            "item" => "Coca Cola",
                            "price" => 6.50
                        ],
                    ],
                    "Pasta" => [
                        [
                            "item" => "Veg Pasta Italiano White",
                            "price" => 6.00
                        ],
                        [
                            "item" => "Tikka Masala Pasta Veg",
                            "price" => 5.50
                        ],
                        [
                            "item" => "Cheesy Jalapeno Pasta Veg",
                            "price" => 4.50
                        ],
                    ],
                    "Chicken" => [
                        [
                            "item" => "Roasted Chicken Wings Peri-Peri",
                            "price" => 18.00
                        ],
                        [
                            "item" => "Roasted Chicken Wings Classic Hot Sauce",
                            "price" => 16.00
                        ],
                        [
                            "item" => "Chicken Meatballs Peri-Peri",
                            "price" => 15.50
                        ],
                        [
                            "item" => "Boneless Chicken Wings Peri-Peri",
                            "price" => 16.00
                        ],
                    ],
                    "Ingredients" => [
                        [
                            "item" => "Cheese",
                            "price" => 2
                        ],
                        [
                            "item" => "Golden Corn",
                            "price" => 1
                        ],
                        [
                            "item" => "Paneer",
                            "price" => 1
                        ],
                        [
                            "item" => "Capsicum",
                            "price" => 1
                        ],
                        [
                            "item" => "Chicken",
                            "price" => 3
                        ],
                        [
                            "item" => "Goldern Corn",
                            "price" => 2
                        ],
                        [
                            "item" => "Black Olives",
                            "price" => 0.5
                        ],
                        [
                            "item" => "Capsicum",
                            "price" => 0.5
                        ],
                        [
                            "item" => "Lemon",
                            "price" => 0.5
                        ],
                        [
                            "item" => "Sugar",
                            "price" => 0.5
                        ],
                        [
                            "item" => "Red Paprika",
                            "price" => 0.5
                        ]
                    ],
                    "Add-ons" => [
                        [
                            "item" => "Regular Bun",
                            "price" => 3.05,
                            "addon" => 1
                        ],
                        [
                            "item" => "Whole Wheat Bun",
                            "price" => 3.25,
                            "addon" => 1
                        ],
                        [
                            "item" => "Regular Fries",
                            "price" => 2.05,
                            "addon" => 1
                        ],
                        [
                            "item" => "Cheese Slice",
                            "price" => 3.45,
                            "addon" => 1
                        ],
                        [
                            "item" => "Ketchup",
                            "price" => 5.05,
                            "addon" => 1
                        ],
                        [
                            "item" => "Coke",
                            "price" => 5.00,
                            "addon" => 1
                        ],
                        [
                            "item" => "Latte",
                            "price" => 6.00,
                            "addon" => 1
                        ],
                        [
                            "item" => "Cappuccino",
                            "price" => 5.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Sweet Potato Fries",
                            "price" => 1.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Lettuce",
                            "price" => 2.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Pickles",
                            "price" => 3.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Extra Cheese",
                            "price" => 3.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Jam",
                            "price" => 0.50,
                            "addon" => 1
                        ],
                        [
                            "item" => "Cheese",
                            "price" => 1.50,
                            "addon" => 1
                        ],
                    ]
                ];
                
                foreach($categories as $category_key => $category_item){
                    $category = [
                        "slack" => $base_controller->generate_slack("category"),
                        "store_id" => $active_store->id,
                        "category_code" => Str::random(6),
                        "label" => $category_key,
                        "description" => 'Sample Category',
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $category_id = CategoryModel::create($category)->id;
        
                    $code_start_config = Config::get('constants.unique_code_start.category');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $category_code = [
                        "category_code" => "CAT".($code_start+$category_id)
                    ];
                    CategoryModel::withoutGlobalScopes()->where('id', $category_id)
                    ->update($category_code);

                    $supplier = SupplierModel::withoutGlobalScopes()->select("id")->where('store_id', $active_store->id)->first();
                    $taxcode = TaxcodeModel::withoutGlobalScopes()->select("id")->where('store_id', $active_store->id)->first();
                    $discountcode = DiscountcodeModel::withoutGlobalScopes()->select("id")->where('store_id', $active_store->id)->first();

                    $taxcode_zero = TaxcodeModel::withoutGlobalScopes()->select("id")->where([
                        ['store_id', '=', $active_store->id],
                        ['tax_code', '=', 'TAX0'],
                    ])->first();

                    foreach($category_item as $category_item_data){
                        $product = [
                            "slack" => $base_controller->generate_slack("products"),
                            "store_id" => $active_store->id,
                            "name" => ucwords($category_item_data['item']),
                            "product_code" => strtoupper(Str::random(4)),
                            "description" => '',
                            "category_id" => $category_id,
                            "supplier_id" => (isset($supplier))?$supplier->id:NULL,
                            "tax_code_id" => ($category_key == 'Ingredients')?$taxcode_zero->id:((isset($taxcode))?$taxcode->id:NULL),
                            "discount_code_id" => (isset($discountcode))?$discountcode->id:NULL,
                            "quantity" => 99999,
                            "purchase_amount_excluding_tax" => abs($category_item_data['price']-1),
                            "sale_amount_excluding_tax" => $category_item_data['price'],
                            "is_ingredient" => ($category_key == 'Ingredients')?1:0,
                            "is_addon_product" => (isset($category_item_data['addon']) && $category_item_data['addon'] == 1)?1:0,
                            "status" => 1,
                            "created_by" => 1
                        ];
                        
                        $product_id = ProductModel::withoutGlobalScopes()->create($product)->id;

                        $product_image_array = [
                            "slack" => $base_controller->generate_slack("product_images"),
                            "product_id" => $product_id,
                            "filename" => 'placeholder_image.png',
                            "status" => 1,
                            "created_by" => 1
                        ];
                        
                        $product_image_id = ProductImagesModel::create($product_image_array)->id;

                        $product_api = new ProductAPI();
                        $product_variant_code = $product_api->generate_variant_code();

                        $variant_product_array = [
                            "slack"             => $base_controller->generate_slack("product_variants"),
                            "variant_code"      => $product_variant_code,
                            "product_id"        => $product_id,
                            "variant_option_id" => $variant_option_id,
                            "created_by"        => 1
                        ];

                        ProductVariantModel::withoutGlobalScopes()->create($variant_product_array);

                        if(isset($category_item_data['variants'])){
                            foreach($category_item_data['variants'] as $variant){
                                $product = [
                                    "slack" => $base_controller->generate_slack("products"),
                                    "store_id" => $active_store->id,
                                    "name" => ucwords($variant['item']),
                                    "product_code" => strtoupper(Str::random(4)),
                                    "description" => '',
                                    "category_id" => $category_id,
                                    "supplier_id" => (isset($supplier))?$supplier->id:NULL,
                                    "tax_code_id" => ($category_key == 'Ingredients')?$taxcode_zero->id:((isset($taxcode))?$taxcode->id:NULL),
                                    "discount_code_id" => (isset($discountcode))?$discountcode->id:NULL,
                                    "quantity" => 99999,
                                    "purchase_amount_excluding_tax" => abs($variant['price']-1),
                                    "sale_amount_excluding_tax" => $variant['price'],
                                    "is_ingredient" => ($category_key == 'Ingredients')?1:0,
                                    "is_addon_product" => (isset($category_item_data['addon']) && $category_item_data['addon'] == 1)?1:0,
                                    "status" => 1,
                                    "created_by" => 1
                                ];
                                
                                $product_id = ProductModel::withoutGlobalScopes()->create($product)->id;
        
                                $product_image_array = [
                                    "slack" => $base_controller->generate_slack("product_images"),
                                    "product_id" => $product_id,
                                    "filename" => 'placeholder_image.png',
                                    "status" => 1,
                                    "created_by" => 1
                                ];
                                
                                $product_image_id = ProductImagesModel::create($product_image_array)->id;

                                $variant_product_array = [
                                    "slack"             => $base_controller->generate_slack("product_variants"),
                                    "variant_code"      => $product_variant_code,
                                    "product_id"        => $product_id,
                                    "variant_option_id" => $variant_option_id,
                                    "created_by"        => 1
                                ];

                                ProductVariantModel::withoutGlobalScopes()->create($variant_product_array);   

                            }
                        }
                    }
                }

                $addon_group_array = [
                    'Extra Fillings', 'Extra Add-on', 'Choose Your Add-on'
                ];

                $addon_products = ProductModel::withoutGlobalScopes()->where('store_id', $active_store->id)->where('is_addon_product', 1)->pluck('id')->chunk(3);

                foreach($addon_group_array as $key => $addon_group_array_item){
                    $addon_group = [
                        "slack" => $base_controller->generate_slack("addon_groups"),
                        "store_id" => $active_store->id,
                        "addon_group_code" => strtoupper(Str::random(6)),
                        "label" => $addon_group_array_item,
                        "multiple_selection" => 1,
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $addon_group_id = AddonGroupModel::create($addon_group)->id;
        
                    $code_start_config = Config::get('constants.unique_code_start.addon_group');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $addon_group_code = [
                        "addon_group_code" => 'AOG'.($code_start+$addon_group_id)
                    ];
                    AddonGroupModel::withoutGlobalScopes()->where('id', $addon_group_id)
                    ->update($addon_group_code);

                    $addon_product_data_array = [];
                    foreach($addon_products[$key] as $ckey => $addon_product_chunk){
                        $addon_product_data_array[] = [
                            "addon_group_id" => $addon_group_id,
                            "product_id" => $addon_product_chunk,
                            "created_by" => 1
                        ];
                    }

                    if(!empty($addon_product_data_array) && count($addon_product_data_array)>0){
                        foreach($addon_product_data_array as $addon_product_data_array_item){
                            AddonGroupProductModel::create($addon_product_data_array_item);
                        }
                    }
                }

                $addon_groups = AddonGroupModel::withoutGlobalScopes()->where('store_id', $active_store->id)->active()->get();

                $billing_products = ProductModel::withoutGlobalScopes()->where('store_id', $active_store->id)->mainProduct()->pluck('id');

                foreach($billing_products as $billing_product_item){

                    $product_addon_group_array = [];
                    if(!empty($addon_groups)){
                        
                        foreach($addon_groups as $key => $addon_group_item){
                            $product_addon_group_array[] = [
                                "product_id" => $billing_product_item,
                                "addon_group_id" => $addon_group_item->id,
                                "created_by" => 1
                            ];
                        }
                    }
                    if(!empty($product_addon_group_array) && count($product_addon_group_array)>0){
                        foreach($product_addon_group_array as $product_addon_group_array_item){
                            ProductAddonGroupModel::create($product_addon_group_array_item);
                        }
                    }
                }
                
                for($k=1; $k<=15; $k++){ 
                    $table = [
                        "slack" => $base_controller->generate_slack("restaurant_tables"),
                        "store_id" => $active_store->id,
                        "table_number" => 'Table '.$k,
                        "no_of_occupants" => 5,
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $table_id = TableModel::create($table)->id;
                }

                for($j = 1; $j <= 6; $j++){
                    $billing_counter = [
                        "slack" => $base_controller->generate_slack("billing_counters"),
                        "store_id" => $active_store->id,
                        "billing_counter_code" => "C".$j,
                        "counter_name" => "Bill Counter ".$j,
                        "description" => '',
                        "status" => 1,
                        "created_by" => 1
                    ];
                    
                    $billing_counter_id = BillingCounterModel::create($billing_counter)->id;
                }
                
            }
        }
        
        DB::insert("INSERT INTO `orders` (`id`, `slack`, `store_id`, `order_number`, `customer_id`, `customer_name`, `customer_phone`, `customer_email`, `contact_number`, `address`, `register_id`, `store_level_discount_code_id`, `store_level_discount_code`, `store_level_total_discount_percentage`, `store_level_total_discount_amount`, `product_level_total_discount_amount`, `store_level_tax_code_id`, `store_level_tax_code`, `store_level_total_tax_percentage`, `store_level_total_tax_amount`, `store_level_total_tax_components`, `product_level_total_tax_amount`, `purchase_amount_subtotal_excluding_tax`, `sale_amount_subtotal_excluding_tax`, `total_discount_before_additional_discount`, `total_amount_before_additional_discount`, `additional_discount_percentage`, `additional_discount_amount`, `total_discount_amount`, `total_after_discount`, `total_tax_amount`, `total_order_amount`, `total_order_amount_rounded`, `payment_method_id`, `payment_method_slack`, `payment_method`, `currency_name`, `currency_code`, `business_account_id`, `order_type_id`, `order_type`, `restaurant_mode`, `bill_type_id`, `bill_type`, `table_id`, `table_number`, `waiter_id`, `order_origin`, `status`, `kitchen_status`, `payment_status`, `order_merged`, `order_merge_parent_id`, `kitchen_screen_dismissed`, `waiter_screen_dismissed`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, '00cnuBLCbKPbVMImkHF5UZpKW', 1, '101', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '3.38', NULL, NULL, '0.00', '0.00', NULL, '0.00', '63.50', '67.50', '3.38', '64.13', '0.00', '0.00', '3.38', '64.13', '0.00', '64.13', '64', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."'),
        (2, 'lPVrzv3s7Nh0ixMW5TFKAlIJ8', 1, '102', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.25', NULL, NULL, '0.00', '0.00', NULL, '0.00', '3.00', '5.00', '0.25', '4.75', '0.00', '0.00', '0.25', '4.75', '0.00', '4.75', '5', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 3, 'Delivery', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."'),
        (3, 'tDJrL3LZlY5ec0LKOKsLctBmG', 1, '103', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.50', NULL, NULL, '0.00', '0.00', NULL, '0.00', '8.00', '10.00', '0.50', '9.50', '0.00', '0.00', '0.50', '9.50', '0.00', '9.50', '10', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 1, 'Dine In', 1, 2, 'Quick Bill', 1, 'Table 1', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(2))->format('Y-m-d H:i:s')."'),
        (4, 'Ikhbn6VM4K9Om5Kig6mFugbV0', 1, '104', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.45', NULL, NULL, '0.00', '0.00', NULL, '0.00', '26.00', '29.00', '1.45', '27.55', '0.00', '0.00', '1.45', '27.55', '0.00', '27.55', '28', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (5, 'NrZrpUI4HVfnjvPcpdNynJl2Y', 1, '105', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.90', NULL, NULL, '0.00', '0.00', NULL, '0.00', '16.00', '18.00', '0.90', '17.10', '0.00', '0.00', '0.90', '17.10', '0.00', '17.10', '17', 0, '', '', 'United States dollar', 'USD', 0, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 2, NULL, 0, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (6, 'vZxTrXdYCWPjvtgt08MpxWICM', 1, '106', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.53', NULL, NULL, '0.00', '0.00', NULL, '0.00', '8.50', '10.50', '0.53', '9.98', '0.00', '0.00', '0.53', '9.98', '0.00', '9.98', '10', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (7, '9alYREBIkm2LTN5W2odR31FC1', 1, '107', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.48', NULL, NULL, '0.00', '0.00', NULL, '0.00', '25.50', '29.50', '1.48', '28.03', '0.00', '0.00', '1.48', '28.03', '0.00', '28.03', '28', 0, '', '', 'United States dollar', 'USD', 0, 3, 'Delivery', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 2, NULL, 0, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (8, 'TjwHSuG6Q7mBfINoHho2N3puM', 1, '108', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.48', NULL, NULL, '0.00', '0.00', NULL, '0.00', '26.50', '29.50', '1.48', '28.03', '0.00', '0.00', '1.48', '28.03', '0.00', '28.03', '28', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (9, 'R587qc4fCDB0ZCQvoqxMkJVhi', 1, '109', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.35', NULL, NULL, '0.00', '0.00', NULL, '0.00', '23.00', '27.00', '1.35', '25.65', '0.00', '0.00', '1.35', '25.65', '0.00', '25.65', '26', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (10, 'BalFkxiP5VqL29uueTIopae2m', 1, '110', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.93', NULL, NULL, '0.00', '0.00', NULL, '0.00', '16.50', '18.50', '0.93', '17.58', '0.00', '0.00', '0.93', '17.58', '0.00', '17.58', '18', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (11, 'uZfxf6TJhbcLr2IMIHfHhVyGO', 1, '111', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.80', NULL, NULL, '0.00', '0.00', NULL, '0.00', '33.00', '36.00', '1.80', '34.20', '0.00', '0.00', '1.80', '34.20', '0.00', '34.20', '34', 5, 'WGvhRAQHLCd4hdeN7g29Tk83X', 'Card', 'United States dollar', 'USD', 1, 3, 'Delivery', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (12, 'y1lpItkbHWxptOJ4SPTeOGM0j', 1, '112', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.93', NULL, NULL, '0.00', '0.00', NULL, '0.00', '16.50', '18.50', '0.93', '17.58', '0.00', '0.00', '0.93', '17.58', '0.00', '17.58', '18', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 1, 'Dine In', 1, 2, 'Quick Bill', 1, 'Table 1', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (13, 'YuTopb9hUXLLQZESle47xDpMz', 1, '113', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '955.00', NULL, NULL, '0.00', '0.00', NULL, '0.00', '16900.00', '19100.00', '955.00', '18145.00', '0.00', '0.00', '955.00', '18145.00', '0.00', '18145.00', '18145', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 1, 'Dine In', 1, 2, 'Quick Bill', 1, 'Table 1', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (14, 'YvfF9y0LgAvUZZZHpbvK4hNuc', 1, '114', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '7915.00', NULL, NULL, '0.00', '0.00', NULL, '0.00', '137300.00', '158300.00', '7915.00', '150385.00', '0.00', '0.00', '7915.00', '150385.00', '0.00', '150385.00', '150385', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (15, 'ov5PgF5wfMtcUHaQnuEpDK3Sq', 1, '115', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.35', NULL, NULL, '0.00', '0.00', NULL, '0.00', '25.00', '27.00', '1.35', '25.65', '0.00', '0.00', '1.35', '25.65', '0.00', '25.65', '26', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (16, '7nhkwUWNhli4etno9En0SMLwL', 1, '116', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.78', NULL, NULL, '0.00', '0.00', NULL, '0.00', '14.50', '15.50', '0.78', '14.73', '0.00', '0.00', '0.78', '14.73', '0.00', '14.73', '15', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (17, 'I1jzFGUFwCiuLG3BOQwbiKQml', 1, '117', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '0.93', NULL, NULL, '0.00', '0.00', NULL, '0.00', '16.50', '18.50', '0.93', '17.58', '0.00', '0.00', '0.93', '17.58', '0.00', '17.58', '18', 4, 'ogENSixT8WlprtTSmvFK1Gv2m', 'Cash', 'United States dollar', 'USD', 1, 1, 'Dine In', 1, 2, 'Quick Bill', 1, 'Table 1', 0, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW()),
        (18, 'yQ1cggPmH0QHO2cC2Kd9lEy5h', 1, '118', 1, 'Walkin Customer', '0000000000', 'walkincustomer@appsthing.com', NULL, NULL, 1, NULL, NULL, '0.00', '0.00', '1.35', NULL, NULL, '0.00', '0.00', NULL, '0.00', '25.00', '27.00', '1.35', '25.65', '0.00', '0.00', '1.35', '25.65', '0.00', '25.65', '26', 5, 'xzL98rYbyfhs6oHxJgAzcgk5m', 'Card', 'United States dollar', 'USD', 1, 2, 'Take Away', 1, 2, 'Quick Bill', 0, '', 6, 'POS_WEB', 1, 0, 1, 0, NULL, 0, 0, 1, NULL, NOW(), NOW())");

        DB::insert("INSERT INTO `order_products` (`id`, `slack`, `order_id`, `parent_order_product_id`, `product_id`, `product_slack`, `product_code`, `name`, `quantity`, `purchase_amount_excluding_tax`, `sale_amount_excluding_tax`, `discount_code_id`, `discount_code`, `discount_percentage`, `tax_code_id`, `tax_code`, `tax_percentage`, `tax_components`, `sub_total_purchase_price_excluding_tax`, `sub_total_sale_price_excluding_tax`, `discount_amount`, `total_after_discount`, `tax_amount`, `total_amount`, `is_ready_to_serve`, `merged_from`, `merged_to`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'zuYRGspbbcuXBXoibcnRqEHlg', 1, NULL, 73, 'hqc0NZuzlsAWjbob1ZZdskjMB', '0UEZ', 'Boneless Chicken Wings Peri-Peri', '1.00', '15.00', '16.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '15.00', '16.00', '0.80', '15.20', '0.00', '15.20', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (2, '9QcJrswfxkAWMUk4E2hwGugUU', 1, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (3, 'scFYFPO7oeHZqV2Pm00B1SQ10', 1, NULL, 26, 'CYZiXGpbJslFlKEj2LwZkbpYk', 'CDAV', 'Veggie Paradise - Medium', '2.00', '17.00', '18.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '34.00', '36.00', '1.80', '34.20', '0.00', '34.20', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (4, 'AaYoyncOitziR4BaYnUpCQc5r', 2, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (5, 'l6mokaX5l9Dy3PT1D7LqYHj7f', 2, NULL, 14, 'qNM1bl4TQrJVFdeMTNwA2H6QG', 'IF0Y', 'Mexican Green Wave - Medium', '1.00', '1.00', '2.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '1.00', '2.00', '0.10', '1.90', '0.00', '1.90', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (6, '3e85lKTswnhTo192KaPsIOt8G', 3, NULL, 65, 'TCD0AGzQ4AWoiP5AMEqUmq1Ou', 'CHGN', 'Sprite', '1.00', '5.00', '6.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '5.00', '6.00', '0.30', '5.70', '0.00', '5.70', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (7, 'hMyHzcaJ96UQ9Km0JZALHtLl3', 3, NULL, 63, 'lwqlF56pE8xTVghBaE5zxFGg5', 'CW0J', 'Lipton Ice Tea', '1.00', '3.00', '4.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '3.00', '4.00', '0.20', '3.80', '0.00', '3.80', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (8, 'XDGkYSUVE5gYBvqTFcpGVZx5D', 4, NULL, 20, 'sb085GSgKbag6zTI9XEOo09Nt', 'KG78', 'Veg Extravaganza - Medium', '2.00', '8.00', '9.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '16.00', '18.00', '0.90', '17.10', '0.00', '17.10', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (9, 'UvIv4MeXb83sxWDABUgeVRMpH', 4, NULL, 60, 'VaA1SJpWgFap1sEhq1Kut6Bu0', 'WSQ8', 'Burger Pizza - Classic Non Veg', '1.00', '10.00', '11.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '10.00', '11.00', '0.55', '10.45', '0.00', '10.45', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (10, 'ZlLrHFgFY6YowwA1OfZAWS5Hj', 5, NULL, 73, 'hqc0NZuzlsAWjbob1ZZdskjMB', '0UEZ', 'Boneless Chicken Wings Peri-Peri', '1.00', '15.00', '16.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '15.00', '16.00', '0.80', '15.20', '0.00', '15.20', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (11, '7ufh5v5LCynbt8FHT9VbHSQxn', 5, NULL, 14, 'qNM1bl4TQrJVFdeMTNwA2H6QG', 'IF0Y', 'Mexican Green Wave - Medium', '1.00', '1.00', '2.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '1.00', '2.00', '0.10', '1.90', '0.00', '1.90', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (12, 'awvOTmdVUi92upSEP4MKYZLLX', 6, NULL, 67, 'C8lmDSlUYM317DJxnwgQlsp7e', '2O8E', 'Veg Pasta Italiano White', '1.00', '5.00', '6.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '5.00', '6.00', '0.30', '5.70', '0.00', '5.70', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (13, 'tnjEq6wOC7zH15fmGMHbUnZqC', 6, NULL, 69, 'J68wOuOyfkmn2MUuzo0eS9snu', '3E7O', 'Cheesy Jalapeno Pasta Veg', '1.00', '3.50', '4.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '3.50', '4.50', '0.23', '4.28', '0.00', '4.28', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (14, 'brBLms8yDSpugdpij357Hw8IJ', 7, NULL, 72, 'L6b14CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (15, 'uABXlQRBVFwbnm14alOnINB8q', 7, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (16, 'OK0pQt1GGIetDEfkCZd9ZNN8o', 7, NULL, 14, 'qNM1bl4TQrJVFdeMTNwA2H6QG', 'IF0Y', 'Mexican Green Wave - Medium', '1.00', '1.00', '2.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '1.00', '2.00', '0.10', '1.90', '0.00', '1.90', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (17, '0MEE7vSQMpDZ5DP7lAtjgX1wQ', 7, NULL, 20, 'sb085GSgKbag6zTI9XEOo09Nt', 'KG78', 'Veg Extravaganza - Medium', '1.00', '8.00', '9.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '8.00', '9.00', '0.45', '8.55', '0.00', '8.55', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (18, 'hQrfzlxH3BykOx2YtBWRw4JmR', 8, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (19, 'ZQW0L6EwIOJAd3l0I6Nz7sNYV', 8, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (20, 'pFqzJkFxYxdUkNV89w8FRtKuC', 8, NULL, 60, 'VaA1SJpWgFap1sEhq1Kut6Bu0', 'WSQ8', 'Burger Pizza - Classic Non Veg', '1.00', '10.00', '11.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '10.00', '11.00', '0.55', '10.45', '0.00', '10.45', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (21, 'gPw1BAwUNRieEcCqmPUl9Kc6m', 9, NULL, 60, 'VaA1SJpWgFap1sEhq1Kut6Bu0', 'WSQ8', 'Burger Pizza - Classic Non Veg', '1.00', '10.00', '11.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '10.00', '11.00', '0.55', '10.45', '0.00', '10.45', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (22, 'q5eVJPaEKIOR7kp1EbOvU2tzV', 9, NULL, 67, 'C8lmDSlUYM317DJxnwgQlsp7e', '2O8E', 'Veg Pasta Italiano White', '1.00', '5.00', '6.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '5.00', '6.00', '0.30', '5.70', '0.00', '5.70', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (23, 'dAHWXT9GtTdbCQr14SK98kt0e', 9, NULL, 63, 'lwqlF56pE8xTVghBaE5zxFGg5', 'CW0J', 'Lipton Ice Tea', '1.00', '3.00', '4.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '3.00', '4.00', '0.20', '3.80', '0.00', '3.80', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (24, 'A3GVpCPqF4cS5GxAe7szlynX0', 9, NULL, 65, 'TCD0AGzQ4AWoiP5AMEqUmq1Ou', 'CHGN', 'Sprite', '1.00', '5.00', '6.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '5.00', '6.00', '0.30', '5.70', '0.00', '5.70', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (25, 'voZNPSXvLsElOnsJuEhAhcPNc', 10, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (26, 'SHPwRAjtGTCowPYagBE14HOgn', 10, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (27, 'fgDnXGn56Fkp1VbwNBEBl4se5', 11, NULL, 26, 'CYZiXGpbJslFlKEj2LwZkbpYk', 'CDAV', 'Veggie Paradise - Medium', '1.00', '17.00', '18.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '17.00', '18.00', '0.90', '17.10', '0.00', '17.10', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (28, 'BFI4CfDYbMoMM8XjNi0vs6Emf', 11, NULL, 73, 'hqc0NZuzlsAWjbob1ZZdskjMB', '0UEZ', 'Boneless Chicken Wings Peri-Peri', '1.00', '15.00', '16.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '15.00', '16.00', '0.80', '15.20', '0.00', '15.20', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (29, 'F7L6zLEY7YMH2c5KKfVJs8Lfq', 11, NULL, 14, 'qNM1bl4TQrJVFdeMTNwA2H6QG', 'IF0Y', 'Mexican Green Wave - Medium', '1.00', '1.00', '2.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '1.00', '2.00', '0.10', '1.90', '0.00', '1.90', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (30, 'mxXMZYZs9IbLxVD732GwlmeeH', 12, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (31, 'BgqaqYzyUyEFwO5Apbii9KMjY', 12, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (32, 'ZNI87GqHtIjun5Sh3Y7BoZoi2', 13, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1000.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14500.00', '15500.00', '775.00', '14725.00', '0.00', '14725.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (33, 'KhrKHGJbWUBsINVxsHW28qjOH', 13, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1200.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2400.00', '3600.00', '180.00', '3420.00', '0.00', '3420.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (34, 'EVNfbjHC9mxDKS8WnlQDdHZZx', 14, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '200.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2900.00', '3100.00', '155.00', '2945.00', '0.00', '2945.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (35, 'URoTFtu568X6Ja6fMTP5CL0VG', 14, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1100.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2200.00', '3300.00', '165.00', '3135.00', '0.00', '3135.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (36, 'J2mp9Sq9ivjUQzSStz0pGMaVb', 14, NULL, 14, 'qNM1bl4TQrJVFdeMTNwA2H6QG', 'IF0Y', 'Mexican Green Wave - Medium', '200.00', '1.00', '2.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '200.00', '400.00', '20.00', '380.00', '0.00', '380.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (37, 'MJbjYUyByddrM6kF0o1PyjcVY', 14, NULL, 20, 'sb085GSgKbag6zTI9XEOo09Nt', 'KG78', 'Veg Extravaganza - Medium', '9000.00', '8.00', '9.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '72000.00', '81000.00', '4050.00', '76950.00', '0.00', '76950.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (38, 'RnMHsKDwCd76jiQOPDiUWUUUV', 14, NULL, 60, 'VaA1SJpWgFap1sEhq1Kut6Bu0', 'WSQ8', 'Burger Pizza - Classic Non Veg', '1500.00', '10.00', '11.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '15000.00', '16500.00', '825.00', '15675.00', '0.00', '15675.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (39, 'PdxHxJvyUbSAWXj2JcXKFC21v', 14, NULL, 65, 'TCD0AGzQ4AWoiP5AMEqUmq1Ou', 'CHGN', 'Sprite', '9000.00', '5.00', '6.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '45000.00', '54000.00', '2700.00', '51300.00', '0.00', '51300.00', 0, NULL, NULL, 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (40, 'Yx4UkoaPFquIqIBTnmSPzrF4T', 15, NULL, 26, 'CYZiXGpbJslFlKEj2LwZkbpYk', 'CDAV', 'Veggie Paradise - Medium', '1.00', '17.00', '18.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '17.00', '18.00', '0.90', '17.10', '0.00', '17.10', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (41, 'a3p9n9kzFh1cGHgYCbLJwYUKJ', 15, NULL, 20, 'sb085GSgKbag6zTI9XEOo09Nt', 'KG78', 'Veg Extravaganza - Medium', '1.00', '8.00', '9.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '8.00', '9.00', '0.45', '8.55', '0.00', '8.55', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (42, 'c2kw3g4w4BNfHWoDTftWZFBQd', 16, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (43, 'oAJ3KaNG3QDz6QWZ5dCmVaIOo', 17, NULL, 72, 'L6b61CqvhubSG4KIj2yheRv0h', 'BX29', 'Chicken Meatballs Peri-Peri', '1.00', '14.50', '15.50', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '14.50', '15.50', '0.78', '14.73', '0.00', '14.73', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (44, 'Ag7ezh81bbSG7xiSnl6vZ34yR', 17, NULL, 11, 'v6Q8hb0KYob95s5AO8iwhA9ny', '0RDX', 'Peppy Paneer - Regular', '1.00', '2.00', '3.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '2.00', '3.00', '0.15', '2.85', '0.00', '2.85', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (46, 'fDO7gmn9p4n8AfloMWsSCwRPG', 18, NULL, 20, '1jxIjI1qxAlgJ8OizkOdT0oLM', 'LXMQ', 'Veg Extravaganza - Medium', '1.00', '8.00', '9.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '8.00', '9.00', '0.45', '8.55', '0.00', '8.55', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW()),
        (45, 'schtG6zEONXBiCiBEnFfYwmRZ', 18, NULL, 26, 'tKpIRraJ8yp9q9NGANMNBW21w', 'GXAO', 'Veggie Paradise - Medium', '1.00', '17.00', '18.00', 1, 'DISCOUNT5', '5.00', 6, 'TAX0', '0.00', NULL, '17.00', '18.00', '0.90', '17.10', '0.00', '17.10', 0, NULL, NULL, 1, 1, NULL, NOW(), NOW())");

        DB::insert("INSERT INTO `invoices` (`id`, `slack`, `store_id`, `invoice_number`, `invoice_reference`, `invoice_date`, `invoice_due_date`, `parent_po_id`, `bill_to`, `bill_to_id`, `bill_to_code`, `bill_to_name`, `bill_to_email`, `bill_to_contact`, `bill_to_address`, `currency_name`, `currency_code`, `tax_option_id`, `subtotal_excluding_tax`, `total_discount_amount`, `total_after_discount`, `total_tax_amount`, `shipping_charge`, `packing_charge`, `total_order_amount`, `notes`, `terms`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'cslw3HlEEuzM7Qfs0Vn6zGOeC', 1, '101', '5445454', '2021-11-01', '2021-11-22', NULL, 'SUPPLIER', 1, 'SUP101', 'Food Mart Co. Ltd.', 'bergnaum.jordyn@yahoo.com', '+5452498135827', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'United States dollar', 'USD', 1, '2897.50', '144.88', '2752.63', '0.00', '10.00', '20.00', '2782.63', NULL, 'sample invoice terms', 4, 1, 1, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (2, 'fPZEXkkzATVjE58RHvjXaPsLt', 1, '102', '6776456865', '2021-11-22', '2022-01-31', NULL, 'SUPPLIER', 1, 'SUP101', 'Food Mart Co. Ltd.', 'bergnaum.jordyn@yahoo.com', '+5452498135827', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'United States dollar', 'USD', 0, '100025.00', '5001.25', '95023.75', '0.00', '0.00', '0.00', '95023.75', NULL, NULL, 3, 1, 1, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (3, 'fnQWFar5NOhpZ5h9SbG2wh2RX', 1, '103', '6564545', '2021-11-01', '2021-11-30', NULL, 'CUSTOMER', 7, '', 'Chance Fahey', 'zluettgen@yahoo.com', '+9435467370627', '7357 Torp Springs\nMitchellborough, ND 68800', 'United States dollar', 'USD', 0, '25.25', '1.26', '23.99', '0.00', '0.00', '0.00', '23.99', NULL, NULL, 5, 1, 1, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NOW()),
        (4, 's0cpbbMOy2yZHZVMSTog1qD17', 1, '104', '9876789', '2021-11-01', '2021-11-30', NULL, 'CUSTOMER', 14, '', 'Mabel Reilly', 'aliyah99@kris.com', '+2355010416313', '67013 German Shore\nRickymouth, TX 16711', 'United States dollar', 'USD', 1, '2307.50', '115.38', '2192.13', '0.00', '0.00', '0.00', '2192.13', NULL, 'Sample terms', 4, 1, 1, NOW(), NOW()),
        (5, 'xRKwCSRB9a4AmjnfOrc4tLRLi', 1, '105', '98767894', '2020-11-01', '2022-10-31', NULL, 'SUPPLIER', 1, 'SUP101', 'Food Mart Co. Ltd.', 'bergnaum.jordyn@yahoo.com', '+5452498135827', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'United States dollar', 'USD', 1, '12.75', '0.64', '12.11', '0.00', '0.00', '0.00', '12.11', NULL, 'Terms', 1, 1, NULL, NOW(), NOW())");

        DB::insert("INSERT INTO `invoice_products` (`id`, `slack`, `invoice_id`, `product_id`, `product_slack`, `product_code`, `name`, `quantity`, `amount_excluding_tax`, `subtotal_amount_excluding_tax`, `discount_percentage`, `tax_percentage`, `discount_amount`, `total_after_discount`, `tax_amount`, `tax_components`, `total_amount`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'ZmyzC2Acc19tyVKyRIg9ykUfr', 1, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (2, 'aeYlXyB2objrBV7IjdVhXPDep', 1, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '150.00', '7.25', '1087.50', '5.00', '0.00', '54.38', '1033.13', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '1033.13', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (3, '6syE9Qpp7cbo8wAtmjogWIuS2', 1, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '100.00', '9.00', '900.00', '5.00', '0.00', '45.00', '855.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '855.00', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (4, 'fmsx36m5bYeqpPDBQ8dIki6hz', 1, 8, 'AkimSOp0Q9oFyq4agWGYeNauU', 'QVUR', 'Farm House - Medium', '30.00', '12.00', '360.00', '5.00', '0.00', '18.00', '342.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '342.00', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (5, 'IS4hV9VrRLtZZBkAzRh54RHNo', 2, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '100.00', '7.25', '725.00', '5.00', '0.00', '36.25', '688.75', '0.00', '', '688.75', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (6, '3LWrxKyEPwY0ujZ2VTAZhyjnh', 2, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '1200.00', '9.00', '10800.00', '5.00', '0.00', '540.00', '10260.00', '0.00', '', '10260.00', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (7, 'ZgENQr5EwQY2XlS1Wq0jkkWJb', 2, 7, 'Ky9OkN7guize5tTQNKYUONEAE', 'QRJ7', 'Farm House - Regular', '1500.00', '11.00', '16500.00', '5.00', '0.00', '825.00', '15675.00', '0.00', '', '15675.00', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (8, 'ZB4HovRrtUJ7CuYhYHzd9vOY8', 2, 8, 'AkimSOp0Q9oFyq4agWGYeNauU', 'QVUR', 'Farm House - Medium', '6000.00', '12.00', '72000.00', '5.00', '0.00', '3600.00', '68400.00', '0.00', '', '68400.00', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (9, 'kErdQ35fPn3xYPhXWZZJQSQV3', 3, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '1.00', '7.25', '7.25', '5.00', '0.00', '0.36', '6.89', '0.00', '', '6.89', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (10, 'FLukvbPcMkrdgF1ZFDHdaB4m9', 3, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '2.00', '9.00', '18.00', '5.00', '0.00', '0.90', '17.10', '0.00', '', '17.10', 1, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', NULL),
        (11, 'a2arMEjPCgvNfmWq9avodNTpc', 4, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 1, 1, NULL, NOW(), NULL),
        (12, '35F4ZjKrRzYFFRevK7THKmjwm', 4, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '150.00', '7.25', '1087.50', '5.00', '0.00', '54.38', '1033.13', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '1033.13', 1, 1, NULL, NOW(), NULL),
        (13, 'EXaN3Otct8ynauqMjV92e6107', 4, 5, 'g6kFjjPprKDKVdtlmVNJXCX7E', 'OVQM', 'Double Cheese Margherita - Medium', '50.00', '11.00', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 1, 1, NULL, NOW(), NULL),
        (14, 'OcN6SOMTcxGrojCxrYpwezktK', 4, 8, 'AkimSOp0Q9oFyq4agWGYeNauU', 'QVUR', 'Farm House - Medium', '10.00', '12.00', '120.00', '5.00', '0.00', '6.00', '114.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '114.00', 1, 1, NULL, NOW(), NULL),
        (15, 'C4jQTDFtsQBVYvImngOrDiQNp', 5, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '1.00', '5.50', '5.50', '5.00', '0.00', '0.28', '5.23', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '5.23', 1, 1, NULL, NOW(), NULL),
        (16, 'Dfj2v5okx87LDWjXguGChWjY1', 5, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '1.00', '7.25', '7.25', '5.00', '0.00', '0.36', '6.89', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '6.89', 1, 1, NULL, NOW(), NULL)");

        DB::insert("INSERT INTO `purchase_orders` (`id`, `slack`, `store_id`, `po_number`, `po_reference`, `order_date`, `order_due_date`, `supplier_id`, `supplier_code`, `supplier_name`, `supplier_address`, `currency_name`, `currency_code`, `tax_option_id`, `subtotal_excluding_tax`, `total_discount_amount`, `total_after_discount`, `total_tax_amount`, `shipping_charge`, `packing_charge`, `total_order_amount`, `terms`, `update_stock`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'Cb00WGilqXnj1Oitpz7naVreD', 1, '123432', '23456', '2021-11-22', '2021-11-30', 1, 'SUP101', 'Food Mart Co. Ltd.', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164, 11111', 'United States dollar', 'USD', 1, '948.75', '47.44', '901.31', '0.00', '10.00', '20.00', '931.31', 'Sample PO', 1, 1, 1, NULL, NOW(), NOW()),
        (2, '0RwqBtMKfem5fLVcEvvKE834l', 1, '4534554', '343434', '2021-11-01', '2022-01-31', 1, 'SUP101', 'Food Mart Co. Ltd.', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164, 11111', 'United States dollar', 'USD', 1, '9250.00', '462.50', '8787.50', '0.00', '0.00', '0.00', '8787.50', 'Sample PO', 0, 1, 1, NULL, NOW(), NOW()),
        (3, 'x6ZP0yhaXDR7FHaEMJftUxwIx', 1, '78788978', '324343434', '2019-11-01', '2021-11-30', 1, 'SUP101', 'Food Mart Co. Ltd.', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164, 11111', 'United States dollar', 'USD', 1, '65.00', '3.25', '61.75', '0.00', '0.00', '0.00', '61.75', NULL, 0, 1, 1, NULL, NOW(), NOW()),
        (4, 'KAxB4aV46ZisFYQrFQn2E6qSO', 1, '98983443', '3334342', '2021-11-01', '2021-11-30', 1, 'SUP101', 'Food Mart Co. Ltd.', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164, 11111', 'United States dollar', 'USD', 1, '3416.00', '170.80', '3245.20', '0.00', '0.00', '0.00', '3245.20', NULL, 0, 1, 1, NULL, NOW(), NOW()),
        (5, 'TcHXlrrckjWAAJs75Z2Voji0y', 1, '2324434', '234344', '2021-11-01', '2021-11-30', 1, 'SUP101', 'Food Mart Co. Ltd.', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164, 11111', 'United States dollar', 'USD', 1, '35.00', '1.75', '33.25', '0.00', '0.00', '0.00', '33.25', NULL, 0, 1, 1, NULL, NOW(), NOW())");

        DB::insert("INSERT INTO `purchase_order_products` (`id`, `slack`, `purchase_order_id`, `product_id`, `product_slack`, `product_code`, `name`, `quantity`, `amount_excluding_tax`, `subtotal_amount_excluding_tax`, `discount_percentage`, `tax_percentage`, `discount_amount`, `total_after_discount`, `tax_amount`, `tax_components`, `total_amount`, `stock_update`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'fuCvdPOMoVcjoXoWsbk2OEJnK', 1, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 0, 1, 1, NULL, NOW(), NULL),
        (2, 'ghfHEdBHWF2X6XXX5XNuzhoKH', 1, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '15.00', '7.25', '108.75', '5.00', '0.00', '5.44', '103.31', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '103.31', 0, 1, 1, NULL, NOW(), NULL),
        (3, '3Dqh4GLWy86Dl70MeJgABOtfE', 1, 38, 'RFIftVLWOHohp1XB0rWMf7jwq', 'APID', 'Chicken Sausage - Medium', '15.00', '14.00', '210.00', '5.00', '0.00', '10.50', '199.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '199.50', 0, 1, 1, NULL, NOW(), NULL),
        (4, 'rCHBTkt0NBcxL67YguEmtGHm7', 1, 64, 'KcyQrvdqFXEyK12ympbjIXJE3', 'S5LB', 'Pepsi', '20.00', '4.00', '80.00', '5.00', '0.00', '4.00', '76.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '76.00', 0, 1, 1, NULL, NOW(), NULL),
        (5, 'wFPSjGRVnnUrR2rnHJnBA1LPX', 2, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 0, 1, 1, NULL, NOW(), NULL),
        (6, 'luZ0Advpn8626oTT8qNWEAVlF', 2, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '1200.00', '7.25', '8700.00', '5.00', '0.00', '435.00', '8265.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '8265.00', 0, 1, 1, NULL, NOW(), NULL),
        (7, 'a5GeQHEygByNh0VKlYiX3qGho', 3, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '1.00', '9.00', '9.00', '5.00', '0.00', '0.45', '8.55', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '8.55', 0, 1, 1, NULL, NOW(), NULL),
        (8, 'IEzkXePUpiW0u6Mb6raVZk0lv', 3, 1, 'VlVLZwLw26cAs2GrMqZ10b1i0', 'GLYY', 'Margherita - Regular', '1.00', '5.00', '5.00', '5.00', '0.00', '0.25', '4.75', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '4.75', 0, 1, 1, NULL, NOW(), NULL),
        (9, 'ALJvRF32QxXryGXhDiSxiTs0F', 3, 22, 'Z7Tl4x6BB1lV97u4IJpfvW4c0', 'GBFP', 'Cheese N Corn - Regular', '1.00', '5.00', '5.00', '5.00', '0.00', '0.25', '4.75', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '4.75', 0, 1, 1, NULL, NOW(), NULL),
        (10, 'R5gfliKVaXbQ0sMxQrKiMZmgh', 3, 27, 'GzMgmv7PNgAVdKCFTmWeDSYjq', 'GBHH', 'Veggie Paradise - Large', '1.00', '9.00', '9.00', '5.00', '0.00', '0.45', '8.55', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '8.55', 0, 1, 1, NULL, NOW(), NULL),
        (11, '5xJJMGOp01RCUTxb3EpjodFzz', 3, 51, 'iZZD01EFzPqXSsXPPUAOlErSu', 'G4LU', 'Pepper Barbecue & Onion - Large', '1.00', '20.00', '20.00', '5.00', '0.00', '1.00', '19.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '19.00', 0, 1, 1, NULL, NOW(), NULL),
        (12, 'EnSl6dIIZlkUBY9MovPIgheDR', 3, 49, 'hjnfMNhOB2yoeaZrcgMtKqq8l', 'GAPX', 'Pepper Barbecue & Onion', '1.00', '17.00', '17.00', '5.00', '0.00', '0.85', '16.15', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '16.15', 0, 1, 1, NULL, NOW(), NULL),
        (13, 'WhvwnSCMRl7l4JVQ0CIjRmv0G', 4, 38, 'RFIftVLWOHohp1XB0rWMf7jwq', 'APID', 'Chicken Sausage - Medium', '100.00', '14.00', '1400.00', '5.00', '0.00', '70.00', '1330.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '1330.00', 0, 1, 1, NULL, NOW(), NULL),
        (14, 'vPezFwkRRvaQJRxxeMXbuysnc', 4, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '200.00', '9.00', '1800.00', '5.00', '0.00', '90.00', '1710.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '1710.00', 0, 1, 1, NULL, NOW(), NULL),
        (15, 'Vj9AL0Rm4ektCFJy34ariZznI', 4, 8, 'AkimSOp0Q9oFyq4agWGYeNauU', 'QVUR', 'Farm House - Medium', '18.00', '12.00', '216.00', '5.00', '0.00', '10.80', '205.20', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '205.20', 0, 1, 1, NULL, NOW(), NULL),
        (16, 'ymuUN0ffYzE8QxBb5beS0xvrT', 5, 4, 'dofcVfr7NmRHHrfGObcZwwJ11', 'FEU0', 'Double Cheese Margherita - Regular', '1.00', '9.00', '9.00', '5.00', '0.00', '0.45', '8.55', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '8.55', 0, 1, 1, NULL, NOW(), NULL),
        (17, 'rO0kqkkLvzMwmf6ZSAwfdRL5z', 5, 27, 'GzMgmv7PNgAVdKCFTmWeDSYjq', 'GBHH', 'Veggie Paradise - Large', '1.00', '9.00', '9.00', '5.00', '0.00', '0.45', '8.55', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '8.55', 0, 1, 1, NULL, NOW(), NULL),
        (18, 'lL4GUhxvoEBPYRJluAHAoBjLJ', 5, 49, 'hjnfMNhOB2yoeaZrcgMtKqq8l', 'GAPX', 'Pepper Barbecue & Onion', '1.00', '17.00', '17.00', '5.00', '0.00', '0.85', '16.15', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '16.15', 0, 1, 1, NULL, NOW(), NULL)");

        DB::insert("INSERT INTO `quotations` (`id`, `slack`, `store_id`, `quotation_number`, `quotation_reference`, `quotation_date`, `quotation_due_date`, `subject`, `bill_to`, `bill_to_id`, `bill_to_code`, `bill_to_name`, `bill_to_email`, `bill_to_contact`, `bill_to_address`, `currency_name`, `currency_code`, `tax_option_id`, `subtotal_excluding_tax`, `total_discount_amount`, `total_after_discount`, `total_tax_amount`, `shipping_charge`, `packing_charge`, `total_order_amount`, `notes`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'tAeAQRnYPal9Vic5J94TbejFo', 1, '101', '4567889', '2021-11-01', '2021-11-30', NULL, 'SUPPLIER', 1, 'SUP101', 'Food Mart Co. Ltd.', 'bergnaum.jordyn@yahoo.com', '+5452498135827', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'United States dollar', 'USD', 1, '86000.00', '4300.00', '81700.00', '0.00', '10.00', '20.00', '81730.00', 'Notes', 4, 1, 1, NOW(), NOW()),
        (2, 'RHjt27d0Ys7OsylV3T2UoNP78', 1, '102', '987656', '2021-07-01', '2021-11-30', NULL, 'SUPPLIER', 1, 'SUP101', 'Food Mart Co. Ltd.', 'bergnaum.jordyn@yahoo.com', '+5452498135827', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'United States dollar', 'USD', 0, '15175.00', '758.75', '14416.25', '0.00', '0.00', '0.00', '14416.25', 'Sample', 2, 1, 1, NOW(), NOW())");

        DB::insert("INSERT INTO `quotation_products` (`id`, `slack`, `quotation_id`, `product_id`, `product_slack`, `product_code`, `name`, `quantity`, `amount_excluding_tax`, `subtotal_amount_excluding_tax`, `discount_percentage`, `tax_percentage`, `discount_amount`, `total_after_discount`, `tax_amount`, `tax_components`, `total_amount`, `status`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'o6SHWuHSrfMveh5sezsOJQbDa', 1, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '522.50', 1, 1, NULL, NOW(), NULL),
        (2, '3PKa4ipsbd8wiQFUqV7JtachH', 1, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '1000.00', '7.25', '7250.00', '5.00', '0.00', '362.50', '6887.50', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '6887.50', 1, 1, NULL, NOW(), NULL),
        (3, '1ygegecsjS5R4u7ZyvIaxAdrD', 1, 5, 'g6kFjjPprKDKVdtlmVNJXCX7E', 'OVQM', 'Double Cheese Margherita - Medium', '1200.00', '11.00', '13200.00', '5.00', '0.00', '660.00', '12540.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '12540.00', 1, 1, NULL, NOW(), NULL),
        (4, '6yPQQTdGu2p8NXVwGfxFN6l5A', 1, 6, 'mESdnPzeqW0F9hn5MBqvvz6V0', 'WOOU', 'Double Cheese Margherita - Large', '5000.00', '13.00', '65000.00', '5.00', '0.00', '3250.00', '61750.00', '0.00', '[{\"name\":\"TAX\",\"tax_percentage\":0,\"tax_amount\":\"0.00\"}]', '61750.00', 1, 1, NULL, NOW(), NULL),
        (5, '5chJMobiU8WD7RD40igiHznN1', 2, 28, 'a9ba7yIAMiazLalCUthHHm3SM', 'AXMY', 'Fresh Veggie - Regular', '100.00', '5.50', '550.00', '5.00', '0.00', '27.50', '522.50', '0.00', '', '522.50', 1, 1, NULL, NOW(), NULL),
        (6, 'Ic2Hr7FzjNc0kmbScVJ833ZCd', 2, 10, '34ZiI0Tm0HffJ2ev2T7ZiNOOV', 'SD9H', 'Peppy Paneer - Regular', '500.00', '7.25', '3625.00', '5.00', '0.00', '181.25', '3443.75', '0.00', '', '3443.75', 1, 1, NULL, NOW(), NULL),
        (7, '1Rff6HNFOsAg0o3Abn19Jp8Jj', 2, 47, 'dLWX57pRwGTl1LLMIKk8pcJ8r', 'ECBM', 'Chicken Dominator - Medium', '1000.00', '11.00', '11000.00', '5.00', '0.00', '550.00', '10450.00', '0.00', '', '10450.00', 1, 1, NULL, NOW(), NULL)");

        DB::insert("INSERT INTO `transactions` (`id`, `slack`, `store_id`, `transaction_code`, `account_id`, `transaction_type`, `payment_method_id`, `payment_method`, `bill_to`, `bill_to_id`, `bill_to_name`, `bill_to_contact`, `bill_to_address`, `currency_code`, `amount`, `notes`, `pg_transaction_id`, `pg_transaction_status`, `transaction_date`, `transaction_merged`, `merged_from`, `merged_to`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
        (1, 'Gn5pB0xxiyyRUxt72KNuJx7Wi', 1, '101', 1, 1, 4, 'Cash', 'POS_ORDER', 1, 'Walkin Customer', '0000000000', '', 'USD', '64.13', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (2, 'K8pb2xtBcUdzXHZPxBW6SvmaT', 1, '102', 1, 1, 4, 'Cash', 'POS_ORDER', 2, 'Walkin Customer', '0000000000', '', 'USD', '4.75', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (3, 'L6sPFBVdTnEl6VPEyYGeAzMOM', 1, '103', 1, 1, 4, 'Cash', 'POS_ORDER', 3, 'Walkin Customer', '0000000000', '', 'USD', '9.50', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (4, 'MO9MaUiqgioOctVqHhYu3MJrM', 1, '104', 1, 1, 4, 'Cash', 'POS_ORDER', 4, 'Walkin Customer', '0000000000', '', 'USD', '27.55', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (5, 'BcmTAXKuhVaxWw0syQeFTTdwb', 1, '105', 1, 1, 4, 'Cash', 'POS_ORDER', 6, 'Walkin Customer', '0000000000', '', 'USD', '9.98', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (6, 'XhGK4lamSOCNVUrff7khxPpWf', 1, '106', 1, 1, 4, 'Cash', 'POS_ORDER', 8, 'Walkin Customer', '0000000000', '', 'USD', '28.03', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (7, 'm5gSyL0dgs8mHApD7TbWFX86h', 1, '107', 1, 1, 4, 'Cash', 'POS_ORDER', 9, 'Walkin Customer', '0000000000', '', 'USD', '25.65', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (8, 'ajGH0Fg1DDLBjZxokizPrurOH', 1, '108', 1, 1, 4, 'Cash', 'POS_ORDER', 10, 'Walkin Customer', '0000000000', '', 'USD', '17.58', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (9, 'tR6ViGyucj7DmjI8Dqe5iuAHj', 1, '109', 1, 1, 5, 'Card', 'POS_ORDER', 11, 'Walkin Customer', '0000000000', '', 'USD', '34.20', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (10, 'yedoWy6n7vcLalECZCBseAdhh', 1, '110', 1, 1, 4, 'Cash', 'POS_ORDER', 12, 'Walkin Customer', '0000000000', '', 'USD', '17.58', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (11, 'vhk2UIuu2nM1bhKllfSQabIjR', 1, '111', 1, 1, 4, 'Cash', 'POS_ORDER', 13, 'Walkin Customer', '0000000000', '', 'USD', '18145.00', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (12, '1lQrMWQGHVPCAfUNZBcMlI81e', 1, '112', 1, 1, 4, 'Cash', 'POS_ORDER', 14, 'Walkin Customer', '0000000000', '', 'USD', '150385.00', '', '', '', '2021-11-22', 0, NULL, NULL, 1, NULL, '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."', '". Carbon::parse(Carbon::now()->subDays(1))->format('Y-m-d H:i:s')."'),
        (13, 'YrWglyIjhApYVC2QG4XIG36wz', 1, '113', 1, 1, 4, 'Cash', 'POS_ORDER', 15, 'Walkin Customer', '0000000000', '', 'USD', '25.65', '', '', '', '2021-11-23', 0, NULL, NULL, 1, NULL, NOW(), NOW()),
        (14, 'H1O316mjmM8q2Hv30E5pJJkVC', 1, '114', 1, 1, 4, 'Cash', 'POS_ORDER', 16, 'Walkin Customer', '0000000000', '', 'USD', '14.73', '', '', '', '2021-11-23', 0, NULL, NULL, 1, NULL, NOW(), NOW()),
        (15, 'Zn9qQLAMqi2vxZFFfwXdg7UuC', 1, '115', 1, 1, 4, 'Cash', 'POS_ORDER', 17, 'Walkin Customer', '0000000000', '', 'USD', '17.58', '', '', '', '2021-11-23', 0, NULL, NULL, 1, NULL, NOW(), NOW()),
        (16, 'LFR4yAVFUBOpeHOMvTtM6lQF7', 1, '116', 1, 2, 4, 'Cash', 'SUPPLIER', 1, 'Food Mart Co. Ltd.', '+5452498135827, bergnaum.jordyn@yahoo.com', '75610 Ritchie Forest\nWisozkmouth, AL 88633-6164,11111', 'USD', '100000.00', NULL, NULL, NULL, '2021-11-23', 0, NULL, NULL, 1, NULL, NOW(), NOW()),
        (17, 'Cq4lVEM4ktmsFpW1VftDHRfm5', 1, '117', 1, 1, 5, 'Card', 'POS_ORDER', 18, 'Walkin Customer', '0000000000', '', 'USD', '25.65', '', '', '', '2021-11-23', 0, NULL, NULL, 1, NULL, NOW(), NOW())");

        $all_users = UserModel::select('id')
        ->active()
        ->get();

        if(count($all_users)>0){
            foreach ($all_users as $user) {

                for($k = 1; $k <= 15; $k++){
                    $notification = [
                        "slack" => $base_controller->generate_slack("notifications"),
                        "user_id" => $user->id,
                        "notification_text" => $faker->sentence($nbWords = 10, $variableNbWords = true),
                        "created_by" => 1
                    ];
                    $notif_id = NotificationModel::create($notification)->id;
                }

            }
        }

        $measurements = [
            [
                'unit_code' => 'L',
                'label' => 'Litre'
            ],
            [
                'unit_code' => 'T',
                'label' => 'Tablespoon'
            ],
            [
                'unit_code' => 'CUP',
                'label' => 'Cup'
            ],
            [
                'unit_code' => 'G',
                'label' => 'Gram'
            ],
        ];

        foreach ($measurements as $measurement) {

            $measurement_unit = [
                "slack" => $base_controller->generate_slack("measurement_units"),
                "unit_code" => $measurement['unit_code'],
                "label" => $measurement['label'],
                "status" => 1,
                "created_by" => 1
            ];
            
            MeasurementUnitModel::create($measurement_unit)->id;
        }
        
        Storage::disk('product')->putFileAs('/', new File(public_path('images/placeholder_images/placeholder_image.png')), 'placeholder_image.png');
        Storage::disk('product')->putFileAs('/', new File(public_path('images/placeholder_images/thumb_placeholder_image.png')), 'thumb_placeholder_image.png');
    }
}
