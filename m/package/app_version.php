<?php

header('Content-Type: application/json');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');

$osver = $_INPUT['osver'];
$appver = $_INPUT['appver'];

$latest_version = '1.0.2'; // 最新版本号，此处不能随意修改
$lowest_version = '1.0.1'; // 最低版本号，此处不能随意修改

$web_package_version_dir = '0.0.4';

$web_package_arr =array(
		'1.0.0' => array(
					'dir' =>'0.0.2',
					'package_ver' => '1.0.023'
					),
		'1.0.1' => array(
					'dir' =>'0.0.3',
					'package_ver' => '1.0.100'
					),
		'1.0.2' => array(
					'dir' =>'0.0.4',
					'package_ver' => '1.0.200'
					)
	);

foreach ($web_package_arr as $key => $value) 
{
	if($appver >= $lowest_version)
	{
		$web_package_arr[$key]['dir'] = $web_package_version_dir;
	}
	
}



$output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/package/resource/'.$web_package_arr[$appver]['dir'].'/app.zip';
$output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/package/resource/'.$web_package_arr[$appver]['dir'].'/app.zip';
$output['packageVer'] = $web_package_arr[$appver]['package_ver'];
$output['packageForceUpdate'] = 'http://yp-wifi.yueus.com/mobile/app';
$output['enterUrl'] = 'http://yp.yueus.com/mobile/app';
$output['no_wifi_enterUrl'] = '1.0';
$output['appForceUpdate'] = 0;
$output['isShowAppTips'] = 1;
$output['appUpdateTips'] = "亲，有新内容噢，请刷新！" ;

echo json_encode($output);

if($_REQUEST['print']==1)
{
	var_dump($output);
	exit;
}

?>