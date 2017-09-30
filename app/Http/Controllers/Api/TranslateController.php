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

        $openId = $request->input('openId');
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
        if( $openId ) {
            $data = [
                'openId' => $openId,
                'source_lan' => $source_lan,
                'target_lan' => $target_lan,
                'source_con' => $content,
                'target_con' => $res,
            ];
            $msg = json_encode($data);
            RabbitmqController::publishMsg([env('MQ_EXCHANGES'),env('MQ_QUEUE2'),env('MQ_ROUTING_KEY2'),$msg]);
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
        $tsrlan = mb_strlen($request->input('content'),'UTF-8');
        $per_num = 190;
        $i = 0;
        $arr = [];
        for ($i; $tsrlan > $i;){
            $tmp = mb_substr($request->input('content'), $i, $i+$per_num,'UTF-8');
            $arr[] = $tmp;
            $i = mb_strlen($tmp,'UTF-8') + $i;
        }

        $lan = $request->input('lan');
        $total = count($arr);
        $res = [];
        foreach ($arr as $key => $item){
            $url = 'https://translate.google.cn/translate_tts?ie=UTF-8&total='.$total.'&idx='.$key.'&client=tw-ob&q='.$item.'&tl='.$lan;
            $save_path = 'text2voice/';
            $uniqid = uniqid();
            $pre_name = date('Y-m-d-H-i-s') . '-' .$uniqid.'.mp3';
            $cmd = '/usr/bin/wget -q -U Mozilla -O  '.$save_path.$pre_name.' "'.$url.'"';
            exec($cmd, $out);
            $res[] = [
                'full_url' => env('APP_URL').$save_path.$pre_name,
                'path_file' => $save_path.$pre_name
            ];
        }

        return [
            'code' => 0,
            'text' => 'success',
            'result' =>$res
        ];

        //file_put_contents($save_path.$pre_name, $response);

    }

    public function youdao(Request $request)
    {
        $q = $request->input('q','');
        $from = $request->input('from','zh-CHS');
        $to = $request->input('to','EN');
        $appKey = env('YOUDAO_APPKEY');
        $salt = rand();
        $youdao_secretkey = env('YOUDAO_SECRETKEY');

        $sign = strtoupper( md5($appKey.$q.$salt.$youdao_secretkey));
        $param = '?q='.urlencode($q).'&from='.$from.'&to='.$to.'&appKey='.$appKey.'&salt='.$salt.'&sign='.$sign;
        $url = env('YOUDAO_API_URL').$param;
        $res = json_decode(file_get_contents($url) ,true);
        if($res['errorCode'] == 0) {

        }
        return $res;
    }
}
