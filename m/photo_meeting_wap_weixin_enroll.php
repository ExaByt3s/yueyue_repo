<?php
/** 
 * 
 * ��Ӱ���ר��ҳ
 * 
 * 2015-4-1
 * 
 * author ����
 * 
 */
 
 
 
include_once('/disk/data/htdocs232/poco/pai/topic/meeting/config/phone_meeting_config.php');//���÷���Ӧ���ε�ID����Ǯ
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_obj   = POCO::singleton('pai_summit_meeting_class');
$pai_sms_obj = POCO::singleton('pai_sms_class');
/*$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
if(!$__is_weixin)
{
    header("location:http://www.yueus.com/topic/meeting/photo_meeting_wap_enroll.php");
}*/

//��ʾҳ��
// ע�� URL һ��Ҫ��̬��ȡ������ hardcode.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//΢����Ȩ
$openid = trim($_COOKIE['yueus_openid']);
$not_repeat = intval($_INPUT['not_repeat']);
if( strlen($openid)<1 && $not_repeat!=1 )
{
    if( strpos($url, '?')===false )
    {
        $url = $url . '?not_repeat=1';
    }
    else
    {
        $url = $url . '&not_repeat=1';
    }
    $weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
    $authorize_url = $weixin_pub_obj->auth_get_authorize_url(array('url'=>$url), 'snsapi_base');
    header("Location: {$authorize_url}");
    exit();
}
//��ʾҳ��

//��������ʾ
$app_id = 'wx25fbf6e62a52d11e'; //ԼԼ��ʽ��
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);

 
$tpl = $my_app_pai->getView('photo_meeting_wap_weixin_enroll.tpl.htm');
 
 
$meeting_price_1 = $meeting_price_array["1"];
$meeting_price_2 = $meeting_price_array["2"];
$meeting_price_3 = $meeting_price_array["3"];


$tpl->assign($wx_sign_package);
$tpl->assign("meeting_price_1",$meeting_price_1);
$tpl->assign("meeting_price_2",$meeting_price_2);
$tpl->assign("meeting_price_3",$meeting_price_3);
$tpl->assign("meeting_name_price_array",$meeting_name_price_array);
$tpl->assign("openid",$openid);
 
 $tpl->output();
 ?>