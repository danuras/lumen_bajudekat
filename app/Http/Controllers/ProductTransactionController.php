<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ProductTransaction;
use App\Models\User;
use Event;
use App\Events\TransactionCreatedEvent;
use Mavinoo\Batch\BatchFacade as Batch;
use DB;

class ProductTransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',["only"=>['create', 'delete', 'showBasket']]);    /*     
        $this->middleware('autha',["only"=>['showByTransactionId']]);      */   
    }

    public function create(Request $request){
        DB::beginTransaction();
        $transaction = Transaction::where([['status', 0], ['cust_id',$request->input('cust_id')]])->first();
        if($transaction){
            
        } else {
            $transaction = Transaction::create([
                'status'=>0,
                'cust_id'=>$request->input('cust_id'),
                'payment'=>$request->input('payment'),
            ]);
        }
        
        $productTransaction = ProductTransaction::create([
            'buy_price' => $request->input('buy_price'),
            'sell_price' => $request->input('sell_price'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'product_id' => $request->input('product_id'),
            'weight' => $request->input('weight'),
            'transaction_id' => $transaction->id,
        ]);
        DB::commit();
        if ($productTransaction!=null) {
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been created!', 
                'data'=>$productTransaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to created!', 
            'data'=>null
        ], 400);
    }

    public function showBasket(Request $request){
        $transaction = Transaction::where([['status', 0], ['cust_id',$request->input('cust_id')]])->first();
        
        $productTransaction = DB::select('select pt.id as id, pt.sell_price as sell_price, pt.currency as currency, pt.amount as amount, pt.product_id as product_id, pt.transaction_id as transaction_id, pt.weight as weight, p.image_url as image_url, p.name as name, p.discount as discount, p.discount_expired_at as discount_expired_at, p.weight as weight from product_transactions pt inner join products p on pt.product_id = p.id where pt.transaction_id = ?', [$transaction->id]);
        if ($productTransaction!=null) {
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been retreived!', 
                'data'=>$productTransaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to retreived!', 
            'data'=>null
        ], 400);
    }

    public function showByTransactionId(Request $request){
        $transaction = Transaction::where([['status', '!=', 0], ['id',$request->input('transaction_id')]])->first();
        
        $productTransaction = DB::select('select pt.id as id, pt.sell_price as sell_price, pt.currency as currency, pt.amount as amount, pt.product_id as product_id, pt.transaction_id as transaction_id, pt.weight as weight, p.image_url as image_url, p.name as name, p.discount as discount, p.discount_expired_at as discount_expired_at, p.weight as weight from product_transactions pt inner join products p on pt.product_id = p.id where pt.transaction_id = ?', [$transaction->id]);
        if ($productTransaction!=null) {
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been retreived!', 
                'data'=>$productTransaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to retreived!', 
            'data'=>null
        ], 400);
    }
/* 
    public function createByCashier(Request $request){
        $transactionId = DB::select('select id from transactions where status = 3')->first();
        if($transactionId ==null){
            $transaction = Transaction::create([
                'cust_id'=>$request->input('cust_id'),
                'status'=>3,
                'payment'=>$request->input('payment'),
            ]);
            $transactionId = $transaction->id;
        }
        
        $productTransaction = ProductTransaction::create([
            'buy_price' => $request->input('buy_price'),
            'sell_price' => $request->input('sell_price'),
            'currency' => $request->input('currency'),
            'amount' => $request->input('amount'),
            'product_id' => $request->input('product_id'),
            'weight' => $request->input('weight'),
            'transaction_id' => $transactionId,
        ]);
        if ($productTransaction!=null) {
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been created!', 
                'data'=>$productTransaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to created!', 
            'data'=>null
        ], 400);
    }
 */
    public function delete(Request $request){
        $transaction = Transaction::where([['status', 0], ['cust_id',$request->input('cust_id')]])->first();
        
        $productTransaction=DB::delete('delete from product_transactions where id = ? and transaction_id = ?', [$request->input('pt_id'), $transaction->id]);
        $check = ProductTransaction::where([['transaction_id',$transaction->id]])->first();
        if(!$check){
            $transaction = DB::delete('delete from transactions where id = ?', [$transaction->id]);
        }
        if($productTransaction){ 
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been deleted!',
                'data'=>$productTransaction
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to deleted',
            'data'=>null
        ], 400);
    }
/* 
    public function deleteByCashier(Request $request){
        $transactionId = DB::select('select id from transactions where status = 3')->first();
        $productTransaction=DB::delete('delete from product_transactions where id = ? and transaction_id = ?', [$request->input('product_transaction_id'), $request->input('transaction_id')]);
        $check = DB::select('select id from product_transactions where transaction_id = ?', [$request->input('transaction_id')])->first();
        if($check!=null){
            $transaction = DB::delete('delete from transactions where id = ?', [$request->input('transaction_id')]);
        }
        if($productTransaction){ 
            return response()->json([
                'success' => true,
                'message'=>'Product Transaction has been deleted!',
                'data'=>$productTransaction
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message'=>'Product Transaction failed to deleted',
            'data'=>null
        ], 400);
    } */
}
