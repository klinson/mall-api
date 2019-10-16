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
        $api->get('carouselAd', 'CarouselAdsController@show');
        $api->post('files', 'FilesController@upload');

        // 微信回调路由
        $api->post('wechat/OrderPaidNotify', 'WechatController@OrderPaidNotify')->name('order.wechat.pay.notify');
        $api->post('wechat/RechargeThresholdOrderPaidNotify', 'WechatController@RechargeThresholdOrderPaidNotify')->name('RechargeThresholdOrder.wechat.pay.notify');



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
        $api->delete('shoppingCarts', 'ShoppingCartsController@clearAll');

        // 订单
        $api->get('orders', 'OrdersController@index');
        $api->post('orders', 'OrdersController@store');
        $api->get('orders/statistics', 'OrdersController@statistics');
        $api->get('orders/{order}', 'OrdersController@show')->where('order', '[0-9]+');
        $api->put('orders/{order}/cancel', 'OrdersController@cancel')->where('order', '[0-9]+');
        $api->put('orders/{order}/pay', 'OrdersController@pay')->where('order', '[0-9]+');
        $api->put('orders/{order}/receive', 'OrdersController@receive')->where('order', '[0-9]+');
        // 退款订单
        $api->get('refundOrders', 'RefundOrdersController@index');
        $api->post('refundOrders/{order}/orderGoods/{orderGoods}', 'RefundOrdersController@store')->where('order', '[0-9]+')->where('orderGoods', '[0-9]+');
        $api->get('refundOrders/{order}', 'RefundOrdersController@show')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}/express', 'RefundOrdersController@express')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}/repeal', 'RefundOrdersController@repeal')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}', 'RefundOrdersController@update')->where('order', '[0-9]+');


        // 钱包
        $api->get('wallet', 'WalletsController@show');
        $api->get('wallet/logs', 'WalletsController@logs');

        // 代理
        $api->get('agency/configs', 'AgencyController@agencyConfigs');
        $api->post('agency/{agencyConfig}/recharge', 'AgencyController@recharge')->where('agencyConfig', '[0-9]+');
        $api->get('agency/rechargeThresholdOrders', 'AgencyController@rechargeThresholdOrders');
        $api->get('agency/rechargeThresholdOrders/{order}', 'AgencyController@rechargeThresholdOrder');

        //系统
        $api->get('system/configs', 'SystemController@getConfig');
        $api->get('expresses', 'ExpressesController@index');

    });
});