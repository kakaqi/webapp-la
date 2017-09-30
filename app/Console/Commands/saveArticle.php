<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Api\RabbitmqController as rabbitmq;

/**
 * 保存文章的消费队列
 *  命令 nohup php artisan save_article >save_article.txt 2>&1 &
 * Class wxUserTranslation
 * @package App\Console\Commands
 */
class saveArticle extends Command
{
    protected $signature = 'save_article';
    protected $description = 'rabbitmq save article into db';
    public function __construct()
    {
        parent::__construct();

    }
    public function handle()
    {
        rabbitmq::getMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE3')],['App\Console\Commands\saveArticle','processMessage']);
    }

    //拉取消息回调参数
    public static function processMessage($envelope)
    {

        $msg = $envelope->getBody();//获取消息队列消息

        $enMeg = json_decode($msg,true);
        self::saveArticle($enMeg);

    }

    /**
     * 处理微信用户翻译
     * @param $data
     */
    private static function saveArticle($data)
    {
        $picture_path = 'public/article_img/';
        $pre_name = $data['date'] .'.jpg';
        $cmd1 = '/usr/bin/wget  -O  '.$picture_path.'big_'.$pre_name.' "'.$data['picture2'].'"';
        $cmd2 = '/usr/bin/wget  -O  '.$picture_path.$pre_name.' "'.$data['picture'].'"';
        $cmd3 = '/usr/bin/wget  -O  '.$picture_path.'fenxiang_'.$pre_name.' "'.$data['fenxiang_img'].'"';
        $cmd = $cmd1.' && '.$cmd2.' && '.$cmd3;
        exec($cmd, $out);
        echo "save article picture".date('Y-m-d H:i:s');
        echo "\n";
    }

}