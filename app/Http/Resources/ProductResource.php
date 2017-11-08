<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function block_recurring_variants($value){
        $this->block_recurring_data = $value;
        return $this;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $ingredients = ProductIngredientResource::collection($this->ingredients);
     
        $ingredients_collection = collect($ingredients);
        $low_ingredient_stock = $ingredients_collection->map(function ($item, $key) {
            return $item['low_stock'];
        })->toArray();
        $low_ingredient_stock = (!empty($low_ingredient_stock))?in_array(1, $low_ingredient_stock):false;
       
        $addon_groups = ProductAddonGroupResource::collection($this->addon_groups);

        $block_recurring_data = (isset($this->block_recurring_data))?$this->block_recurring_data:false;

        $variants = [];
        $variants_by_options = [];
        $variants_by_options_pos = [];
        $parent_variant_option = [];
        if($block_recurring_data == false){
            $product_variants = $this->product_variants($this->id);
            $variants = ProductVariantResource::collection($product_variants['product_variants']);

            $variants_collection = collect($variants);
            $variants_by_options = $variants_collection->groupBy('variant_option.label');
            $variants_by_options->toArray();
            
            $parent_variant_option = $product_variants['parent_variant_option'];

            $product_variants_pos = $this->product_variants($this->id, false);
            $variants_pos = ProductVariantResource::collection($product_variants_pos['product_variants']);

            $variants_pos_collection = collect($variants_pos);
            $variants_by_options_pos = $variants_pos_collection->sortBy('product.name')->groupBy('variant_option.label');
            $variants_by_options_pos->toArray();
        }

        return [
            'slack' => $this->slack,
            'product_code' => $this->product_code,
            'name' => $this->name,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'alert_quantity' => $this->alert_quantity,
            'purchase_amount_excluding_tax' => $this->purchase_amount_excluding_tax,
            'sale_amount_excluding_tax' => $this->sale_amount_excluding_tax,
            'sale_amount_including_tax' => $this->sale_amount_including_tax,
            'category' => new CategoryResource($this->category),
            'supplier' => new SupplierResource($this->supplier),
            'tax_code' => new TaxcodeResource($this->tax_code),
            'discount_code' => new DiscountcodeResource($this->discount_code),
            'images' => ProductImageResource::collection($this->product_images),
            'is_ingredient' => $this->is_ingredient,
            'is_ingredient_price' => $this->is_ingredient_price,
            'ingredients' => $ingredients,
            'ingredient_low_stock' => $low_ingredient_stock,
            'is_addon_product' =>  $this->is_addon_product,
            'addon_groups' => $addon_groups,
            'variants' => isset($variants)?$variants:NULL,
            'variants_by_options' => isset($variants_by_options)?$variants_by_options:NULL,
            'variants_by_options_pos' => isset($variants_by_options_pos)?$variants_by_options_pos:NULL,
            'parent_variant_option' => isset($parent_variant_option)?$parent_variant_option:NULL,
            'customizable' => ($addon_groups->isEmpty())?0:1,
            'status' => new MasterStatusResource($this->status_data),
            'detail_link' => (check_access(['A_DETAIL_PRODUCT'], true))?route('product', ['slack' => $this->slack]):'',
            'created_at_label' => $this->parseDate($this->created_at),
            'updated_at_label' => $this->parseDate($this->updated_at),
            'created_by' => new UserResource($this->createdUser),
            'updated_by' => new UserResource($this->updatedUser)
        ];
    }
}
