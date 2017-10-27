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
        )->orderby("id","desc")->get();
        return view('admin.article')->with($this->view_data);
    }
}
