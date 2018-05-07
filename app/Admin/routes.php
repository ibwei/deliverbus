<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');

    //商品管理
    $router->resource('goods', GoodsController::class);
    //用户表管理
    $router->resource('user', UserController::class);
    //收货地址表管理
    $router->resource('addresses', AddressesController::class);
    //积分商品表管理
    $router->resource('point_good', Point_GoodsController::class);


});
