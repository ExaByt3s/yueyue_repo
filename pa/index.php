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
//�Ƿ�������ת����ҳ
if(strlen($url) <1 || strlen($puid) <1)
{
    header("Location:http://www.yueus.com");
    exit;
}
$pattern = '/yueus.com/is'; //��ֹ������
preg_match($pattern,$url,$match);
if(!$match)
{
    header("Location:http://www.yueus.com");
    exit;
}
//�Ϸ�������ת
setcookie('tj_spread_regedit',$puid,time()+3600,'/','yueus.com');
header("Location:$url");
