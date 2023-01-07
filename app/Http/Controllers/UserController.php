<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use DB;
use Validator;
use App\Mail\SendEmail;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth', ['only'=>['show','update', 'verifyUpdateEmail']]);
        $this->middleware('autha', ['only'=>['showAll']]);
    }
    
    public function show(Request $request){
        $user = DB::select('select id, name, email, phone_number, address, city from users where id = ?', [$request->input('id')]);
        if($user!=null){
            return response()->json([
                'success' => true,
                'message' => 'User Found!',
                'data' => $user
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'User Not Found!',
                'data' => ''
            ], 400);
        }
    }

    public function requestUpdate(Request $request){
    }

    public function verifyUpdateEmail(Request $request){
        $useru = User::where('id', $request->input('id')) -> first();
        if($useru->token == $request->input('token')){
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1',
                'phone_number' => 'required',
                'address' => 'required|max:50',
                'email'=>'required|email|unique:users',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json([
                    'success' => false,
                    'message' => $messages,
                    'data' => '',
                ], 400);
            }
            $user = DB::update('update users set name = ?, phone_number = ?, address = ?, email = ? where id = ?', [$request->input('name'), $request->input('phone_number'), $request->input('address'), $request->input('email'),$request->input('id')]);

            if($user){
                return response()->json([
                    'success' => true,
                    'message' => 'User Updated!',
                    'data' => $user
                ], 200);
            }
            else {
                return response()->json([
                    'success' => false,
                    'message' => 'User Not updated!',
                    'data' => ''
                ], 400);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Wrong token!',
                'data' => ''
            ], 300);
        }
    }
    
    public function update(Request $request){
        $userv = User::where('id', $request->input('id'))->first();
        if($userv->email == $request->input('email')){
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1',
                'phone_number' => 'required',
                'city' => 'required',
                'address' => 'required|max:50',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json([
                    'success' => false,
                    'message' => $messages,
                    'data' => '',
                ], 400);
            }
        } else {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:1',
                'phone_number' => 'required',
                'city' => 'required',
                'address' => 'required|max:50',
                'email'=>'required|email|unique:users',
            ]);
            if ($validator->fails()) {
                $messages = $validator->messages();
                return response()->json([
                    'success' => false,
                    'message' => $messages,
                    'data' => '',
                ], 400);
            }
        }
        
        $old_email =User::where('id', $request->input('id')) -> first();
        if($old_email->email != $request->input('email')){
            $token = Str::random(6);
            DB::update('update users set token = ? where id = ?', [$token, $request->input('id')]);
            Mail::to($request->input('email'))->send(new SendEmail($token));

            $user = DB::update('update users set name = ?, phone_number = ?, address = ?, email = ?, email_verified_at = null, city = ? where id = ?', [$request->input('name'), $request->input('phone_number'), $request->input('address'), $request->input('email'),$requst->input('city'),$request->input('id')]);

            return response()->json([
                'success' =>false,
                'message' => 'Must verification email',
                'data' => '',
            ], 300);
        }
        $user = DB::update('update users set name = ?, phone_number = ?, address = ?, email = ?, city = ? where id = ?', [$request->input('name'), $request->input('phone_number'), $request->input('address'), $request->input('email'),$request->input('city'),$request->input('id')]);

        if($user){
            return response()->json([
                'success' => true,
                'message' => 'User Updated!',
                'data' => $user
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'User Not updated!',
                'data' => ''
            ], 400);
        }
    }

    public function showAll(Request $requst){
        $user = DB::select('select name, email, address, phone_number from users where name like ?', $request->input('name'));

        if($user){
            return response()->json([
                'success' => true,
                'message' => 'User Found!',
                'data' => $user
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'User Not Found!',
                'data' => ''
            ], 400);
        }
    }
}
