<?php

/** @var \Laravel\Lumen\Routing\Router $router */
use Illuminate\Support\Facades\Mail;


/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/test', 'EmailController@send');

$router->group(['prefix'=>'user'], function () use ($router){
    $router->post('/auth/register', 'AuthController@register');
    $router->post('/auth/login', 'AuthController@login');
    $router->post('/auth/requestForgetPassword', 'AuthController@requestForgetPassword');
    $router->post('/auth/verifyUpdatePassword', 'AuthController@verifyUpdatePassword');
    $router->post('/auth/sendEmailVerification', 'AuthController@sendEmailVerification');
    $router->post('/auth/verifyEmail', 'AuthController@verifyEmail');

    $router->post('/show', 'UserController@show');
    $router->post('/update', 'UserController@update');
    $router->post('/verifyUpdateEmail', 'UserController@verifyUpdateEmail');

    $router->post('/product/showById', 'ProductController@showById');
    $router->post('/product/countByCategory', 'ProductController@countByCategory');
    $router->post('/product/showAllByCategory', 'ProductController@showAllByCategory');
    $router->post('/product/countBySearch', 'ProductController@countBySearch');
    $router->post('/product/showBySearch', 'ProductController@showBySearch');
    $router->post('/product/countAllDiscount', 'ProductController@countAllDiscount');
    $router->post('/product/showAllDiscount', 'ProductController@showAllDiscount');

    $router->post('/transaction/countByUserId', 'TransactionController@countByUserId');
    $router->post('/transaction/updateStatusPesan', 'TransactionController@updateStatusPesan');
    $router->post('/transaction/showAllByUserId', 'TransactionController@showAllByUserId');
    
    $router->post('/producttransaction/create', 'ProductTransactionController@create');
    $router->post('/producttransaction/delete', 'ProductTransactionController@delete');
    $router->post('/producttransaction/showBasket', 'ProductTransactionController@showBasket');

    $router->post('/productcategory/showAll', 'ProductCategoryController@showAll');
    $router->post('/productcategory/showById', 'ProductCategoryController@showById');
    $router->post('/productcategory/showByName', 'ProductCategoryController@showByName');

    $router->post('/information/show','InformationController@show');
    
    $router->get('/rajaongkir/getCity','RajaOngkirController@getCity');
    $router->post('/rajaongkir/getCityById','RajaOngkirController@getCityById');
    $router->post('/rajaongkir/getCost','RajaOngkirController@getCost');
});

$router->group(['prefix'=>'admin'], function () use ($router){
    $router->post('/user/showAll', 'UserController@showAll');
    $router->post('/login', 'AdminController@login');
    $router->post('/register', 'AdminController@register');

    $router->post('/product/create', 'ProductController@create');
    $router->post('/product/update', 'ProductController@update');
    $router->post('/product/delete', 'ProductController@delete');

    $router->post('/transaction/updateStatusDikirim', 'TransactionController@updateStatusDikirim');
    $router->post('/transaction/countByAll', 'TransactionController@countByAll');
    $router->post('/transaction/showAll', 'TransactionController@showAll');
    
    $router->post('/producttransaction/showByTransactionId', 'ProductTransactionController@showByTransactionId');
    
    $router->post('/productcategory/create', 'ProductCategoryController@create');    
    $router->post('/information/update','InformationController@update');
});