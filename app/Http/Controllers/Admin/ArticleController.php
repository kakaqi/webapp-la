<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Redis;
class ArticleController extends BaseController
{
    public function index()
    {

        $this->view_data['page_title'] = '每日一句列表';
        $this->view_data['page_name'] = '文章管理';
        $this->view_data['data'] =  Article::select(
            '*',
            \DB::raw('CONCAT("'.env('APP_URL').'", picture) AS picture'),
            \DB::raw('CONCAT("'.env('APP_URL').'", picture2) AS picture2'),
            \DB::raw('CONCAT("'.env('APP_URL').'", fenxiang_img) AS fenxiang_img')
        )->get();
        return view('admin.article')->with($this->view_data);
    }

    public function store(Request $request)
    {

        $name = $request->input('name');
        $title = $request->input('title');
        $status = $request->input('status');
        $validator = \Validator::make($request->input(), [
            'name' => [
                'required',
                'unique:languages',
                'regex:/^[a-zA-z][a-zA-Z0-9_-]{1,10}$/'
            ],
            'title' => [
                'required',
                'unique:languages',
                'regex:/^[\w\_\x{4e00}-\x{9fa5}]{1,20}$/u'//中文、英文、数字、下划线结合而且3-20字符
            ]
        ]);

        if ($validator->fails()) {
            return [
                'code'=>4001,
                'text'=>$validator->errors(),
                'result'=>'',
            ];
        }
        $data = [
            'name' => $name,
            'title' => $title,
            'status' => $status
        ];

        $re = Language::create($data);

        if( ! $re ) {
            return [
                'code'=> 4002,
                'text'=> '保存失败！',
                'result'=> '',
            ];
        }
        self::updateRedis();
        return [
            'code'=> 0,
            'text'=> '操作成功！',
            'result'=> $re,
        ];

    }
    public function del($id)
    {
        $re = Language::find($id);
        if( ! $re ) {
            return [
                'code'=> 4002,
                'text'=> '数据不存在！',
                'result'=> $re,
            ];
        }

        $re = Language::where('id', $id)->delete();

        if( ! $re ) {
            return [
                'code'=> 4002,
                'text'=> '删除数据失败！',
                'result'=> $re,
            ];
        }
        self::updateRedis();
        return [
            'code'=> 0,
            'text'=> '操作成功！',
            'result'=> $re,
        ];
    }

    public function show($id)
    {
        $re = Language::find($id);
        if( ! $re ) {
            return [
                'code'=> 4002,
                'text'=> '数据不存在！',
                'result'=> $re,
            ];
        }
        return [
            'code'=> 0,
            'text'=> '操作成功！',
            'result'=> $re,
        ];
    }

    public function update(Request $request)
    {
        $id = $request->input('id');
        $re = Language::find($id);
        if( ! $re ) {
            return [
                'code'=> 4002,
                'text'=> '数据不存在！',
                'result'=> $re,
            ];
        }

        $name = $request->input('name');
        $title = $request->input('title');
        $status = $request->input('status');
        $validator = \Validator::make($request->input(), [
            'name' => [
                'required',
                'unique:languages,name,'.$id,
                'regex:/^[a-zA-z][a-zA-Z0-9_-]{1,10}$/'
            ],
            'title' => [
                'required',
                'unique:languages,title,'.$id,
                'regex:/^[\w\_\x{4e00}-\x{9fa5}]{1,20}$/u'//中文、英文、数字、下划线结合而且3-20字符
            ]
        ]);

        if ($validator->fails()) {
            return [
                'code'=>4001,
                'text'=>$validator->errors(),
                'result'=>'',
            ];
        }
        $data = [
            'name' => $name,
            'title' => $title,
            'status' => $status
        ];

        $re = Language::where('id', $id)-> update($data);

        if( $re === false ) {
            return [
                'code'=> 4002,
                'text'=> '保存失败！',
                'result'=> $re,
            ];
        }
        self::updateRedis();
        return [
            'code'=> 0,
            'text'=> '保存成功！',
            'result'=> $re,
        ];

    }

    protected function updateRedis()
    {
        $redis_key = 'languages';
        $res = Language::select('name','title')->where('status',1)->get();
        $lang_name = $res->pluck('name');
        $lang_title = $res->pluck('title');
        $data = [
            'lang_name' => $lang_name,
            'lang_title' => $lang_title
        ];
        Redis::set($redis_key, json_encode($data));
    }
}
