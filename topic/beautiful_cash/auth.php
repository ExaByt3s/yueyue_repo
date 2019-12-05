<?php 
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

if(!$_COOKIE['yueus_openid'])
{
	header("Location: https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx25fbf6e62a52d11e&redirect_uri=http%3A%2F%2Fyp.yueus.com%2Fm%2Fauth.php%3Furl%3Dhttp%253A%252F%252Fwww.yueus.com%252Ftopic%252Fbeautiful_cash%252Fgeton.php&response_type=code&scope=snsapi_userinfo&state=#wechat_redirect");
}
else
{
	header("Location: http://www.yueus.com/topic/beautiful_cash/geton.php");
}

?>