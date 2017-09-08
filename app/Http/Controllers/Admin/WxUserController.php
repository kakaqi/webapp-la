<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Wxuser;

class WxUserController extends BaseController
{
    public function index()
    {
        $this->view_data['page_title'] = '小程序用户列表';
        $this->view_data['page_name'] = '帐号管理';

        $re = Wxuser::get();
        $this->view_data['data'] = $re->each(function ($item){
            $item['nickName'] = json_decode($item['nickName'], true);
            $item['gender'] = $item['gender'] == 1 ? '男' : ($item['gender'] == 0 ? '未知' : '女');

        });

        return view('admin.wx_user')->with($this->view_data);
    }
}
