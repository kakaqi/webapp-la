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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

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
    Route::post('text/voice', 'TranslateController@baiduText2Voice');
    Route::delete('text/voice', 'TranslateController@deleteAudio');
    Route::post('google/text/voice', 'TranslateController@translateTts');
    Route::post('baidu/voice', 'FileUploadController@getBaiduVoice');
    //每日一句
    Route::get('day/article', 'ArticleController@index');
    //获取当前周
    Route::get('weeks', 'ArticleController@getCurWeek');
    //点赞更新
    Route::post('article/love/{id}', 'ArticleController@love');
    //用户信息解密
//    Route::post('user/decrypt', 'ArticleController@love');
    //微信用户登录认证
    Route::post('user/auth', 'WxUserController@userLogin');
    Route::post('user/decrypt', 'WxUserController@getUserInfo');

    //文章评论
    Route::post('article/comment/{id}', 'ArticleController@comment');
    //获取评论
    Route::get('article/comment/{id}', 'ArticleController@getCommet');

    //评论不显示
    Route::post('article/comment/status/{id}', 'ArticleController@hide');
    //文章分享统计
    Route::post('article/share/{id}', 'ArticleController@share');

    //有道查词
    Route::get('youdao', 'TranslateController@youdao');

    //获取用户服务器的翻译
    Route::post('lishi', 'TranslateController@getUserTranslation');

    //获取用户本地的翻译历史
    Route::post('lishi/local', 'TranslateController@getUserLocalTranslation');

    //删除历史翻译
    Route::delete('lishi/{id}', 'TranslateController@deleteTranslation');
    //清除历史翻译
    Route::delete('lishi', 'TranslateController@removeTranslation');

    Route::get('pay/open', 'WxUserController@openPayPage');
    Route::get('pay/save', 'WxUserController@savePayImage');
    //腾讯语音识别接口
    Route::post('tengxun/voice', 'TranslateController@tengxunVoiveToText');
    Route::get('tengxun/voice/callback', 'TranslateController@tengxunCallback');
});
