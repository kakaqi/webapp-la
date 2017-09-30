<?php
/**
 * 微信用户控制器
 */
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wxuser;
use Xiang\WechatApp\Decode\WXBizDataCrypt;
use Illuminate\Support\Facades\Redis;

/**
 * 获取微信用户信息
 * Class WxUserController
 * @package App\Http\Controllers\Api
 */
class WxUserController extends Controller
{
    /**保存用户信息
     * @param Request $request
     * @return array
     */
    public function store(Request $request)
    {
        $openId = $request->input('openId');
        $user_info = $request->input('userInfo');
        $msg = json_encode(['openId' => $openId,'user_info' => $user_info]);
        RabbitmqController::publishMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE1'),env('MQ_ROUTING_KEY1'),$msg]);
        return [
            'code' => 0,
            'text' => 'success',
            'result' => ''
        ];
    }

    /**y用户登录根据code换取sessionKey信息
     * @param Request $request
     * @return array
     */
    public function userLogin(Request $request)
    {
        $code = $request->input('code');
        $appid = env('APPID');
        $secret = env('APPSECRET');
        $grant_type='authorization_code';
        $url='https://api.weixin.qq.com/sns/jscode2session';
        $url= sprintf("%s?appid=%s&secret=%s&js_code=%s&grant_type=%",$url,$appid,$secret,$code,$grant_type);
        $data = file_get_contents($url);
        $user_data=json_decode($data,true);
//        $redis_key = sha1($user_data['openid'].'csj');
//        Redis::set($redis_key, $data);
        return [
            'code' => 0,
            'text' => 'success',
            'result' => $user_data,
        ];
    }

    /**
     * 获取用户解密数据详情信息
     * @param Request $request
     * @return array
     */
    public function getUserInfo(Request $request)
    {
        $appid = env('APPID');
        $redis_key = $request->input('key','');
        $user_data = Redis::get($redis_key);
        if ( !$user_data ) {
            return [
                'code' => 400,
                'text' =>  'session_key error',
                'result' => ''
            ];
        }
        $user_data = json_decode($user_data, true);
        $sessionKey = $user_data['session_key'];

        $encryptedData = $request->input('encryptedData');
        $iv = $request->input('iv');
        $pc = new WXBizDataCrypt($appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        return [
            'code' => $errCode,
            'text' =>  $errCode == 0 ? 'success' : 'error',
            'result' => json_decode($data, true)
        ];
    }

    public function getAccessToken() {

    }
}
