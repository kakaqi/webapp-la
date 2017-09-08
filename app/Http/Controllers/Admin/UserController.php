<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    public function index()
    {
        $this->view_data['page_title'] = '后台帐号列表';
        $this->view_data['page_name'] = '帐号管理';
        $this->view_data['data'] = User::get();
        return view('admin.user')->with($this->view_data);
    }
}
