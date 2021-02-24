<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => 'admin::',
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users', UsersController::class);
    $router->get('categories/resetCache', 'CategoriesController@resetCache');
    $router->resource('categories', CategoriesController::class);
    $router->resource('goods', GoodsController::class);
    $router->resource('discountGoods', DiscountGoodsController::class);

    $router->resource('addresses', AddressesController::class);
    $router->put('orders/express', 'OrdersController@batchExpress');
    $router->get('orders/{order}/logistics', 'OrdersController@logistics')->where('order', '[0-9]+');
    $router->put('orders/{order}/express', 'OrdersController@express')->where('order', '[0-9]+');
    $router->put('orders/batch/{handle}', 'OrdersController@batch')->where('handle', 'receive');

    $router->put('orders/cancel', 'OrdersController@cancel');
    $router->resource('orders', OrdersController::class);

    $router->put('refundOrders/batch/refund', 'RefundOrdersController@batchRefund');
    $router->put('refundOrders/batch/{handle}', 'RefundOrdersController@batch')->where('handle', 'pass|reject|repeal|rejectRefund');
    $router->resource('refundOrders', RefundOrdersController::class);

    $router->resource('broadcasts', BroadcastsController::class);
    $router->resource('freightTemplates', FreightTemplatesController::class);


    // 通用轮播路由
    $router->get('carouselAds/{ad}/items', 'CarouselAdsController@items')->where('ad', '[0-9]+');
    $router->get('carouselAds/{ad}/resetCache', 'CarouselAdsController@resetCache')->where('ad', '[0-9]+');
    $router->post('carouselAds/{ad}/items', 'CarouselAdsController@storeItems')->where('ad', '[0-9]+');
    $router->get('carouselAds/{ad}/items/{item}/edit', 'CarouselAdsController@editItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->put('carouselAds/{ad}/items/{item}', 'CarouselAdsController@updateItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->delete('carouselAds/{ad}/items/{item}', 'CarouselAdsController@destroyItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->resource('carouselAds', CarouselAdsController::class);
    $router->resource('articles', ArticlesController::class);
    $router->resource('agencyConfigs', AgencyConfigsController::class);

    // 系统
    $router->get('system', 'SystemController@index');

    $router->resource('memberLevels', MemberLevelsController::class);

    $router->get('memberRechargeActivities/{activity}/coupons', 'MemberRechargeActivitiesController@coupons')->where('activity', '[0-9]+');
    $router->post('memberRechargeActivities/{activity}/coupons', 'MemberRechargeActivitiesController@storeCoupons')->where('activity', '[0-9]+');
    $router->delete('memberRechargeActivities/{activity}/coupons/{coupon_id}', 'MemberRechargeActivitiesController@destroyCoupons')->where('activity', '[0-9]+')->where('coupon_id', '[0-9]+');
    $router->resource('memberRechargeActivities', MemberRechargeActivitiesController::class);
    $router->resource('memberRechargeOrders', MemberRechargeOrdersController::class);
    $router->put('prizes/{prize}/updateQuantity', 'PrizesController@updateQuantity')->where('prize', '[0-9]+');
    $router->resource('prizes', PrizesController::class);
    $router->get('lotteryRecords/{record}/logistics', 'LotteryRecordsController@logistics')->where('record', '[0-9]+');
    $router->put('lotteryRecords/{record}/express', 'LotteryRecordsController@express')->where('record', '[0-9]+');
    $router->resource('lotteryRecords', LotteryRecordsController::class);
    $router->resource('lotteryChances', LotteryChancesController::class);

    $router->put('cofferWithdrawals/batch/{handle}', 'CofferWithdrawalsController@batch')->where('handle', 'pass|reject');
    $router->resource('cofferWithdrawals', CofferWithdrawalsController::class);
    $router->resource('coupons', CouponsController::class);
    $router->put('userHasCoupons/batch/{handle}', 'UserHasCouponsController@batch')->where('handle', 'freeze|unfreeze');
    $router->resource('userHasCoupons', UserHasCouponsController::class);
    $router->resource('cofferLogs', CofferLogsController::class);
    $router->resource('walletLogs', WalletLogsController::class);

    $router->get('testFrom', 'TestFormController@index');
    $router->post('testFrom', 'TestFormController@store');

    $router->get('stores/resetCache', 'StoresController@resetCache');
    $router->resource('stores', StoresController::class);

    $router->resource('walletActivities', WalletActivitiesController::class);
    $router->resource('rechargeThresholdOrders', RechargeThresholdOrdersController::class);

    $router->resource('presses', PressesController::class);
    $router->resource('authors', AuthorsController::class);

});
