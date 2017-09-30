<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\RabbitmqController as rabbitmq;
use App\Models\Translation;

/**
 *  命令 nohup php artisan wx_users_translation >wx_users_translation.txt 2>&1 &
 * Class wxUserTranslation
 * @package App\Console\Commands
 */
class wxUserTranslation extends Command
{
    protected $signature = 'wx_users_translation';
    protected $description = 'rabbitmq get wx_users translation';
    public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {
        rabbitmq::getMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE2')],['App\Console\Commands\wxUserTranslation','processMessage']);
    }

    //拉取消息回调参数
    public static function processMessage($envelope)
    {

        $msg = $envelope->getBody();//获取消息队列消息
        $enMeg = json_decode($msg,true);
        self::wxUserTranslate($enMeg);

    }

    /**
     * 处理微信用户翻译
     * @param $data
     */
    private static function wxUserTranslate($data)
    {
        $where = [
            'openId' => $data['openId'],
            'source_lan' => $data['source_lan'],
            'target_lan' => $data['target_lan'],
            'source_con' => $data['source_con'],
        ];
        $re = Translation::where($where)->first();
        if( $re ) {
            Translation::where($where)->update($data);
            echo '更新用户'.$data['openId'].'翻译成功:'.date('Y-m-d H:i:s');
            echo "\n";
        } else {
            Translation::create($data);
            echo '添加用户'.$data['openId'].'翻译成功:'.date('Y-m-d H:i:s');
            echo "\n";
        }

    }

}