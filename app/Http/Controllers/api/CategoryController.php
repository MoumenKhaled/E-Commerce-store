<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
class CategoryController extends Controller
{
    //POST
    public function addCategory(Request $request)
    {
        $name=$request->name;
        $category=new Category();
        $category->name=$name;
        $category->save();
    }
    //GET
    public function getProducts($category_id)
    {
        $products=Category::find($category_id)->products;
        return response()->json([
            'status'=>true,
            'data'=>$products
        ],200);
    }
    //GET
    public function getCategories()
    {
        return response()->json([
            'status'=>true,
            'data'=>Category::all()
        ],200);
    }
}
