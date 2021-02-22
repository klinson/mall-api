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
        $api->get('categories/top', 'CategoriesController@top');
        $api->get('goods', 'GoodsController@index');
        $api->get('goods/{goods}', 'GoodsController@show')->where('goods', '[0-9]+');;
        $api->get('goods/{goods}/qrcode', 'GoodsController@qrcode')->where('goods', '[0-9]+');;
        $api->get('goodsSpecifications', 'GoodsSpecificationsController@index');


        $api->get('discountGoods', 'DiscountGoodsController@index');
        $api->get('discountGoods/{goods}', 'DiscountGoodsController@show')->where('goods', '[0-9]+');;
        $api->get('discountGoods/{goods}/qrcode', 'DiscountGoodsController@qrcode')->where('goods', '[0-9]+');;


        $api->get('broadcast', 'BroadcastsController@show');
        $api->get('carouselAd', 'CarouselAdsController@show');
        $api->post('files', 'FilesController@upload');
        $api->get('articles/{article}', 'ArticlesController@show')->where('article', '[0-9]+');

        // 微信回调路由
        $api->post('wechat/OrderPaidNotify', 'WechatController@OrderPaidNotify');
        $api->post('wechat/RechargeThresholdOrderPaidNotify', 'WechatController@RechargeThresholdOrderPaidNotify');
        $api->post('wechat/MemberRechargeOrderPaidNotify', 'WechatController@MemberRechargeOrderPaidNotify');

        //系统
        $api->get('system/configs', 'SystemController@getConfig');
        $api->get('expresses', 'ExpressesController@index');

        // 会员制查询
        $api->get('memberLevels', 'MemberLevelsController@index');
        $api->get('memberLevels/max', 'MemberLevelsController@max');
        $api->get('memberLevels/{memberLevel}', 'MemberLevelsController@show')->where('memberLevel', '[0-9]+');
        $api->get('memberLevels/activities', 'MemberRechargeActivitiesController@index');
        $api->get('memberLevels/activities/inviteRank', 'MemberRechargeActivitiesController@inviteRank');
        $api->get('memberLevels/activities/{activity}', 'MemberRechargeActivitiesController@show')->where('activity', '[0-9]+');
        $api->get('memberLevels/activities/{activity}/qrcode', 'MemberRechargeActivitiesController@qrcode')->where('activity', '[0-9]+');

        // 抽奖
        $api->get('prizes', 'LotteryController@prizes');
        $api->post('lottery/presentChance', 'LotteryController@presentChance');
        $api->get('lottery/records/recent', 'LotteryRecordsController@recent');

        // 优惠券
        $api->get('coupons', 'CouponsController@index');
        $api->get('coupons/{coupon}', 'CouponsController@show')->where('coupon', '[0-9]+');
        $api->post('coupons/{coupon}/present', 'CouponsController@present')->where('coupon', '[0-9]+');

        // 门店
        $api->get('stores', 'StoresController@index');

        $api->get('wallet/activities', 'WalletsController@activities');

    });

    // 需要登录的路由
    $api->group([
        'middleware' => 'refresh.token'
    ], function ($api) {
        // 用户
        $api->get('user', 'UserController@show');
        $api->put('user', 'UserController@update');
        $api->get('addresses/default', 'AddressesController@getDefault');
        $api->resource('addresses', 'AddressesController', ['only' => ['index', 'update', 'store', 'destroy', 'show']]);

        // 商品收藏
        $api->get('goods/favours', 'GoodsController@favours');
        $api->post('goods/{goods_id}/favour', 'GoodsController@favour')->where('goods_id', '[0-9]+');
        $api->delete('goods/{goods_id}/favour', 'GoodsController@unfavour')->where('goods_id', '[0-9]+');
        $api->delete('goods/unfavour', 'GoodsController@unfavourByIds');


        // 购物车
        $api->resource('shoppingCarts', 'ShoppingCartsController', ['only' => ['index', 'update', 'store', 'destroy']]);
        $api->delete('shoppingCarts', 'ShoppingCartsController@clearAll');
        $api->delete('shoppingCartsByIds', 'ShoppingCartsController@deleteByIds');

        // 订单
        $api->get('orders', 'OrdersController@index');
        $api->post('orders', 'OrdersController@store');
        $api->get('orders/statistics', 'OrdersController@statistics');
        $api->get('orders/{order}', 'OrdersController@show')->where('order', '[0-9]+');
        $api->put('orders/{order}/cancel', 'OrdersController@cancel')->where('order', '[0-9]+');
        $api->put('orders/{order}/pay', 'OrdersController@pay')->where('order', '[0-9]+');
        $api->put('orders/{order}/receive', 'OrdersController@receive')->where('order', '[0-9]+');
        $api->get('orders/{order}/logistics', 'OrdersController@logistics')->where('order', '[0-9]+');

        // 退款订单
        $api->get('refundOrders', 'RefundOrdersController@index');
        $api->post('refundOrders/{order}/orderGoods/{orderGoods}', 'RefundOrdersController@store')->where('order', '[0-9]+')->where('orderGoods', '[0-9]+');
        $api->get('refundOrders/{order}', 'RefundOrdersController@show')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}/express', 'RefundOrdersController@express')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}/repeal', 'RefundOrdersController@repeal')->where('order', '[0-9]+');
        $api->put('refundOrders/{order}', 'RefundOrdersController@update')->where('order', '[0-9]+');
        $api->get('refundOrders/{order}/logistics', 'RefundOrdersController@logistics')->where('order', '[0-9]+');


        // 钱包
        $api->get('wallet', 'WalletsController@show');
        $api->get('wallet/logs', 'WalletsController@logs');
        $api->post('wallet/recharge', 'WalletsController@recharge');

        // 代理
        $api->get('agency/configs', 'AgencyController@agencyConfigs');
        $api->post('agency/{agencyConfig}/recharge', 'AgencyController@recharge')->where('agencyConfig', '[0-9]+');
        $api->get('agency/rechargeThresholdOrders', 'AgencyController@rechargeThresholdOrders');
        $api->get('agency/rechargeThresholdOrders/{order}', 'AgencyController@rechargeThresholdOrder');
        $api->get('agency/qrcode', 'AgencyController@qrcode');
        // 代理金库
        $api->get('coffer', 'CoffersController@show');
        $api->get('coffer/logs', 'CoffersController@logs');
        $api->post('coffer/withdraw', 'CoffersController@withdraw');
        $api->get('coffer/withdrawals', 'CoffersController@withdrawals');
        $api->get('coffer/withdrawals/{withdrawal}', 'CoffersController@withdrawal')->where('withdrawal', '[0-9]+');

        // 会员
        $api->get('memberRechargeOrders', 'MemberRechargeOrdersController@index');
        $api->post('memberRechargeOrders', 'MemberRechargeOrdersController@store');
        $api->get('memberRechargeOrders/{order}', 'MemberRechargeOrdersController@show')->where('order', '[0-9]+');
        $api->get('user/memberLevels', 'MemberLevelsController@mine');

        // 抽奖
        $api->post('lottery', 'LotteryController@lottery');
        $api->get('user/lotteryChanceCount', 'LotteryController@myChanceCount');
        $api->get('lottery/records', 'LotteryRecordsController@index');
        $api->get('lottery/records/{record}', 'LotteryRecordsController@show')->where('record', '[0-9]+');
        $api->put('lottery/records', 'LotteryRecordsController@setAddress');
        $api->get('lottery/records/{record}/logistics', 'LotteryRecordsController@logistics')->where('record', '[0-9]+');

        // 优惠券
        $api->get('user/coupons', 'CouponsController@myCoupons');
        $api->post('coupons/check', 'CouponsController@checkUserCoupons');

    });
});