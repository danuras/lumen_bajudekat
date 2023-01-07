<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Admin;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class AdminController extends BaseController
{
  public function login(Request $request){
    $email = $request->input('email');
    $password = $request->input('password');

    $admin = Admin::where('email', $email) -> first();
    if(Hash::check($password, $admin->password)){
        $apiToken = base64_encode(Str::random(100));
        $admin->update([
            'api_token' =>Hash::make($apiToken),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Login Success!',
            'data' => [
                'admin' => $admin,
                'api_token' => $apiToken,
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Login Fail!',
            'data' => ''
        ], 400);
    }
  }
 /*  public function register(Request $request){
    $apiToken = base64_encode(Str::random(100));

    $admin=Admin::create([
        'email' => $request->input('email'), 
        'api_token' =>  Hash::make($apiToken), 
        'password' => Hash::make($request->input('password')), 
    ]);
    if($admin){
        return response()->json([
            'success' => true,
            'message' => 'Register Success!',
            'data' => [
              'admin'=>$admin,
              'api_token'=>$apiToken,
            ],
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Register Faild!',
            'data' => '',
        ], 400);
    }
  } */
}
