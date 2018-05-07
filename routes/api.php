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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::namespace('Api')->group(function () {
    // 在 "App\Http\Controllers\Api" 命名空间下的控制器
    Route::get('/index','IndexController@index');
    Route::get('/store-info','IndexController@storeInfo');
    Route::get('/banner','BannerController@index');
    Route::get('/special','NavController@special');
    Route::get('/goods_list','GoodsController@index');
    Route::post('/login','AuthenticateController@auto_login')->name('login');
    // 地区json
    Route::any('/addr_json', 'AddrjsonbController@index');
    Route::any('/addr_json/city', 'AddrjsonbController@city');
    Route::any('/addr_json/dorm', 'AddrjsonbController@dorm');
    Route::any('/create-city-data', 'CreateCityDataController@index');
    Route::any('/order-notify','PaymentController@notify');
    //商品详情api
    Route::get('/goods-detail', 'GoodsController@detail');

    // 需要用户信息的
    Route::middleware('auth:api')->group(function () {
        // 下单相关的
        Route::get('/default-addr','AddressesController@defaultAddr');
        Route::get('/addr-add-outside','AddressesController@outsideCreate');
        Route::get('/addr-add-inside','AddressesController@defaultAddr');
        Route::get('/addr-list','AddressesController@getList');
        Route::get('/addr-detail','AddressesController@getDetail');
        Route::get('/addr-delete','AddressesController@destroy');
        Route::get('/addr-update-default','AddressesController@updateDefault');

        Route::post('/addr-inside-add', 'AddressesController@insideCreate');

        // 创建订单
        Route::post('/order/create','PaymentController@orderCreate');
        Route::get('/order-mylist','OrderInfoController@getMyOrderList');
        Route::get('/order-statistics','OrderInfoController@getMyOrderStatistics');
        Route::get('/order-cancel','OrderInfoController@cancelOrder');
        // 付款相关
        Route::get('/order-pay','PaymentController@toPay');

        //配送员请求订单列表
        Route::get('/delivery-orders/list', 'OrdersController@deliveryOrderList');
        //确认接单接口
        Route::post('/delivery-orders/receive', 'OrdersController@receiveOrder');
        //确认配送接口
        Route::post('/delivery-orders/delivery', 'OrdersController@deliveryOrder');

        //获取订单详情
        Route::get('/order/detail', 'OrdersController@getOrderDetail');
        //确认收货
        Route::post('/order/complete', 'OrdersController@completeOrder');

    });


    //
    Route::get('/school-info/get-like-school-list', 'DormInfoController@getLikeSchoolList');
    Route::get('/dorm-info/get-like-dorm-list', 'DormInfoController@getLikeDormList');
    //获取学校列表
    Route::get('/addr-school-list', 'DormInfoController@getAllSchoolList');
    Route::get('/addr-dorm-list', 'DormInfoController@getDormListBySchoolID');
    //根据学校获取宿舍楼列表
    //新闻列表API
    Route::get('/news-list','NewsApiController@getList');
    Route::get('/news-list/{detail}','NewsApiController@show');
    //绑定手机API
    Route::get('/get-user-iphone','IphoneController@getUserIphone');
    Route::get('/save-user-iphone','IphoneController@saveUserIphone');
});

