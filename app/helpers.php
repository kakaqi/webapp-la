<?php

if (! function_exists('trans')) {
    /**
     * Translate the given message.
     *
     * @param  string  $id
     * @param  array   $parameters
     * @param  string  $domain
     * @param  string  $locale
     * @return string
     */
    function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        if (is_null($id)) {
            return app('translator');
        }

        return app('translator')->trans($id, $parameters, $domain, $locale);
    }
}

if (! function_exists('list_to_tree')) {
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];

                    }
                }
            }
        }
        return $tree;
    }
}

function toTimeZone($src, $to_tz = 'America/Denver', $from_tz = 'Asia/Shanghai', $fm = 'Y-m-d H:i:s') {
    $datetime = new DateTime($src, new DateTimeZone($from_tz));
    $datetime->setTimezone(new DateTimeZone($to_tz));
    return $datetime->format($fm);
}

/**
 * 创建订单号
 * @param string $prefix
 * @return string
 */
function createOrderSn($prefix = "L")
{
$yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
return strtoupper($prefix).$yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
}

function randomkeys($length)
{
    $returnStr='';
    $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
    for($i = 0; $i < $length; $i ++) {
        $returnStr .= $pattern {mt_rand ( 0, 61 )}; //生成php随机数
    }
    return str_shuffle($returnStr);
}

//菜单添加装饰前缀
function menusPrefix($menus = [],$prefix='|--')
{
    if(empty($menus))
        return false;

    foreach ($menus as $k=>$v)
    {
        if($v['parent_id'] > 0)
        {
            $menus[$k]['title_cn'] = $v['title_cn'] ? $prefix.$v['title_cn'] : "";
            $menus[$k]['title_en'] = $v['title_en'] ? $prefix.$v['title_en'] : "";
        }
    }
    return $menus;
}

//根据算法参数生成SecurityKey操
function createSecurityKey($algorithm='sha1',$str)
{
    return hash($algorithm,$str);
}

/**
 * encrypt解密
 * @param string $strtoencrypt
 * @return string
 */
function encrypt_ (string $strtoencrypt) : string
{
    $password = "kinge383e";
    for( $i=0; $i<strlen($password); $i++ ) {
        $cur_pswd_ltr = substr($password,$i,1);
        $pos_alpha_ary[] = substr(strstr(env('ALPHABET'),$cur_pswd_ltr),0,strlen(env('RALPHABET')));
    }

    $i=0;
    $n = 0;
    $nn = strlen($password);
    $c = strlen($strtoencrypt);
    $encrypted_string = '';

    while($i<$c) {
        $encrypted_string .= substr($pos_alpha_ary[$n],strpos(env('RALPHABET'),substr($strtoencrypt,$i,1)),1);
        $n++;
        if($n==$nn) $n = 0;
        $i++;
    }

    return $encrypted_string;

}

/**
 * decrypt加密
 * @param $strtodecrypt
 * @return string
 */
function decrypt_ (string $strtodecrypt): string
{
    $password = "kinge383e";
    for( $i=0; $i<strlen($password); $i++ ) {
        $cur_pswd_ltr = substr($password,$i,1);
        $pos_alpha_ary[] = substr(strstr(env('ALPHABET'),$cur_pswd_ltr),0,strlen(env('RALPHABET')));
    }

    $i=0;
    $n = 0;
    $nn = strlen($password);
    $c = strlen($strtodecrypt);
    $decrypted_string = '';

    while($i<$c) {
        $decrypted_string .= substr(env('RALPHABET'),strpos($pos_alpha_ary[$n],substr($strtodecrypt,$i,1)),1);
        $n++;
        if($n==$nn) $n = 0;
        $i++;
    }

    return $decrypted_string;

}