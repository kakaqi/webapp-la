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
    //翻译接口
    Route::get('translate', 'TranslateController@index');
    //语音上传&语音转文字&文字翻译
    Route::post('file/store', 'FileUploadController@upload');
    //获取语言列表
    Route::post('languages', 'LanguageController@index');
    //保存微信用户wxUser
    Route::post('wxUser', 'WxUserController@store');
    Route::get('speech', 'GoogleSpeechController@index');
    Route::get('baidu/speech', 'FileUploadController@getBaiduVoice');

});
