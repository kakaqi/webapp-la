<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\RabbitmqController as rabbitmq;
use App\Models\Wxuser;

/**
 *  命令 nohup php artisan wx_users >wx_user.txt 2>&1 &
 * Class wxUsers
 * @package App\Console\Commands
 */
class wxUsers extends Command
{
    protected $signature = 'wx_users';
    protected $description = 'rabbitmq get wx_users';
    public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {
        rabbitmq::getMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE1')],['App\Console\Commands\wxUsers','processMessage']);
    }

    //拉取消息回调参数
    public static function processMessage($envelope,$que)
    {
        $msg = $envelope->getBody();//获取消息队列消息
        $enMeg = json_decode($msg,true);
        self::wxUser($enMeg);

    }

    /**
     * 处理微信用户
     * @param $data
     */
    private static function wxUser($data)
    {

        $openId = $data['openId'];
        $user_info = $data['user_info'];
        if( ! $openId ) {
            echo 'openId为空:'.date('Y-m-d H:i:s');
            echo "\n";
            return ;
        }
        $user_data = [];
        if( $user_info ) {
            $user_data = [
                'nickName' => isset( $user_info['nickName']) ? json_encode($user_info['nickName'])  : '',
                'gender' => isset( $user_info['gender']) ? $user_info['gender']  : '',
                'avatarUrl' => isset( $user_info['avatarUrl']) ? $user_info['avatarUrl']  : '',
                'city' => isset( $user_info['city'])  ? $user_info['city']  : '',
                'province' => isset( $user_info['province']) ? $user_info['province']  : '',
                'country' => isset( $user_info['country']) ? $user_info['country']  : '',
                'language' => isset( $user_info['language']) ? $user_info['language']  : ''
            ];

        }

        if( ! $re = Wxuser::where('openId',$openId)->first()) {
            $user_data['openId'] = $openId;
            Wxuser::create($user_data);
            echo '添加用户成功:'.date('Y-m-d H:i:s');
            echo "\n";
        } else {
            $user_data && Wxuser::where('openId', $openId)->update($user_data);
            echo '更新用户成功:'.date('Y-m-d H:i:s');
            echo "\n";
        }

    }

}