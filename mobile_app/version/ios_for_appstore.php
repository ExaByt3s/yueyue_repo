<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$osver  	= $_REQUEST['osver'];
$appver 	= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi 	= $_REQUEST['iswifi'];
$user_id    = $_REQUEST['uid'];

$pai_obj    = POCO::singleton('pai_user_class');
$role       = $pai_obj->check_role($user_id);

$latest_version		= '3.2.10';   //最新版本
$latest_down_url	= 'https://itunes.apple.com/cn/app/yueyue/id935185009?l=zh&ls=1&mt=8'; //版本下载地址
//$latest_notice		= '首页全新改本，更清晰逼格更高||新增摄影服务，更多摄影培训、化妆师、影棚入驻';

$latest_notice		= '1、新增收藏功能：商家/服务可以收藏了！快去添加收藏吧||2、新增促销功能：可以查看服务的促销信息，并享受优惠||3、其他细节优化：增加首页搜索、筛选，新增开通城市等';

$lowest_version     = '3.2.10';
$lowest_notice      = '发现新版本，修复了ios9的一些问题：||及时接收服务商回复信息，修复分享微信等平台服务，点击马上更新。';

$array_result['result']     = "0000";         
$array_result['update']     = 0;     


//if($user_id == 100008) {
	if(version_compare($appver, $lowest_version, '<'))
	{
		//版本低于最低版本，强制更新
		$array_result['result']     = "0000";
		$array_result['update']     = 2;
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
	}elseif(version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')){
		//版本低于最新版本，高于最低版本，建议更新
		$array_result['result']     = "0000";
		$array_result['update']     = 1;
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $lowest_notice);
	}else{
		//不需要更新
		$array_result['result']     = "0000";
		$array_result['update']     = 0;
	}
/*}else{
	//不需要更新
	$array_result['result']     = "0000";
	$array_result['update']     = 0;
}*/


echo json_encode($array_result);
?>