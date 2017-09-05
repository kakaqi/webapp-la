<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
        $cmd = '/usr/bin/sh /usr/local/src/silk-v3-decoder/converter_beta.sh  /www/webapp-la/public/voice/'.$filename.' '.$type;
        exec($cmd, $out);
        $token = self::baiduVoiceAuth();
        $response = self::baiduVoice($token, $pathname.$pre_name.'.'.$type, 'wav');
        $res = self::translate($response['result'][0], $source_lan, $target_lan);
        return [
            'code' => 0,
            'text' => 'success',
            'result' => $res,
        ];
    }

    /**
     * 百度接口认证
     * @return mixed
     */
    protected function baiduVoiceAuth()
    {
        $auth_url = env('AUTH_URL').env('APIKEY')."&client_secret=".env('SECRETKEY');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($ch);
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($response, true);
        return $response['access_token'];
    }

    /**
     * 百度语音识别
     * @param $token
     * @param $audio_file
     * @param string $type
     * @return mixed
     */
    protected function baiduVoice($token, $audio_file, $type='wav')
    {
        $audio = file_get_contents($audio_file);
        $base_data = base64_encode($audio);
        $array = array(
            "format" => $type,
            "rate" => 8000,
            "channel" => 1,
            //"lan" => "zh",
            "token" => $token,
            "cuid"=> env('CUID'),
            //"url" => "http://www.xxx.com/sample.pcm",
            //"callback" => "http://www.xxx.com/audio/callback",
            "len" => filesize($audio_file),
            "speech" => $base_data,
        );
        $json_array = json_encode($array);
        $content_len = "Content-Length: ".strlen($json_array);
        $header = array ($content_len, 'Content-Type: application/json; charset=utf-8');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, env('BAIDU_API_URL'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_array);
        $response = curl_exec($ch);
        if(curl_errno($ch))
        {
            print curl_error($ch);
        }
        curl_close($ch);
        $response = json_decode($response, true);
        file_put_contents('.test.txt', var_export($response));
        return  $response;
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
}
