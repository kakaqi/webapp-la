<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\TranslateClient;

class TranslateController extends Controller
{
    /**
     * 谷歌翻译api接口，支持一对一翻译
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {

        $source_lan = $request->input('source_lan','zh-CN');//原始语言
        $target_lan = $request->input('target_lan','en');//目标语言
        $content = $request->input('content','');//翻译内容

        $res = '';
        if( $content ) {
            $obj = new TranslateClient();
            $obj->setUrlBase(env('GOOGLE_TRANSLATE_URL'));
            $obj->setSource($source_lan);
            $obj->setTarget($target_lan);
            $res = $obj->translate($content);
        }

        return [
            'code'=>0,
            'text'=> 'success',
            'result'=>$res,
        ];
    }
}
