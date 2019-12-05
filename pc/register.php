<?php


/**
 * 引用资源文件定位，注意！确保引用路径争取
 */
$file_dir = dirname(__FILE__);



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
# ↓↓↓↓↓↓↓↓↓↓ 加载 产生get_hash的方法的类 2015-11-17 黄石汉
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once(G_YUEYUE_ROOT_PATH . '/system_service/verify_code/poco_app_common.inc.php');
# ↑↑↑↑↑↑↑↑↑↑ 加载 产生get_hash的方法的类 2015-11-17 黄石汉
// ================== 载入模板 ==================
$tpl = $my_app_pai->getView('register.tpl.htm');
# ↑↑↑↑↑↑↑↑↑↑ 获取hash值 2015-11-17 黄石汉
$get_hash = POCO::singleton('validation_code_class')->get_hash();
$tpl->assign('token', $get_hash);
$device_arr = mall_get_user_agent_arr();
if($device_arr["is_pc"]==1)
{
    $is_pc = "pc";
}
else
{
    $is_pc = "else";
}
$tpl->assign('is_pc', $is_pc);
# ↑↑↑↑↑↑↑↑↑↑ 获取hash值 2015-11-17 黄石汉





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

/**
 * 判断客户端
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;

// 微信小授权

if($__is_weixin && (!$_COOKIE['yueus_openid'] || $_COOKIE['yueus_scope'] != 'snsapi_userinfo'))
{
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; // 注册页地址
	$url2 = $r_url;// 注册成功后的目的地址
	$scope = 'snsapi_userinfo';

	$params = array(
		'url' => $url,
		'url2' => $url2,
	);
	$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
	$auth_url = $weixin_pub_obj->auth_get_authorize_url($params, $scope);	

	header("Location:{$auth_url}");
	exit();
}

//扫描关注时，渠道参数
$qrscene_tj_str = '';
$weixin_pub_obj = POCO::singleton('pai_weixin_pub_class');
$receive_info_tmp = $weixin_pub_obj->get_receive_info_by_subscribe($_COOKIE['yueus_openid']);
if( !empty($receive_info_tmp) && !empty($receive_info_tmp['EventKey']) )
{
	$qrscene_tj_str = trim($receive_info_tmp['EventKey']);
}
$tpl->assign('qrscene_tj_str', $qrscene_tj_str);

$tpl->output();
?>