<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Transaction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use DB;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->input('Authorization') && $request->is('user/*')) {
                $apiToken=explode(' ', $request->input('Authorization'));
                if($request->is('user/transaction/*')){
                    $user = User::where('id', $request->input('cust_id')) -> first();
                } else if ($request->is('user/producttransaction/*')){
                    $transaction = Transaction::where([
                        ['status',0], 
                        ['cust_id',$request->input('cust_id')],
                    ])->first();
                    if($transaction){
                        $user = User::where('id', $transaction->cust_id) -> first();
                    } else {
                        
                        $user = User::where('id', $request->input('cust_id')) -> first();
                    }
                } else {
                    $user = User::where('id', $request->input('id')) -> first();
                }
                if($user){
                    if(Hash::check( $apiToken[1],$user->api_token)){
                        return User::where([
                            ['id', $user->id],
                            ['email_verified_at', '<=', Carbon::now()],
                        ])->first();
                    }
                }
            }
            if ($request->input('Authorization') && $request->is('admin/*')) {
                $apiToken=explode(' ', $request->input('Authorization'));
                $admin = Admin::where('id', $request->input('id')) -> first();
                if($admin){
                    if(Hash::check( $apiToken[1],$admin->api_token)){
                        return Admin::where([
                            ['id', $request->input('id')]
                        ])->first();
                    }
                }
            }
        });
    }
}
