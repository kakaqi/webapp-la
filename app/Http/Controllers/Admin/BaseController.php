<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Menus;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    //视图数据
    protected $view_data = [];
    protected $guard = 'web';

    public function __construct()
    {

        $this->view_data['userInfo'] = Auth::user();

        self::getMenus();
    }
    protected function getMenus()
    {
        $redis_key = 'menus';
        if( ! $data = Redis::get($redis_key)){
            $re = Menus::where('status',1)->orderby('sort','asc')->get()->toArray();
            $menus = list_to_tree($re,  'id', 'pid', '_child');
            Redis::set($redis_key, json_encode($menus));
        } else {
            $menus = json_decode($data, true);
        }
        $this->view_data['menus'] = $menus;
        $this->view_data['current_uri'] = '/'.Route::current()->uri;

    }
}
