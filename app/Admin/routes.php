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
    $router->resource('orders', OrdersController::class);
    $router->resource('broadcasts', BroadcastsController::class);

    // 通用轮播路由
    $router->get('carouselAds/{ad}/items', 'CarouselAdsController@items')->where('ad', '[0-9]+');
    $router->post('carouselAds/{ad}/items', 'CarouselAdsController@storeItems')->where('ad', '[0-9]+');
    $router->get('carouselAds/{ad}/items/{item}/edit', 'CarouselAdsController@editItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->put('carouselAds/{ad}/items/{item}', 'CarouselAdsController@updateItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->delete('carouselAds/{ad}/items/{item}', 'CarouselAdsController@destroyItems')->where('ad', '[0-9]+')->where('item', '[0-9]+');
    $router->resource('carouselAds', CarouselAdsController::class);


    $router->get('testFrom', 'TestFormController@index');
    $router->post('testFrom', 'TestFormController@store');
});
