<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Stichoza\GoogleTranslate\TranslateClient;
use Illuminate\Support\Facades\File;

use App\Libs as libs;

class FileUploadController extends Controller
{
    //
    public function upload(Request $request)
    {
        $source_lan = $request->input('source_lan','zh-CN');//原始语言
        $target_lan = $request->input('target_lan','en');//目标语言

        $file = $request->file('file');
        $voice_type = 'voice';//文件类型
        $ext = $file->getClientOriginalExtension();// 扩展名
        $pathname = $voice_type.'/';
        $pre_name = date('Y-m-d-H-i-s') . '-' . uniqid();
        $filename = $pre_name . '.' . $ext;
        $file->move($pathname, $filename);

        $type = 'wav';
        $type2 = 'pcm';
        $cmd = '/usr/bin/sh /silk-v3-decoder/converter.sh  /www/webapp-la/public/voice/'.$filename.' '.$type;
        exec($cmd, $out);
        $aipSpeech = new libs\AipSpeech(env('CUID'), env('APIKEY'), env('SECRETKEY'));
        // 识别本地文件
        $response = $aipSpeech->asr(@file_get_contents($pathname.$pre_name.'.'.$type.'.'.$type2), $type2, 16000, array(
            'lan' => 'zh',
        ));
        if( $response['err_no'] != 0) {
            return [
                'code' => 400,
                'text' => 'error',
                'result' => ''
            ];
        }
        if( !isset($response['result'])) {
            return [
                'code' => 400,
                'text' => 'error',
                'result' => ''
            ];
        }
        $content = rtrim($response['result'][0], '，') ;
        $res = self::translate($content, $source_lan, $target_lan);
        File::delete($pathname.$filename);
        File::delete($pathname.$pre_name.'.'.$type);
        File::delete($pathname.$pre_name.'.'.$type.'.'.$type2);
//        File::cleanDirectory($pathname);

        return [
            'code' => 0,
            'text' => 'success',
            'result' => [
                'source_text' => $content,
                'result_text' => $res
            ]
        ];
    }



    protected function translate($content, $source_lan, $target_lan)
    {
        $res = '';
        if( $content ) {
            $obj = new TranslateClient();
            $obj->setUrlBase(env('GOOGLE_TRANSLATE_URL'));
            $obj->setSource($source_lan);
            $obj->setTarget($target_lan);
            $res = $obj->translate($content);
        }
        return $res;
    }

    public function getBaiduVoice()
    {
        $aipSpeech = new libs\AipSpeech(env('CUID'), env('APIKEY'), env('SECRETKEY'));
        // 识别本地文件
        $re = $aipSpeech->asr(file_get_contents('./2017090403224593.silk.pcm'), 'pcm', 8000, array(
            'lan' => 'zh',
        ));
        return $re;
       // var_export($re);
    }
}
