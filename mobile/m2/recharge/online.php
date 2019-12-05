<?php
/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

include_once($file_dir.'/../../yue_res_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/mobile/task_auth_common.inc.php');

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false;  

$redirect_url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$_SERVER['REQUEST_URI'];

if(!$yue_login_id && !$__is_yueyue_app)
{
	if($__is_weixin)
	{
		if(preg_match('/predev/',$_SERVER['SCRIPT_URI']))
		{
			$mode = 'm2predev';
		}
		else
		{
			$mode = 'm2';				
		}

		$reg_url = 'http://'.$_SERVER['HTTP_HOST'].'/mobile/'.$mode.'/account/reg.php?redirect_url='.urlencode($redirect_url);

		//微信授权
		$openid = trim($_COOKIE['yueus_openid']);
		
		$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
		$authorize_url = $weixin_pub_obj->auth_get_authorize_url(array('url'=>$reg_url,'url2'=>$redirect_url), 'snsapi_base');
		header("Location: {$authorize_url}");
		exit();
	}
	else
	{
		header('Location:../account/reg.php?redirect_url='.urlencode($redirect_url));
		exit();
	}
}



$head_html = include_once($file_dir. '/../webcontrol/head.php');

$tpl = new SmartTemplate("rechare_online.tpl.html");

$tpl ->assign('is_weixin',$__is_weixin);
$tpl ->assign('is_yueyue_app',$__is_yueyue_app);
$tpl ->assign('head_html',$head_html);
$tpl->output();
?>
