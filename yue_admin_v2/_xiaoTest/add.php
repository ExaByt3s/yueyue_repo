<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   11:09
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');

$tpl = new SmartTemplate( 'add.tpl.htm' );

$act = trim($_INPUT['act']);
$url = trim($_INPUT['url']);

if($act == 'create')
{
    if(strlen($url) <1) js_pop_msg_v2('url不能为空');
    //if(filter_var($url, FILTER_VALIDATE_URL)) js_pop_msg_v2('链接地址非法');
    $curl = "http://www.yueus.com/pa/?url=".urlencode($url)."&puid={$yue_login_id}";
    $img_url = pai_activity_code_class::get_qrcode_img($curl);
    echo $curl;
    //exit;
    $tpl->assign('url',$url);
    $tpl->assign('curl',$curl);
    $tpl->assign('img_url',$img_url);
}

$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();