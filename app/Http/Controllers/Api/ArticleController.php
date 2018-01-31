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
                unset($response['sid'],$response['tts'],$response['caption'],$response['s_pv'],$response['sp_pv'],$response['tags'],$response['love']);
                $response['date'] = $date;
                //图片保存到服务器使用队列处理
                $msg = json_encode($response);
                RabbitmqController::publishMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE3'),env('MQ_ROUTING_KEY3'),$msg]);
                unset($response['date']);

                $picture_path = 'article_img/';
                $pre_name = $date .'.jpg';
                $response['picture'] = $picture_path.$pre_name;
                $response['picture2'] = $picture_path.'big_'.$pre_name;
                $response['fenxiang_img'] = $picture_path.'fenxiang_'.$pre_name;

                $res = Article::create($response);

                if( $res->id ) {
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
                        'add_time' => date('Y-m-d H:i:s'),
                        'sort' => 1
                    ];
                    \DB::table('article_comments')->insert($default_comment_data);
                } else {
                    return [
                        'code' => 0,
                        'text' => 'success',
                        'result' => ''
                    ];
                }

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

        Article::where('id',$data['id'])->increment('views');
        $data['translation'] = '';
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
        $year_month = date('Y-m-',strtotime($date));

        $_0 = strtotime("$date -".$w .' days');
        $_0_day = date('d', $_0);
        $_0_date = date('Y-m-d',$_0);
        //$sunday = date('d',strtotime("$date -".$w .' days'));
        $_1 = strtotime("$day +1 days");
        $_1_day = date('d',$_1);
        $_1_date = date('Y-m-d',$_1);

        //$monday = date('d',strtotime("$day +1 days"));
        $_2 = strtotime("$day +2 days");
        $_2_day = date('d',$_2);
        $_2_date = date('Y-m-d',$_2);

        //$tuesday = date('d',strtotime("$day +2 days"));
        $_3 = strtotime("$day +3 days");
        $_3_day = date('d', $_3);
        $_3_date = date('Y-m-d',$_3);

        //$wednesday = date('d',strtotime("$day +3 days"));
        $_4 = strtotime("$day +4 days");
        $_4_day = date('d', $_4);
        $_4_date = date('Y-m-d',$_4);

        //$thursday = date('d',strtotime("$day +4 days"));
        $_5 = strtotime("$day +5 days");
        $_5_day = date('d',$_5);
        $_5_date = date('Y-m-d', $_5);

        //$friday = date('d',strtotime("$day +5 days"));
        $_6 = strtotime("$day +6 days");
        $_6_day = date('d',$_6);
        $_6_date = date('Y-m-d', $_6);

        $cur_week = [
            [
                'id' => 0,
                'name' => 'Sun',
                'value' => $_0_day,
                'date' => $_0_date,
                'is_cur' => $w == 0 ? 1 : 0,
                'is_disabled' => $now_date < $_0_date ? 'disabled' : ''
            ],
            [
                'id' => 1,
                'name' => 'Mon',
                'value' => $_1_day,
                'date' => $_1_date,
                'is_cur' => $w == 1 ? 1 : 0,
                'is_disabled' => $now_date < $_1_date ? 'disabled' : ''
            ],
            [
                'id' => 2,
                'name' => 'Tue',
                'value' => $_2_day,
                'date' => $_2_date,
                'is_cur' => $w == 2 ? 1 : 0,
                'is_disabled' => $now_date < $_2_date ? 'disabled' : ''
            ],
            [
                'id' => 3,
                'name' => 'Wen',
                'value' => $_3_day,
                'date' => $_3_date,
                'is_cur' => $w == 3 ? 1 : 0,
                'is_disabled' => $now_date < $_3_date ? 'disabled' : ''
            ],
            [
                'id' => 4,
                'name' => 'Thu',
                'value' => $_4_day,
                'date' => $_4_date,
                'is_cur' => $w == 4 ? 1 : 0,
                'is_disabled' => $now_date < $_4_date ? 'disabled' : ''
            ],
            [
                'id' => 5,
                'name' => 'Fri',
                'value' => $_5_day,
                'date' => $_5_date,
                'is_cur' => $w == 5 ? 1 : 0,
                'is_disabled' => $now_date < $_5_date ? 'disabled' : ''
            ],
            [
                'id' => 6,
                'name' => 'Sat',
                'value' => $_6_day,
                'date' => $_6_date,
                'is_cur' => $w == 6 ? 1 : 0,
                'is_disabled' => $now_date < $_6_date ? 'disabled' : ''
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
        //文章点赞队列处理
        $msg = json_encode(['openId' => $openId, 'id' => $id]);
        RabbitmqController::publishMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE4'),env('MQ_ROUTING_KEY4'),$msg]);
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

        $db = \DB::table('article_comments')->where('article_id', $id)->where('status', 1)->orderby('sort','asc');
        $data = $db->paginate($page_num)->toArray();

        foreach ($data['data'] as &$v) {
            $v = (array)$v;
            if( $v['user_name']) {
                $v['user_name'] = json_decode($v['user_name']);
            } else {
                $v['user_name'] = '匿名'.substr(md5($v['user_id']),0,5);
            }

            if( $v['replay_name']) {
                $v['replay_name'] = json_decode($v['replay_name']);
            } else {
                $v['replay_name'] = '匿名'.substr(md5($v['reply_id']),0,5);
            }

            if( $v['user_id'] == 1) {
                $v['user_name'] = '多小编';
                $v['user_img'] = env('APP_URL').'image/xiaobian.png';
            }
            if( $v['reply_id'] == 1) {
                $v['replay_name'] = '多小编';
            }
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

    public function test()
    {
        return '6666';
    }


}
