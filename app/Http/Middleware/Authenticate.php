<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use App\Models\User;
use App\Models\Transaction;
use DB;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            $apiToken=explode(' ', $request->input('Authorization'));
            if($request->is('user/transaction/*')){
                $user = User::where('id', $request->input('cust_id')) -> first();
            } else if ($request->is('user/producttransaction/*')){
                $transaction = Transaction::where([['status',0], ['cust_id',$request->input('cust_id')]])->first();
                if($transaction){
                    $user = User::where('id', $transaction->cust_id) -> first();
                } else {
                    $user = User::where('id', $request->input('cust_id')) -> first();
                }
            } else {
                $user = User::where('id', $request->input('id')) -> first();
            }
            
            if($user){
                if(!$user->email_verified_at){
                    return response()->json([
                        'success' => false,
                        'message' => 'Must verify email',
                        'data'=>$user,
                    ], 350);
                }
            }
            
            return response()->json([
                'success'=>false,
                'message'=>'Unauthorized.',
                'data'=>'',
            ], 401);
        }

        return $next($request);
    }
}
