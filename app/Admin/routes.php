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
    $router->resource('categories', CategoriesController::class);
    $router->resource('goods', GoodsController::class);
    $router->resource('addresses', AddressesController::class);
    $router->put('orders/express', 'OrdersController@batchExpress');
    $router->put('orders/{order}/express', 'OrdersController@express')->where('order', '[0-9]+');
    $router->put('orders/cancel', 'OrdersController@cancel');
    $router->resource('orders', OrdersController::class);

    $router->put('refundOrders/batch/refund', 'RefundOrdersController@batchRefund');
    $router->put('refundOrders/batch/{handle}', 'RefundOrdersController@batch')->where('handle', 'pass|reject|repeal|rejectRefund');
    $router->resource('refundOrders', RefundOrdersController::class);

    $router->resource('broadcasts', BroadcastsController::class);
    $router->resource('freightTemplates', FreightTemplatesController::class);


    // 通用轮播路由
    $router->get('carouselAds/{ad}/items', 'CarouselAdsController@items')->where('ad', '[0-9]+');
    $router->post('carouselAds/{ad}/items', 'CarouselAdsController@storeItems')->where('ad', '[0-9]+');
    $router->get('carouselAds/{ad}/items/{item}/edit', 'CarouselAdsController@editItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->put('carouselAds/{ad}/items/{item}', 'CarouselAdsController@updateItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->delete('carouselAds/{ad}/items/{item}', 'CarouselAdsController@destroyItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->resource('carouselAds', CarouselAdsController::class);
    $router->resource('articles', ArticlesController::class);
    $router->resource('agencyConfigs', AgencyConfigsController::class);

    // 系统
    $router->get('system', 'SystemController@index');


    $router->get('testFrom', 'TestFormController@index');
    $router->post('testFrom', 'TestFormController@store');
});
