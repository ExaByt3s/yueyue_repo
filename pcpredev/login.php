<?php


/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 

// 通用
include_once($file_dir.'/./pc_common.inc.php');

// 权限文件
include_once($file_dir.'/./pc_auth_common.inc.php');

// 头部css相关
include_once($file_dir. '/./webcontrol/head.php');

// 顶部栏
include_once($file_dir. '/./webcontrol/global-top-bar.php');

// 底部
include_once($file_dir. '/./webcontrol/footer.php');

// 下载区域
include_once($file_dir. '/./webcontrol/down-app-area.php');


// ================== 载入模板 ==================
$tpl = $my_app_pai->getView('login.tpl.htm');




$r_url = urldecode(trim($_INPUT['r_url']));//来源地址

$tpl->assign('r_url',$r_url);


// 头部公共样式和js引入
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// 头部bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);

// 底部
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);


// 微信小授权

if($__is_weixin && !$_COOKIE['yueus_openid'])
{
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; // 登录页地址
	$url2 = $r_url;// 登录成功后的目的地址
	$scope = 'snsapi_base';

	$params = array(
		'url' => $url,
		'url2' => $url2,
	);
	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
	$auth_url = $weixin_pub_obj->auth_get_authorize_url($params, $scope);	

	header("Location:{$auth_url}");
	exit();
}

$tpl->output();
?>