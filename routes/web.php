<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
//    return view('welcome');
    return 'welcome to kakaqi.club';
});

Route::prefix('admin')->namespace('Admin')->group(function () {
    //后台登录页面
    Route::get('login', 'LoginController@index');
    Route::get('/   ', 'LoginController@index');
    //登录处理
    Route::post('authorization', 'LoginController@login');

    //后台验证
    Route::middleware('guest:web')->group(function () {
        //登出
        Route::get('logout', 'LoginController@logout');
        //后台首页
        Route::get('index', 'IndexController@index');
        //后台用户列表
        Route::get('users', 'UserController@index');
        //后台用户列表
        Route::get('wx/users', 'WxUserController@index');


        //语言列表
        Route::get('languages', 'LanguageController@index');
        //添加语言
        Route::post('languages', 'LanguageController@store');
        //保存语言
        Route::put('languages', 'LanguageController@update');
        //删除语言
        Route::delete('languages/{id}', 'LanguageController@del');
        //获取单条语言数据
        Route::get('languages/{id}', 'LanguageController@show');


    });

});
