<?php
/**
 * Created by PhpStorm.
 * User: chensongjian
 * Date: 2017/7/13
 * Time: 13:34
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Article;
use App\Models\Wxuser;
use  Yankewei\LaravelSensitive\Facades\Sensitive;
use Illuminate\Support\Facades\Redis;

class dayArticle extends Command
{
    protected $signature = 'dayArticle';
    protected $description = '这是每天获取文章定时任务，每天凌晨跑一次';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {

        $date = date('Y-m-d');
        $redis_key = 'article_'.$date;
        if( ! $data = Redis::get($redis_key) ) {
            $data = Article::select(
                '*',
                \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
                \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
                \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
            )->where('dateline',$date)->first();

            if( ! $data ) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://open.iciba.com/dsapi/?date='.$date);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $response = curl_exec($ch);
                if(curl_errno($ch))
                {
                    print curl_error($ch);
                }
                curl_close($ch);

                $response = json_decode($response, true);

                $picture_path = '/www/webapp-la/public/article_img/';
                $picture_path2 = 'article_img/';
                $pre_name = $date .'.jpg';

                $cmd = '/usr/bin/wget  -O  '.$picture_path.$pre_name.' "'.$response['picture'].'"';
                exec($cmd, $out);
                $cmd = '/usr/bin/wget  -O  '.$picture_path.'big_'.$pre_name.' "'.$response['picture2'].'"';
                exec($cmd, $out);
                $cmd = '/usr/bin/wget  -O  '.$picture_path.'fenxiang_'.$pre_name.' "'.$response['fenxiang_img'].'"';
                exec($cmd, $out);
                $response['picture'] = $picture_path2.$pre_name;
                $response['picture2'] = $picture_path2.'big_'.$pre_name;
                $response['fenxiang_img'] = $picture_path2.'fenxiang_'.$pre_name;

                unset($response['sid'],$response['tts'],$response['caption'],$response['s_pv'],$response['sp_pv'],$response['tags'],$response['love']);
                Article::create($response);


                $data = Article::select(
                    '*',
                    \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
                    \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
                    \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
                )->where('dateline',$date)->first();
                //默认添加一条评论数据
                $default_comment_data = [
                    'pid' => 0,
                    'article_id' => $data['id'],
                    'user_id' => 1,
                    'user_name' => '多小编',
                    'user_img' => '',
                    'reply_id' => 0,
                    'replay_name' => '',
                    'content' => json_encode(preg_replace('/词霸小编：/','',$data['translation'])),
                    'add_time' => date('Y-m-d H:i:s')
                ];
                \DB::table('article_comments')->insert($default_comment_data);

            }

            Redis::set($redis_key, json_encode($data));
            Redis::expire($redis_key,24*60*60*7);//设置几秒后过期
            $this->info('数据请求成功！'.date('Y-m-d H:i:s'));
        } else {
            $this->info($redis_key.'redis数据已经存在，不需要请求接口');
        }


    }
}