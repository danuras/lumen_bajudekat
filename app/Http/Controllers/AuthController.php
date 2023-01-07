<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\User;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use DB;
use Carbon\Carbon;

class AuthController extends BaseController
{
  public function register(Request $request){
    $validator = Validator::make($request->all(), [
        'email'=>'required|email|unique:users',
        'password' => [
            'required',
            'string',
            'min:8',             // must be at least 10 characters in length
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
            'regex:/[@$!%*#?&]/', // must contain a special character
        ],
        'city' => 'required',
        'address' => 'required|max:50',
        'name' => 'required|min:1',
        'phone_number' => 'required',
    ]);
    if ($validator->fails()) {
        $messages = $validator->messages();
        return response()->json([
            'success' => false,
            'message' => $messages,
            'data' => '',
        ], 400);
    }

    
    $apiToken = base64_encode(Str::random(100));

    $user=User::create([
        'name' => $request->input('name'), 
        'email' => $request->input('email'), 
        'phone_number' => $request->input('phone_number'), 
        'city' => $request->input('city'), 
        'address' => $request->input('address'), 
        'api_token' =>  Hash::make($apiToken), 
        'password' => Hash::make($request->input('password')), 
    ]);
    if($user){
        $token = Str::random(6);
        $ok = DB::update('update users set token = ? where email = ?', [$token, $request->input('email')]);
        Mail::to($request->input('email'))->send(new SendEmail($token));
    }
    if($user){
        return response()->json([
            'success' => true,
            'message' => 'Register Success!',
            'data' => [
              'user'=>$user,
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
  }

  public function sendEmailVerification(Request $request){
    $token = Str::random(6);
    $user = DB::update('update users set token = ? where email = ?', [$token, $request->input('email')]);
    Mail::to($request->input('email'))->send(new SendEmail($token));
    if($user){
        return response()->json([
            'success' => true,
            'message' => 'Security has been sent!',
            'data' => $user,
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Security has not been sent!',
            'data' => '',
        ], 400);
    }
  }

  public function verifyEmail(Request $request){
    $user = User::where('email', $request->input('email')) -> first();
    if($user->token == $request->input('token')){
        $useru = DB::update('update users set email_verified_at = ? where email = ?', [Carbon::now(), $request->input('email')]);
        if($useru){
            return response()->json([
                'success' => true,
                'message' => 'Email has been verified!',
                'data' => $useru,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Email has not been verified!',
                'data' => '',
            ], 400);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Wrong token!',
            'data' => '',
        ], 350);
    }
  }

  public function login(Request $request){
    $email = $request->input('email');
    $password = $request->input('password');

    $user = User::where('email', $email) -> first();
    if(Hash::check($password, $user->password)){
        $apiToken = base64_encode(Str::random(100));
        $user->update([
            'api_token' =>Hash::make($apiToken),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Login Success!',
            'data' => [
                'user' => $user,
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

  public function requestForgetPassword(Request $request){
    $token = Str::random(6);
    $user = DB::update('update users set token = ? where email = ?', [$token, $request->input('email')]);
    Mail::to($request->input('email'))->send(new SendEmail($token));
    
    if($user){
        return response()->json([
            'success' => true,
            'message' => 'Request has been sended!',
            'data' => $user,
        ], 200);
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Request has not been sended!',
            'data' => '',
        ], 400);
    }
  }
  
  public function verifyUpdatePassword(Request $request){
    $u = User::where('email', $request->input('email')) -> first();
    if($u->token == $request->input('token')){
        $validator = Validator::make($request->all(), [
          'password' => [
              'required',
              'string',
              'min:8',             // must be at least 10 characters in length
              'regex:/[a-z]/',      // must contain at least one lowercase letter
              'regex:/[A-Z]/',      // must contain at least one uppercase letter
              'regex:/[0-9]/',      // must contain at least one digit
              'regex:/[@$!%*#?&]/', // must contain a special character
          ],
        ]);
        if ($validator->fails()) {
            $messages = $validator->messages();
            return response()->json([
                'success' => false,
                'message' => $messages,
                'data' => '',
            ], 400);
        }
        $user = DB::update('update users set password = ? where email = ?', [Hash::make($request->input('password')), $request->input('email')]);

        if($user){
            return response()->json([
                'success' => true,
                'message' => 'Password Updated!',
                'data' => $user
            ], 200);
        }
        else {
            return response()->json([
                'success' => false,
                'message' => 'Password Not updated!',
                'data' => ''
            ], 400);
        }
    } else {
        return response()->json([
            'success' => false,
            'message' => 'Wrong token!',
            'data' => ''
        ], 350);
    }
  }
}
