<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    //用户
    $router->resource('user', UserController::class);
    //收货地址表管理
    $router->resource('addresses', AddressesController::class);
    //学校
    $router->resource('school', SchoolController::class);
    //站点
    $router->resource('site', SiteController::class);
    //司机
    $router->resource('driver', DriverController::class);
    //bus
    $router->resource('bus', BusController::class);

});
