<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi = $_REQUEST['iswifi'];

$latest_version		= '1.0.0';   //最新版本
//$latest_down_url	= 'http://itunes.apple.com/cn/app/id935185009?mt=8'; //版本下载地址
$latest_down_url	= 'http://app.yueus.com/'; 
$latest_notice		= '约约v1.0.1: ||新增2套新装饰，让照片立刻升级！还有适配iOS8，亲们一定要更新哦~||△  泡泡光+彩虹=梦幻，新增多款极致光效||△  “照片定制”冲印服务，把你的作品和纪念的照片打印出来！|| △  优化图片保存画质，多次美化不会降低画质。'; 

$lowest_version     = '1.0.0';
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