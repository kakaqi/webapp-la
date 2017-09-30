<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Translation;
use App\Models\Wxuser;
use Illuminate\Support\Facades\Redis;

class TranslationController extends BaseController
{
    public function index()
    {
        $this->view_data['page_title'] = '翻译历史列表';
        $this->view_data['page_name'] = '多语言管理';
        $data = Translation::get();
        $data->each(function ($item){
            $item->userInfo;
            $item->userInfo->nickName &&  $item->userInfo->nickName = json_decode( $item->userInfo->nickName);
        });
        $this->view_data['data'] = $data;
        return view('admin.translation')->with($this->view_data);
    }
}
