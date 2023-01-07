<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ProductTransaction;
use App\Models\User;
use Event;
use App\Events\TransactionCreatedEvent;
use Mavinoo\Batch\BatchFacade as Batch;
use DB;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',["only"=>[ 'updateStatusPesan', 'showAllByUserId', 'countByUserId']]);
        $this->middleware('autha',["only"=>['updateStatusDikirim', 'showAll', 'countByAll']]);
    }

    public function updateStatusPesan(Request $request){
        $target = Transaction::where(
            [
                ['status', 0],
                ['id',$request->input('transaction_id')],
                ['cust_id',$request->input('cust_id')]
            ]
        )->first();

        $transaction = DB::update('update transactions set status = ?, address = ?, kurir = ?, total = ?, ongkos = ? where id = ? and cust_id = ?', [1, $request->input('address'), $request->input('kurir'), $request->input('total'), $request->input('ongkos'), $target->id, $target->cust_id]);
        
        if ($transaction) {
            //event(new TransactionCreatedEvent($transaction->store_id));
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been updated!', 
                'data'=>$transaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Transaction failed to updated!', 
            'data'=>null
        ], 400);
    }
    
    public function updateStatusDikirim(Request $request){
        DB::beginTransaction();
        $transaction = DB::update('update transactions set status = ?,  nomor_resi = ? where id = ?', [2, $request->input('nomor_resi'), $request->input('transaction_id')]);
        
        $productTransaction = DB::select('select pt.product_id, p.stock-pt.amount as new_stock from product_transactions pt inner join products p on p.id =pt.product_id where pt.transaction_id = ?', [$request->input('transaction_id')]);
        

        $length = sizeof($productTransaction);
        $values = '[';
        $values .= '{"id":'.$productTransaction[0]->product_id.",";
        $values .= '"stock":'.$productTransaction[0]->new_stock."}";
        if($productTransaction[0]->new_stock<0){
            return response()->json([
                'success' => false,
                'message' => 'Stok telah habis',
                'data' => $productTransaction[0]
            ], 400);
        }
        for ($i = 1; $i < $length; $i++){
            $values .=",".'{"id":'.$productTransaction[$i]->product_id.",".'"stock":'.$productTransaction[$i]->new_stock."}";
            
            if($productTransaction[$i]->new_stock<0){
                return response()->json([
                    'success' => false,
                    'message' => 'Stok telah habis',
                    'data' => $productTransaction[$i]
                ], 400);
            }
        }
        $values .="]";

        $productInstance = new Product;
        $values = json_decode($values, true);
        $product = Batch::update($productInstance, $values, 'id');
        DB::commit();

        if ($transaction!=null && $product!=null) {
            return response()->json([
                'success' => true,
                'message'=>'Status transactions has been changed!', 
                'data'=>$transaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Status transactions failed to changed!', 
            'data'=>null
        ], 400);
    }

    /* public function createByCashier(Request $request){
        $transaction = DB::update('update transactions set status = 2 where status = 3 and id = ?', [$request->input('transaction_id')]);
        if ($transaction!=null && $productTransaction!=null) {
            event(new TransactionCreatedEvent($transaction->store_id));
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been created!', 
                'data'=>$transaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Transaction failed to created!', 
            'data'=>null
        ], 400);
    } */

    /* public function requestCancel(Request $request){
        $transaction = DB::update('update transactions set is_request_cancel = 1 where id = ?', [$transaction->input('id')]);

        if ($transaction) {
            event(new CancelOrder($transaction->store_id));
            return response()->json([
                'success' => true,
                'message'=>'Request has been sended!', 
                'data'=>$transaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Request has not been sended!', 
            'data'=>null
        ], 400);
    }

    public function updateStatusCancel(Request $request){
        $transaction = DB::update('update transactions set status = ? where id = ?', [5, $request->input('id')]);

        if ($transaction) {
            return response()->json([
                'success' => true,
                'message'=>'Status transactions has been changed!', 
                'data'=>$transaction
            ], 200);
        } 
        return response()->json([
            'success' => false,
            'message'=>'Status transactions failed to changed!', 
            'data'=>null
        ], 400);
    } */

    public function countByUserId(Request $request){
        $transaction=DB::select('select count(*) as count from transactions where cust_id = ? and status != 0 and status = ?', [$request->input('cust_id'), $request->input('status')]);
        
        if ($transaction) {
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been retreived successfully!', 
                'data'=>$transaction
            ], 200);
        } 
        return response([
            'success' => false,
            'message'=>'Transaction failed to retrieved!', 
            'data'=>null
        ], 400);
    }

    public function showAllByUserId(Request $request){
        $transaction=DB::select('select id, status, address, nomor_resi, cust_id, payment, kurir, total, ongkos, updated_at from transactions where cust_id = ? and status != 0 and status = ? limit ?, 20', [$request->input('cust_id'), $request->input('status'), $request->input('count')]);
        
        if ($transaction) {
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been retreived successfully!', 
                'data'=>$transaction
            ], 200);
        } 
        return response([
            'success' => false,
            'message'=>'Transaction failed to retrieved!', 
            'data'=>null
        ], 400);
    }

    public function countByAll(Request $request){
        $transaction=DB::select('select count(*) as count from transactions where status != 0 and status = ?', [$request->input('status')]);
        
        if ($transaction) {
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been retreived successfully!', 
                'data'=>$transaction
            ], 200);
        } 
        return response([
            'success' => false,
            'message'=>'Transaction failed to retrieved!', 
            'data'=>null
        ], 400);
    }

    public function showAll(Request $request){
        $transaction=DB::select('select id, status, nomor_resi, address, cust_id, payment, kurir, total, ongkos from transactions where status != 0 and status = ? limit ?,20', [$request->input('status'), $request->input('count')]);
        
        if ($transaction) {
            return response()->json([
                'success' => true,
                'message'=>'Transaction has been retreived successfully!', 
                'data'=>$transaction
            ], 200);
        } 
        return response([
            'success' => false,
            'message'=>'Transaction failed to retrieved!', 
            'data'=>null
        ], 400);
    
    }
}
