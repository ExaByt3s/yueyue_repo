<?php


/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

/**
 * �жϿͻ���
 */
$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 

// ͨ��
include_once($file_dir.'/./pc_common.inc.php');

// Ȩ���ļ�
include_once($file_dir.'/./pc_auth_common.inc.php');

// ͷ��css���
include_once($file_dir. '/./webcontrol/head.php');

// ������
include_once($file_dir. '/./webcontrol/global-top-bar.php');

// �ײ�
include_once($file_dir. '/./webcontrol/footer.php');

// ��������
include_once($file_dir. '/./webcontrol/down-app-area.php');


// ================== ����ģ�� ==================
$tpl = $my_app_pai->getView('login.tpl.htm');




$r_url = urldecode(trim($_INPUT['r_url']));//��Դ��ַ

$tpl->assign('r_url',$r_url);


// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);

// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);


// ΢��С��Ȩ

if($__is_weixin && !$_COOKIE['yueus_openid'])
{
	$url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; // ��¼ҳ��ַ
	$url2 = $r_url;// ��¼�ɹ����Ŀ�ĵ�ַ
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