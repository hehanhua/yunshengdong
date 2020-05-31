<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function cdnurl($url,$mode=false){
    if(empty($url)){return '';}
    $arr = parse_url($url);
    if($mode){
        if(isset($arr['scheme'])){
            return $url;
        }
    }
    $new_url = '';
    $new_url .= 'http://img.j6yx.com';
    if(isset($arr['path'])){
        $new_url .= $arr['path'];
    }
    if(isset($arr['query'])){
        $new_url .= '?'.$arr['query'];
    }
    return $new_url;
}

function hjyurl($url='',$mode=false){
    $new_url = 'http:'.$url;
    return $new_url;
}

function cdnbody($str){
    return preg_replace('/(src\s*=\s*[\"|\'])(.*?)([\"|\'])/i' , "\${1}".GAME_DOMAIN."\${2}\${3}",$str);
}
/**
 * 将字符部分加密并输出
 * @param unknown $str
 * @param unknown $start 从第几个位置开始加密(从1开始)
 * @param unknown $length 连续加密多少位
 * @return string
 */
function encryptShow($str,$start,$length) {
    $end = $start - 1 + $length;
    $array = str_split($str);
    foreach ($array as $k => $v) {
        if ($k >= $start-1 && $k < $end) {
            $array[$k] = '*';
        }
    }
    return implode('',$array);
}
function agent() {
    if(!isset($_SERVER['HTTP_USER_AGENT'])){return 'pc';}
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if(strpos ( $agent, "android" ) || strpos ( $agent, "Android" )){
        return 'android';
    }
    if(strpos($agent, 'iphone') || strpos($agent, 'ipad')){
        return 'ios';
    }
    if(strpos($agent, 'iPhone') || strpos($agent, 'iPad')){
        return 'ios';
    }
    return 'pc';
}
function is_weixin() {
    if(!isset($_SERVER['HTTP_USER_AGENT'])){return 'pc';}
    $agent = $_SERVER ['HTTP_USER_AGENT'];
    if(strpos ( $agent, "icroMessenger" ) || strpos ( $agent, "MicroMessenger" ) || strpos ( $agent, "WindowsWechat" ) ){
        return true;
    }
    return false;
}
/**
 * 判断是否为手机访问
 * @return  boolean
 */
function is_mobile() {
    if(!isset($_SERVER['HTTP_USER_AGENT'])){return false;}


    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        return false;
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
    ) {
        return true;
    } else {
        return false;
    }
}
function priceFormat($price) {
    return number_format($price,2,'.','');
}
function numFormat($num,$len=2){
    return number_format($num,$len,'.','');
}

//对emoji表情转义
function emoji_encode($str){
    $strEncode = '';

    $length = mb_strlen($str,'utf-8');

    for ($i=0; $i < $length; $i++) {
        $_tmpStr = mb_substr($str,$i,1,'utf-8');
        if(strlen($_tmpStr) >= 4){
            $strEncode .= '';
            //$strEncode .= '[[EMOJI:'.rawurlencode($_tmpStr).']]';
        }else{
            $strEncode .= $_tmpStr;
        }
    }

    return trim($strEncode);
}
//对emoji表情转反义
function emoji_decode($str){
    $strDecode = preg_replace_callback('|\[\[EMOJI:(.*?)\]\]|', function($matches){
        return rawurldecode($matches[1]);
    }, $str);
    return $strDecode;
}
function password_encode($pass){
    return sha1('ju6game'.md5($pass));
}
//把int类型数据格式化
function format_intdate($timestamp,$rule='Y-m-d H:i:s',$empty=''){
    if(empty($timestamp)){
        return $empty;
    }else{
        return date($rule,$timestamp);
    }
}
/**
 * 随机字符
 * @param number $length 长度
 * @param string $type 类型
 * @param number $convert 转换大小写
 * @return string
 */
function random($length=6, $type='string', $convert=0){
    $config = array(
        'number'=>'1234567890',
        'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if(!isset($config[$type])) $type = 'string';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) -1;
    for($i = 0; $i < $length; $i++){
        $code .= $string{mt_rand(0, $strlen)};
    }
    if(!empty($convert)){
        $code = ($convert > 0)? strtoupper($code) : strtolower($code);
    }
    return $code;
}
function makeSign($data, $key){
    ksort($data);
    $da = [];
    foreach ($data as $k => $v) {
        array_push($da, $k . '=' . $v);
    }
    $str = implode('&',$da).$key;
    return md5($str);
}
