<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $date = $request->input('date','');

        if( $date ) {
            $redis_key = 'article_'.$date;
        } else {
            $date = date('Y-m-d');
            $redis_key = 'article_'.$date;
        }

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

                $picture_path = 'article_img/';
                $pre_name = $date .'.jpg';

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
                )->where('dateline',$date)->first();
            }
            Redis::set($redis_key, json_encode($data));
            Redis::expire($redis_key,24*60*60*7);//设置几秒后过期
        } else {
            $data = json_decode($data, true);
        }

        return [
            'code' => 0,
            'text' => 'success',
            'result' => $data
        ];

    }

    /**
     * 获取当前周
     * @return array
     */
    public function getCurWeek()
    {


        $date=date('Y-m-d');  //当前日期
        $first=0; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6
        $day = date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days'));
        $sunday = date('d',strtotime("$date -".($w ? $w - $first : 6).' days'));
        $monday = date('d',strtotime("$day +1 days"));
        $tuesday = date('d',strtotime("$day +2 days"));
        $wednesday = date('d',strtotime("$day +3 days"));
        $thursday = date('d',strtotime("$day +4 days"));
        $friday = date('d',strtotime("$day +5 days"));
        $saturday = date('d',strtotime("$day +6 days"));
        $cur_w_d = Carbon::now()->dayOfWeek;
        $cur_week = [
            [
                'id' => 0,
                'name' => 'Sun',
                'value' => $sunday,
                'date' => date('Y-m-').$sunday,
                'is_cur' => $cur_w_d == 0 ? 1 : 0,
            ],
            [
                'id' => 1,
                'name' => 'Mon',
                'value' => $monday,
                'date' => date('Y-m-').$monday,
                'is_cur' => $cur_w_d == 1 ? 1 : 0,
            ],
            [
                'id' => 2,
                'name' => 'Tue',
                'value' => $tuesday,
                'date' => date('Y-m-').$tuesday,
                'is_cur' => $cur_w_d == 2 ? 1 : 0,
            ],
            [
                'id' => 3,
                'name' => 'Wen',
                'value' => $wednesday,
                'date' => date('Y-m-').$wednesday,
                'is_cur' => $cur_w_d == 3 ? 1 : 0,
            ],
            [
                'id' => 4,
                'name' => 'Thu',
                'value' => $thursday,
                'date' => date('Y-m-').$thursday,
                'is_cur' => $cur_w_d == 4 ? 1 : 0,
            ],
            [
                'id' => 5,
                'name' => 'Fri',
                'value' => $friday,
                'date' => date('Y-m-').$friday,
                'is_cur' => $cur_w_d == 5 ? 1 : 0,
            ],
            [
                'id' => 6,
                'name' => 'Sat',
                'value' => $saturday,
                'date' => date('Y-m-').$saturday,
                'is_cur' => $cur_w_d == 6 ? 1 : 0,
            ],
        ];
        return [
            'code' => 0,
            'text' => 'success',
            'result' => $cur_week
        ];
    }
}
