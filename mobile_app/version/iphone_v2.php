<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi = $_REQUEST['iswifi'];

$latest_version		= '1.1.0';   //最新版本
$latest_down_url	= 'http://app.yueus.com/'; //版本下载地址
$latest_notice		= '1，主页全新改版，摄影师可以发布自己需求了。||2，模特可以主动推荐自己，参与报名工作机会。||3，体验优化。';

$lowest_version     = '1.1.0';
$lowest_notice      = '你还可以继续使用，但建议更新到最新版本';

$array_result['result']     = "0000";         
$array_result['update']     = 0;     

if($appver){
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
}

echo json_encode($array_result);
?>