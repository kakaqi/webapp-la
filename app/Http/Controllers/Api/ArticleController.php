<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Redis;

class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $redis_key = 'article_'.date('Y-m-d');
        if( ! $data = Redis::get($redis_key) ) {
            $data = Article::select(
                '*',
                \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
                \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
                \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
            )->where('dateline',date('Y-m-d'))->first();
            if( ! $data ) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://open.iciba.com/dsapi/');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                $response = curl_exec($ch);
                if(curl_errno($ch))
                {
                    print curl_error($ch);
                }
                curl_close($ch);
                $response = json_decode($response, true);

                $picture_path = 'article_img/';
                $pre_name = date('Y-m-d') .'.jpg';

                $cmd = '/usr/bin/wget  -O  '.$picture_path.$pre_name.' "'.$response['picture'].'"';
                exec($cmd, $out);
                $cmd = '/usr/bin/wget  -O  '.$picture_path.'big_'.$pre_name.' "'.$response['picture2'].'"';
                exec($cmd, $out);
                $cmd = '/usr/bin/wget  -O  '.$picture_path.'fenxiang_'.$pre_name.' "'.$response['fenxiang_img'].'"';
                exec($cmd, $out);
                $response['picture'] = $picture_path.$pre_name;
                $response['picture2'] = $picture_path.'big_'.$pre_name;
                $response['fenxiang_img'] = $picture_path.'fenxiang_'.$pre_name;

                unset($response['sid'],$response['tts'],$response['caption'],$response['s_pv'],$response['sp_pv'],$response['tags']);
                Article::create($response);

                $data = Article::select(
                    '*',
                    \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
                    \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
                    \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
                )->where('dateline',date('Y-m-d'))->first();
            }
            Redis::set($redis_key, json_encode($data));
            Redis::expire($redis_key,24*60*60);//设置几秒后过期
        } else {
            $data = json_decode($data, true);
        }

        return $data;


    }
}
