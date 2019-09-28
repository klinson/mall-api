<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings'],
], function ($api) {
    // 登录验证相关路由
    $api->group([
        'prefix' => 'auth'
    ], function ($api) {
        $api->post('login', 'AuthorizationsController@login');
        $api->post('logout', 'AuthorizationsController@logout');
        $api->post('login/wxapp', 'AuthorizationsController@wxappLogin');

    });

    //不需要登录的路由
    $api->group([

    ], function ($api) {
        $api->get('categories', 'CategoriesController@index');
        $api->get('goods', 'GoodsController@index');
        $api->get('goods/{goods}', 'GoodsController@show')->where('goods', '[0-9]+');;
        $api->get('broadcast', 'BroadcastsController@show');
        $api->post('files', 'FilesController@upload');


    });

    // 需要登录的路由
    $api->group([
        'middleware' => 'refresh.token'
    ], function ($api) {
        // 用户
        $api->get('user', 'UserController@show');
        $api->put('user', 'UserController@update');
        $api->resource('addresses', 'AddressesController', ['only' => ['index', 'update', 'store', 'destroy', 'show']]);

        // 购物车
        $api->resource('shoppingCarts', 'ShoppingCartsController', ['only' => ['index', 'update', 'store', 'destroy']]);
        $api->delete('shoppingCarts', 'ShoppingCartController@clearAll');

        // 订单
        $api->get('orders', 'OrdersController@index');
        $api->post('orders', 'OrdersController@store');
        $api->get('orders/{order}', 'OrdersController@show')->where('order', '[0-9]+');
    });
});