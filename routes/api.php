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


    // //forTest
    // Route::post('/test/upload','testController@testupload');
    // Route::post('/user/login', 'testController@test13');
    // Route::post('/user/logout', 'testController@test14');
    // Route::post('/user/getCode', 'testController@test17');
    // Route::post('/user/validateCode', 'testController@test18');
    // Route::post('/project/projectData', 'testController@test1');
    // Route::post('/project/allProjectName', 'testController@test15');
    // Route::post('/project/editProject', 'testController@test2');
    // Route::post('/project/getAllSite', 'testController@test3');
    // Route::post('/project/getAllType', 'testController@test4');
    // Route::post('/project/newProjectType', 'testController@test8');
    // Route::post('/project/newProject', 'testController@test5');
    // Route::post('/check/searchCheck', 'testController@test6');
    // Route::post('/check/checkHistory', 'testController@test7');
    // Route::post('/check/aCheck', 'testController@test9');
    // Route::post('/check/bCheck', 'testController@test10');
    // Route::post('/check/getCheckReport', 'testController@test11');
    // Route::post('/project/searchProject', 'testController@test16');


    Route::post('/login', 'AuthenticateController@auto_login')->name('login');
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
    Route::post('/other/sendFeedback', 'SchoolController@sendFeedback');
    Route::post('/user/getMyAddresses', 'UserInfoController@getMyAddresses');
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
        Route::post('/driver/getPassenger', 'UserInfoController@getPassenger');
        Route::post('/driver/passengerDetail', 'UserInfoController@passengerDetail');
        //改变bus状态
        Route::post('/bus/changeStatus', 'UserInfoController@changeStatus');
        //地址

        Route::post('/user/setDefaultAddress', 'UserInfoController@setDefaultAddress');
        Route::post('/user/saveAddress', 'UserInfoController@saveAddress');
        Route::post('/user/delAddress', 'UserInfoController@delAddress');
        //申请司机
        Route::post('/driver/newDriver', 'UserInfoController@newDriver');
       
        Route::post('/user/ifSiJi', 'UserInfoController@ifSiJi');
    });
    Route::post('/user/uploadCardImg', 'UserInfoController@uploadCardImg');
    Route::post('/user/isDriver', 'UserInfoController@ifDriver');

    //支付
    Route::post('/order/pay', 'TicketController@pay');
    Route::post('/user/saveFormId', 'TicketController@saveFormId');
    Route::post('/order/payok', 'TicketController@payok');
    //获取我的车票
    Route::post('/user/getTicket', 'TicketController@getTicket');
    //确认收货
    Route::post('/order/confirmReceived', 'TicketController@confirmReceived');
});
