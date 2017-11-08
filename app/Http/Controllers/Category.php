<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Category as CategoryModel;

use App\Http\Resources\CategoryResource;

class Category extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_CATEGORY';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('category.categories', $data);
    }

    //This is the function that loads the add/edit page
    public function add_category($slack = null){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_CATEGORY';
        $data['action_key'] = ($slack == null)?'A_ADD_CATEGORY':'A_EDIT_CATEGORY';
        check_access(array($data['action_key']));

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();

        $data['category_data'] = null;
        if(isset($slack)){
            $category = CategoryModel::where('slack', '=', $slack)->first();
            if (empty($category)) {
                abort(404);
            }
            
            $category_data = new CategoryResource($category);
            $data['category_data'] = $category_data;
        }

        return view('category.add_category', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_CATEGORY';
        $data['action_key'] = 'A_DETAIL_CATEGORY';
        check_access([$data['action_key']]);

        $category = CategoryModel::where('slack', '=', $slack)->first();
        
        if (empty($category)) {
            abort(404);
        }

        $category_data = new CategoryResource($category);
        
        $data['category_data'] = $category_data;

        return view('category.category_detail', $data);
    }
}
