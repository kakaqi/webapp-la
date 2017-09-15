<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Wxuser;

/**
 * 获取微信用户信息
 * Class WxUserController
 * @package App\Http\Controllers\Api
 */
class WxUserController extends Controller
{
    public function store(Request $request)
    {
        $user_info = $request->input('userInfo');
        $re = Wxuser::where('avatarUrl',$user_info['avatarUrl'])->first();
        $res = '';
        if( ! $re ){
            $user_info['nickName'] = json_encode($user_info['nickName']);
            $data = [
                'nickName' => isset( $user_info['nickName']) ? $user_info['nickName']  : '',
                'gender' => isset( $user_info['gender']) ? $user_info['gender']  : '',
                'avatarUrl' => isset( $user_info['avatarUrl']) ? $user_info['avatarUrl']  : '',
                'city' => isset( $user_info['city'])  ? $user_info['city']  : '',
                'province' => isset( $user_info['province']) ? $user_info['province']  : '',
                'country' => isset( $user_info['country']) ? $user_info['country']  : '',
                'language' => isset( $user_info['language']) ? $user_info['language']  : ''
            ];

            $res =  Wxuser::create($data);
        }

        return [
            'code' => 0,
            'text' => 'success',
            'result' => $res
        ];
    }
}
