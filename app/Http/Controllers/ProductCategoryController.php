<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Validator;
use DB;

class ProductCategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('autha', ['only'=>['create']]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'categories_name' => 'required|unique:product_categories'
        ]);
        if ($validator->fails()) {
            $storeCategory = DB::select('select id, categories_name from product_categories where categories_name = ?', [$request->input('categories_name')]);
            if($storeCategory){
                return response()->json([
                    'success' => true,
                    'massage' => 'Category has been retrevied!',
                    'data' => $storeCategory
                ], 200);
            }
            $messages = $validator->messages();
            return response()->json([
                'success' => false,
                'message' => $messages,
                'data' => '',
            ], 400);
        }

        $storeCategory=ProductCategory::create($request->all());
        if($storeCategory){
            return response()->json([
                'success' => true,
                'message'=>'new Category has been added', 
                'data'=>$storeCategory
            ],200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Category hasn\'t been added', 
            'data'=>''
        ],400);
    
    
    }

    public function showAll(){
        $storeCategory=DB::select('select id, categories_name from product_categories');
        if ($storeCategory) {
            return response()->json([
                'success' => true,
                'message'=>'Data retrieved successfully!', 
                'data'=>$storeCategory
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Data failed to retrieve!', 
            'data'=>null
        ], 400);
    }

    public function showByName(Request $request){
        $storeCategory = DB::select('select id, categories_name from product_categoriees where categories_name like ?', [$request->input("categories_name")]);
        
        if ($storeCategory) {
            return response()->json([
                'success' => true,
                'message'=>'Data retrieved successfully!', 
                'data'=>$storeCategory
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Data failed to retrieve!', 
            'data'=>null
        ], 400);
    }

    public function showById(Request $request){
        $productCategory=DB::select('select categories_name from product_categories where id = ?',[$request->input('id')]);
        if ($productCategory) {
            return response()->json([
                'success' => true,
                'message'=>'Data retrieved successfully!', 
                'data'=>$productCategory
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Data failed to retrieve!', 
            'data'=>null
        ], 400);
    }
}
