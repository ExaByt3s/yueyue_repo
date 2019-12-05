<?php
/** 
 * 
 * 约约模特注册页
 * 
 * author 星星
 * 
 * 
 * 2015-1-23
 * 
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

//跳去全站登录页
$param_r_url = trim($_INPUT['r_url']);
$main_url = "http://www.yueus.com/pc/register.php?r_url=".$param_r_url;
header("location:{$main_url}");
exit;
//跳去全站登录页


$r_url = urldecode(trim($_INPUT['r_url']));//从什么活动过来


//校验返回链接结构，作安全处理
if(!empty($r_url) || $r_url!="")
{
    $parse_url_arr = parse_url($r_url);

    if($parse_url_arr['scheme']!="http")
    {
        echo "<script>
               alert('返回链接不符合链接结构');
               location.href='http://www.yueus.com/reg/register.php';
            </script>";
        exit();
    }
    if(!preg_match("/\.yueus\.com/",$parse_url_arr['host']))
    {
        echo "<script>
               alert('返回链接不是yueus域名的链接');
               location.href='http://www.yueus.com/reg/register.php';
            </script>";
        exit();
    }
}

$tpl = $my_app_pai->getView('register.tpl.htm');

if(!empty($yue_login_id))
{
    //header("location:{$_SERVER['HTTP_REFERER']}");
    
    
    
    header("location:http://www.yueus.com/topic/party_topic/");
    
    
    
} 

// 公共样式和js引入
$Party_global_header = $my_app_pai->webControl('Party_global_header', array(), true);
$tpl->assign('Party_global_header', $Party_global_header);
$tpl->assign('r_url', $r_url);
$tpl->assign('encode_r_url',urlencode($r_url));// 头部引入

$header_html = $my_app_pai->webControl('PartyHeader', array(), true);
$tpl->assign('header_html', $header_html);
$tpl->assign("rand",201503091415);

$tpl->output();
 ?>