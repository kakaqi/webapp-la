<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;
use App\Models\Wxuser;
use  Yankewei\LaravelSensitive\Facades\Sensitive;
class ArticleController extends Controller
{
    public function index(Request $request)
    {

        $date = $request->input('date','');
        $openId = $request->input('openId');
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

                unset($response['sid'],$response['tts'],$response['caption'],$response['s_pv'],$response['sp_pv'],$response['tags'],$response['love']);
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
        $data['translation'] = preg_replace('/词霸小编/','♪♪♪♪',$data['translation']);

        if(isset($openId) && !empty($openId)) {
            $user = Wxuser::where('openId',$openId)->first();
            $is_love = \DB::table('user_article_love')->where(['user_id' => $user->id, 'article_id' => $data['id']])->first();
            $data['is_love'] = $is_love ? 'on' : '';
        } else {
            $data['is_love'] = '';
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
    public function getCurWeek(Request $request)
    {
        $now_date = date('Y-m-d');
        $date = $request->input('date','');
        if( !$date) {
            $date = $now_date;  //当前日期
        }

        $first=0; //$first =1 表示每周星期一为开始日期 0表示每周日为开始日期
        $w=date('w',strtotime($date));  //获取当前周的第几天 周日是 0 周一到周六是 1 - 6

        $day = date('Y-m-d',strtotime("$date -".$w.' days'));
        $sunday = date('d',strtotime("$date -".$w .' days'));
        $monday = date('d',strtotime("$day +1 days"));
        $tuesday = date('d',strtotime("$day +2 days"));
        $wednesday = date('d',strtotime("$day +3 days"));
        $thursday = date('d',strtotime("$day +4 days"));
        $friday = date('d',strtotime("$day +5 days"));
        $saturday = date('d',strtotime("$day +6 days"));

//        if( $request->input('date') == date('Y-m-d')) {
//            $cur_w_d = Carbon::now()->dayOfWeek;
//        } else {
//
//        }


        $cur_week = [
            [
                'id' => 0,
                'name' => 'Sun',
                'value' => $sunday,
                'date' => date('Y-m-').$sunday,
                'is_cur' => $w == 0 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$sunday ? 'disabled' : ''
            ],
            [
                'id' => 1,
                'name' => 'Mon',
                'value' => $monday,
                'date' => date('Y-m-').$monday,
                'is_cur' => $w == 1 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$monday ? 'disabled' : ''
            ],
            [
                'id' => 2,
                'name' => 'Tue',
                'value' => $tuesday,
                'date' => date('Y-m-').$tuesday,
                'is_cur' => $w == 2 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$tuesday ? 'disabled' : ''
            ],
            [
                'id' => 3,
                'name' => 'Wen',
                'value' => $wednesday,
                'date' => date('Y-m-').$wednesday,
                'is_cur' => $w == 3 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$wednesday ? 'disabled' : ''
            ],
            [
                'id' => 4,
                'name' => 'Thu',
                'value' => $thursday,
                'date' => date('Y-m-').$thursday,
                'is_cur' => $w == 4 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$thursday ? 'disabled' : ''
            ],
            [
                'id' => 5,
                'name' => 'Fri',
                'value' => $friday,
                'date' => date('Y-m-').$friday,
                'is_cur' => $w == 5 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$friday ? 'disabled' : ''
            ],
            [
                'id' => 6,
                'name' => 'Sat',
                'value' => $saturday,
                'date' => date('Y-m-').$saturday,
                'is_cur' => $w == 6 ? 1 : 0,
                'is_disabled' => $now_date < date('Y-m-').$saturday ? 'disabled' : ''
            ],
        ];
        return [
            'code' => 0,
            'text' => 'success',
            'result' => $cur_week
        ];
    }

    public function love( Request $request, int $id){

        $openId = $request->input('openId');

        if( ! $openId ) {
            return [
                'code' => 400,
                'text' => '用户参数错误',
                'result' => ''
            ];
        }
        if( ! $re = Article::find($id)) {
            return [
                'code' => 400,
                'text' => '数据不存在',
                'result' => ''
            ];
        }
        $user = Wxuser::where('openId',$openId)->first();

        $is_love = \DB::table('user_article_love')->where(['user_id' => $user->id, 'article_id' => $id])->first();
        if( $is_love ) {
            return [
                'code' => 400,
                'text' => '已经点赞过了',
                'result' => ''
            ];
        }

        Article::where('id',$id)->increment('love');
        \DB::table('user_article_love')->insert(['user_id' => $user->id, 'article_id' => $id]);

        $data = Article::select(
            '*',
            \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
            \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
            \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
        )->find($id);
        $data['is_love'] = 'on';
        $redis_key = 'article_'.$data['dateline'];
        Redis::set($redis_key, json_encode($data));
        return [
            'code' => 0,
            'text' => 'success',
            'result' => ''
        ];
    }

    /**
     * 文章评论&回复
     * @param Request $request
     * @param int $id
     */
    public function comment(Request $request, int $id)
    {


        $content = $request->input('content','');
        $reply_id = $request->input('reply_id', 0);
        $openId = $request->input('openId', '');
        $pid = $request->input('pid', 0);

        $validator = \Validator::make($request->input(), [
            'content' => [
                'required',
            ],
            'openId' => [
                'required'
            ]
        ]);
        if ($validator->fails()) {
            return [
                'code'=>400,
                'text'=>$validator->errors()->first(),
                'result'=>'',
            ];
        }

        $user = Wxuser::where('openId', $openId)->first();

        if( ! $user ){
            return [
                'code'=>400,
                'text'=>'用户不存在',
                'result'=>'',
            ];
        }

        if( $user->id == $reply_id) {
            return [
                'code'=>400,
                'text'=>'自己不能回复自己',
                'result'=>'',
            ];
        }
        if( $reply_id ) {
            $reply_user = Wxuser::where('id', $reply_id)->first();
            if( ! $reply_user ){
                return [
                    'code'=>400,
                    'text'=>'回复用户不存在',
                    'result'=>'',
                ];
            }
        }

        $interference = ['&', '*'];
        $data = config('words');
        Sensitive::interference($interference); //添加干扰因子
        Sensitive::addwords($data); //需要过滤的敏感词
        $content = Sensitive::filter($content);

        $data = [
            'pid' => $pid,
            'article_id' => $id,
            'user_id' => $user->id,
            'user_name' => $user->nickName,
            'user_img' => $user->avatarUrl,
            'reply_id' => $reply_id,
            'replay_name' => $reply_id ? $reply_user->nickName : '',
            'content' => json_encode($content),
            'add_time' => date('Y-m-d H:i:s')
        ];
        $res = \DB::table('article_comments')->insert($data);

        if($res) {
            return [
                'code'=>0,
                'text'=>'评论成功',
                'result'=>'',
            ];
        }

        return [
            'code'=>400,
            'text'=>'评论失败',
            'result'=>'',
        ];
    }

    /**
     * 获取文章评论
     * @param Request $request
     * @param $id 文章id
     * @return array
     */
    public function getCommet(Request $request, $id)
    {
        $page_num = (int) $request->input('page_num', env('PAGE_NUM', 1000));

        $db = \DB::table('article_comments')->where('article_id', $id)->where('status', 1)->orderby('add_time','asc');
        $data = $db->paginate($page_num)->toArray();

        foreach ($data['data'] as &$v) {
            $v = (array)$v;
            $v['user_name'] && $v['user_name'] = json_decode($v['user_name']);
            $v['replay_name'] && $v['replay_name'] = json_decode($v['replay_name']);
            $v['content'] && $v['content'] = json_decode($v['content']);
        }
        unset($v);

        $data['data'] = list_to_tree($data['data'], 'id','pid');
        return [
            'code'=>0,
            'text'=>'success',
            'result'=> $data,
        ];
    }

    public function share(Request $request, $id) {
        $openId = $request->input('openId');
        if( ! $openId ) {
            return [
              'code' => 400,
              'text' => '没有获取到用户登录状态',
              'result' => ''
            ];
        }

        $user = Wxuser::where('openId', $openId)->first();

        if( ! $user ){
            return [
                'code'=>400,
                'text'=>'用户不存在',
                'result'=>'',
            ];
        }

        $data = [
            'article_id' => $id,
            'user_id' => $user->id,
            'add_time' => date('Y-m-d H:i:s')
        ];
        $res = \DB::table('article_share')->insert($data);
        if( $res ) {
            Article::where('id',$id)->increment('shares');
            return [
                'code'=>0,
                'text'=>'操作成功',
                'result'=>'',
            ];
        }

        return [
            'code'=>400,
            'text'=>'操作失败',
            'result'=>'',
        ];
    }

    public function hide(Request $request, $id){
        \DB::table('article_comments')->where('id', $id)->update(['status' => 0]);
        return [
            'code'=>0,
            'text'=>'操作成功',
            'result'=>'',
        ];
    }
}
