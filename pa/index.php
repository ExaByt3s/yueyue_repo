<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/16
 * Time: 15:41
 */

//$url = "http://www.yusu.com/pa/?url=" . urlencode('http://www.yueus.com/regedit/'). "&puid=100008";

$url = trim($_GET['url']);
$puid = trim($_GET['puid']);
//非法链接跳转到首页
if(strlen($url) <1 || strlen($puid) <1)
{
    header("Location:http://www.yueus.com");
    exit;
}
$pattern = '/yueus.com/is'; //防止被攻击
preg_match($pattern,$url,$match);
if(!$match)
{
    header("Location:http://www.yueus.com");
    exit;
}
//合法链接跳转
setcookie('tj_spread_regedit',$puid,time()+3600,'/','yueus.com');
header("Location:$url");
