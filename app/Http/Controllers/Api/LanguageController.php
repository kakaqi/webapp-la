<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Support\Facades\Redis;
/**
 * 多语言控制器
 * Class LanguageController
 * @package App\Http\Controllers\Api
 */
class LanguageController extends Controller
{
    public function index(Request $request)
    {
        $redis_key = 'languages';
        if( ! $data = Redis::get($redis_key)){
            $res = Language::select('name','title')->where('status',1)->get();
            $lang_name = $res->pluck('name');
            $lang_title = $res->pluck('title');
            $data = [
                'lang_name' => $lang_name,
                'lang_title' => $lang_title
            ];
            Redis::set($redis_key, json_encode($data));
        } else {
            $data = json_decode($data, true);
        }


        return [
            'code' => 0,
            'text' => 'success',
            'result' => $data
        ];
    }

}
