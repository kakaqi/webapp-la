<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\TranslateClient;
use Illuminate\Support\Facades\File;
use App\Libs as libs;

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

    public function baiduText2Voice(Request $request)
    {
        $lan = $request->input('lan','en');//目标语言
        $content = $request->input('content'); //内容
        /*return [
            'lan' => $lan,
            'content' => $content
        ];*/

        $aipSpeech = new libs\AipSpeech(env('CUID'), env('APIKEY'), env('SECRETKEY'));
        $result = $aipSpeech->synthesis($content, 'zh', 1, array(
            'vol' => 5,
        ));
        $save_path = 'text2voice/';
        if( ! File::exists($save_path) ){
            File::makeDirectory($save_path,  $mode = 0777, $recursive = false);
        }
        // 识别正确返回语音二进制 错误则返回json 参照下面错误码
        if(!is_array($result)){
            $uniqid = uniqid();
            $pre_name = date('Y-m-d-H-i-s') . '-' .$uniqid.'.mp3';
            file_put_contents($save_path.$pre_name, $result);
            return [
                'code' => 0,
                'text' => 'success',
                'result' => [
                    'full_url' => env('APP_URL').$save_path.$pre_name,
                    'path_file' => $save_path.$pre_name
                ]
            ];
        }
        return [
            'code' => 400,
            'text' => 'fail',
            'result' => $result
        ];
    }

    public function deleteAudio( Request $request)
    {
        $path_file = $request->input('path_file');
        File::delete($path_file);
        return [
            'code' => 0,
            'text' => 'success',
            'result' => ''
        ];
    }

    public function translateTts(Request $request)
    {
        $content = $request->input('content');
        $lan = $request->input('lan');
        $url = 'https://translate.google.cn/translate_tts?ie=UTF-8&total=1&idx=0&client=tw-ob&q='.$content.'&tl='.$lan;
        $save_path = 'text2voice/';
        $uniqid = uniqid();
        $pre_name = date('Y-m-d-H-i-s') . '-' .$uniqid.'.mp3';
        $cmd = '/usr/bin/wget -q -U Mozilla -O  '.$save_path.$pre_name.' "'.$url.'"';
        exec($cmd, $out);

        return [
            'code' => 0,
            'text' => 'success',
            'result' => [
                'full_url' => env('APP_URL').$save_path.$pre_name,
                'path_file' => $save_path.$pre_name
            ]
        ];

        //file_put_contents($save_path.$pre_name, $response);

    }
}
