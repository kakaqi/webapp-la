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
           $res =  Wxuser::create($user_info);
        }

        return [
            'code' => 0,
            'text' => 'success',
            'result' => $res
        ];
    }
}