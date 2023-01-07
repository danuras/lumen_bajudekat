<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductTransaction;
use App\Models\Transaction;
use Mavinoo\Batch\BatchFacade as Batch;
use DB;
use Validator;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('autha', ['only' => ['create', 'update', 'delete']]);
    }

    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:40',
            'categories_name' => 'required',
            'description' => 'required|max:1000',
            'image_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'currency' => 'required',
            'buy_price' => 'required|min:0',
            'sell_price' => 'required|min:0',
            'weight'=>'required|min:0',
            'stock'=>'required|min:0',
        ]);

        
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'success' => false,
                'message' => $messages,
                'data' => '',
            ], 400);
        }

        $productCategory = ProductCategory::where('categories_name', $request->input('categories_name'))->first();
        $pcid;
        if($productCategory){
            $pcid = $productCategory->id;
        } else {
            $productCategory = ProductCategory::create([
                'categories_name'=>$request->input('categories_name'),
            ]);
            $pcid = $productCategory->id;
        }
        
        $file = $request->file('image_url');
        $filename = date('YmdHi').$file->getClientOriginalName();
        $file->move(public_path('product/Image/'), $filename);
        $image = public_path('product/Image/').'/'.$filename;
        
        
        $product = Product::create([
            'name'=>$request->input('name'),
            'image_url'=>$image,
            'categories_id'=>$pcid,
            'description'=>$request->input('description'),
            'buy_price'=>$request->input('buy_price'),
            'sell_price'=>$request->input('sell_price'),
            'currency'=>$request->input('currency'),
            'weight'=>$request->input('weight'),
            'stock'=>$request->input('stock'),
            'discount'=>$request->input('discount'),
            'discount_expired_at'=>$request->input('discount_expired_at'),
        ]);
        
        if($product){
            return response()->json([
                'success' => true,
                'message'=> 'Product has been created!',
                'data'=> $product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to created!',
            'data'=>null
        ], 400);
    }

    public function update(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:40',
            'categories_name' => 'required',
            'description' => 'required|max:1000',
            'currency' => 'required',
            'weight' => 'required|min:0',
            'buy_price' => 'required|min:0',
            'sell_price' => 'required|min:0',
            'stock'=>'required|min:0',
        ]);
        
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'success' => false,
                'message' => $messages,
                'data' => '',
            ], 400);
        }

        
        $productCategory = ProductCategory::where('categories_name', $request->input('categories_name'))->first();
        $pcid;
        if($productCategory){
            $pcid = $productCategory->id;
        } else {
            $productCategory = ProductCategory::create([
                'categories_name'=>$request->input('categories_name'),
            ]);
            $pcid = $productCategory->id;
        }
        $image = '';

        if($request->file('image_url')){
            $validator = Validator::make($request->all(), [
                'image_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json([
                    'success' => false,
                    'message' => $messages,
                    'data' => '',
                ], 400);
            }
            $file = $request->file('image_url');
            $filename = date('YmdHi').$file->getClientOriginalName();
            $file->move(public_path('product/Image/'), $filename);
            $image = public_path('product/Image/').'/'.$filename;
        }
        $product=DB::update('update products set name = ?, image_url = ?, categories_id = ?, description = ?, buy_price = ?,sell_price = ?,currency = ?, stock = ?, weight = ?, discount = ?, discount_expired_at = ? where id = ?', [$request->input('name'),$image,$pcid,$request->input('description'),$request->input('buy_price'),$request->input('sell_price'),$request->input('currency'),$request->input('stock'),$request->input('weight'),$request->input('discount'),$request->input('discount_expired_at'), $request->input('product_id')]);

        if($product){
            return response()->json([
                'success' => true,
                'message'=>'Product has been updated!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'succcess' =>false,
            'message'=>'Product failed to updated!',
            'data'=>null
        ],400);
    }
    
    public function delete(Request $request){
        $product=DB::delete('delete from products where id = ?', [$request->input('product_id')]);
        if($product){ 
            return response()->json([
                'success' => true,
                'message'=>'Product has been deleted!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to deleted',
            'data'=>null
        ], 400);
    }

    /* public function updateStock(Request $request){
        $productInstance = new Product;
        $values = json_decode($request->input('values'), true);
        $product = Batch::update($productInstance, $values, 'id');

        if($product){
            return response()->json([
                'success' => true,
                'message'=>'Stock has been updated!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'succcess' =>false,
            'message'=>'Stock failed to updated!',
            'data'=>null
        ],400);
    }

    public function showStock(Request $request){
        $stocks = DB::table('products')->selectRaw('stock')->whereIn('id',json_decode(trim($request->input('ids'),','), true))->get();
        
        if($stocks){ 
            return response()->json([
                'success' => true,
                'message'=>'Stock product has been retreived!',
                'data'=>$stocks
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Stock product failed to retreived',
            'data'=>null
        ], 400);
    } */

    public function showById(Request $request){
        $transaction = Transaction::where([['status', 0], ['cust_id', $request->input('cust_id')]])->first();
        if($transaction){
            $productBasket = DB::select( 'select product_id from product_transactions where transaction_id = ?',[ $transaction->id]);
            $values = '';
            $length = count($productBasket);
            
            $values .= $productBasket[0]->product_id;


            for ($i = 1; $i < $length; $i++){
                $values .= ",".$productBasket[$i]->product_id;
            }
            $product=DB::select('select id,name,image_url,categories_id,description,buy_price,sell_price, stock,currency,weight, id in ('.$values.') as isBasket,discount,discount_expired_at from products where id = ?', [ $request->input('product_id')]);
        }
        else {
            $product=DB::select('select id,name,image_url,categories_id,description,buy_price,sell_price, stock,currency,weight, 0 as isBasket,discount,discount_expired_at from products where id = ?', [$request->input('product_id')]);
        }
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to retrieved!',
            'data'=>null
        ], 400);
    }

    public function countByCategory(Request $request){
        $product=DB::select('select count(*) as count from products where categories_id = ?', [ $request->input('categories_id')]);
        
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to retrieved!',
            'data'=>null
        ], 400);
    }

    public function showAllByCategory(Request $request){
        $product=DB::select('select id,name,image_url,categories_id,sell_price,currency,discount,discount_expired_at from products where categories_id = ? limit ?, 24', [$request->input('categories_id'), $request->input('count')]);
        
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to retrieved!',
            'data'=>null
        ], 400);
    }

    public function countAllDiscount(Request $request){
        $product=DB::select('select count(*) as count from products where discount > 0 and discount_expired_at > ?',[Carbon::now()->setTimezone('Asia/Jakarta')] );
        
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to retrieved!',
            'data'=>null
        ], 400);
    }

    public function showAllDiscount(Request $request){
        $product=DB::select('select id,name,image_url,categories_id,sell_price,currency,discount,discount_expired_at from products where discount > 0 and discount_expired_at > ? limit ?, 24', [Carbon::now()->setTimezone('Asia/Jakarta'), $request->input('count')]);
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product failed to retrieved!',
            'data'=>null
        ], 400);
    }

    public function countBySearch(Request $request){
        $product=DB::select('select count(*) as count from products where (name like ? or description like ?)', [$request->input('search'), $request->input('search')]);

        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success'=>false,
            'message'=>'Product failed to retreived!',
            'data'=>null
        ], 400);
    }

    public function showBySearch(Request $request){
        $product=DB::select('select id,name,image_url,categories_id,sell_price,currency,discount,discount_expired_at from products where (name like ? or description like ?) limit ?, 24', [$request->input('search'), $request->input('search'), $request->input('count')]);
        if($product){
            return response()->json([
                'success'=>true,
                'message'=>'Product has been retreived!',
                'data'=>$product
            ], 200);
        }
        return response()->json([
            'success'=>false,
            'message'=>'Product failed to retreived!',
            'data'=>null
        ], 400);
    }
}
