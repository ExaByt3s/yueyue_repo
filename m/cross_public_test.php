<?php
/**
 * 跨公众号测试
 * 2015-6-4
 *
 * author 星星
 *
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


//显示页面
// 注意 URL 一定要动态获取，不能 hardcode.
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

//微信授权
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
//显示页面

//往下是显示
$app_id = 'wx25fbf6e62a52d11e'; //约约正式号
$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');
$wx_sign_package = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);

$tpl = $my_app_pai->getView('cross_public_test.tpl.htm');

$tpl->assign($wx_sign_package);
$tpl->assign("openid",$openid);

$tpl->output();
?>