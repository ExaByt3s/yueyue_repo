<?php
$osver		= $_REQUEST['osver'];
$appver		= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi		= $_REQUEST['iswifi'];

$latest_version		= '1.0.3';   //最新版本
$latest_down_url	= 'http://c.poco.cn/y201505141232.apk'; //版本下载地址
$latest_notice		= '1.首页全新改版，模特榜单一目了然, 
                       2. 加入了外拍频道，新增约约专属活动报名
                       3. 新增心愿单，方便个性拍摄订制'; 

$lowest_version     = '1.0.3';
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