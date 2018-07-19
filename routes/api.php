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


Route::namespace('Api')->group(function () {
    //Route::post('/login','AuthenticateController@auto_login')->name('login');
    //用户登录模块
    Route::post('/user/register', 'LoginController@register');
    Route::post('/user/login', 'LoginController@login')->name('login');
    Route::post('/line/searchLine', 'LineController@index');




    //学校模块
    //获取所有学校列表
    Route::post('/other/selectAllSchool', 'SchoolController@selectAllSchool');
    //按关键字搜索学校
    Route::post('/other/searchSchool', 'SchoolController@searchSchool');
    //获取当前学校所有站点
    Route::post('/other/getSite', 'SchoolController@getSite');
    //发送反馈
    Route::post('/other/sendFeedback','SchoolController@sendFeedback');
 Route::post('/user/getMyAddresses','UserInfoController@getMyAddresses');
    //bus模块

    //发布线路
    Route::post('/bus/newBus', 'BusController@newBus');
    //起点终点查询
    Route::post('/bus/searchBus1', 'BusController@searchBus1');
    //从早到晚查询
    Route::post('/bus/searchBus2', 'BusController@searchBus2');
    //起点查询
    Route::post('/bus/searchBus3', 'BusController@searchBus3');
    //发车时间段查询
    Route::post('/bus/searchBus4', 'BusController@searchBus4');

    //用户私密信息接口
    Route::middleware('auth:api')->group(function () {
        //乘客信息
        Route::post('/driver/getPassenger','UserInfoController@getPassenger');
        Route::post('/driver/passengerDetail','UserInfoController@passengerDetail');
        //改变bus状态
        Route::post('/bus/changeStatus','UserInfoController@changeStatus');
        //地址
       
        Route::post('/user/setDefaultAddress','UserInfoController@setDefaultAddress');
        Route::post('/user/saveAddress','UserInfoController@saveAddress');
        Route::post('/user/delAddress','UserInfoController@delAddress');
        //申请司机
        Route::post('/driver/newDriver','UserInfoController@newDriver');
        Route::post('/user/uploadCardImg','UserInfoController@uploadCardImg');
        Route::post('/user/ifSiJi','UserInfoController@ifSiJi');
    });
    Route::post('/user/isDriver','UserInfoController@ifDriver');

    //支付
    Route::post('/order/pay','TicketController@pay');
    Route::post('/order/payok','TicketController@payok');
    //获取我的车票
    Route::post('/user/getTicket', 'TicketController@getTicket');
    //确认收货
    Route::post('/order/confirmReceived', 'TicketController@confirmReceived');
});

/*
 *
 *     index: _api_root + 'default/index',
    user:{
       register:_api_root+'user/register',
       login:_api_root+'user/login',
       getUserInfo:_api_root+'user/getUserInfo',//查询与用户所有信息
       getMoblie:_api_root+'user/getMoblie',
       saveMoblie:_api_root+'user/saveMoblie',
       getTicket:_api_root+'user/getTicket',
       getMyAddresses:_api_root+'user/getMyAddresses',
       setDefaultAddress:_api_root+'user/setDefaultAddress',
       delAddress:_api_root+'user/delAddress',
       editAddress:_api_root+'user/editAddress',
       saveAddress:_api_root+'user/saveAddress',
       getMyCoupons:_api_root+'user/getMyCoupons',
    },
    driver: {
        newDriver:_api_root+'driver/newDriver',
    },
    bus:{
       newBus:_api_root+'bus/newBus',
       searchBus1:_api_root+'bus/searchBus1',
       searchBus2:_api_root+'bus/searchBus2',
       searchBus3:_api_root+'bus/searchBus3',
       searchBus4:_api_root+'bus/searchBus4',
       getBusInfo:_api_root+'bus/getBusInfo'
    },
    order:{
        pay:_api_root+'bus/pay'
    },
    other:{
        getSite:_api_root+'other/getSite',
        uploadImage:_api_root+'other/uploadImage',
        sendFeedback:_api_root+'other/sendFeedback',
        searchSchool:_api_root+'other/searchSchool',
        selectAllSchool:_api_root+'other/selectAllSchool'
    }
 *
 *
 *
 */