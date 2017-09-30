<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\RabbitmqController as rabbitmq;
use App\Models\Article;
use App\Models\Wxuser;
use Illuminate\Support\Facades\Redis;

/**
 *  命令 nohup php artisan articleLove >articleLove.txt 2>&1 &
 * Class wxUserTranslation
 * @package App\Console\Commands
 */
class articleLove extends Command
{
    protected $signature = 'articleLove';
    protected $description = 'rabbitmq get wx_users articleLove';
    public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {
        rabbitmq::getMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE4')],['App\Console\Commands\articleLove','processMessage']);
    }

    //拉取消息回调参数
    public static function processMessage($envelope)
    {

        $msg = $envelope->getBody();//获取消息队列消息
        $enMeg = json_decode($msg,true);
        self::articleLove($enMeg);

    }

    /**
     * 处理微信用户翻译
     * @param $data
     */
    private static function articleLove($data)
    {
        $user = Wxuser::where('openId',$data['openId'])->first();
        $is_love = \DB::table('user_article_love')->where(['user_id' => $user->id, 'article_id' => $data['id']])->first();
        if( $is_love ) {
            echo "文章id:".$data['id']."已经点赞过了".date('Y-m-d H:i:s');
            echo "\n";
            return;
        }

        Article::where('id',$data['id'])->increment('love');
        \DB::table('user_article_love')->insert(['user_id' => $user->id, 'article_id' => $data['id']]);

        $res = Article::select(
            '*',
            \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
            \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
            \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
        )->find($data['id']);
        $res['is_love'] = 'on';
        $redis_key = 'article_'.$res['dateline'];
        Redis::set($redis_key, json_encode($res));

        echo  "用户:".$data['openId']."文章id:".$data['id']."点赞成功".date('Y-m-d H:i:s');
        echo "\n";
    }

}