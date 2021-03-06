<?php
/**
 * test请求网页更新
 * 每次发布应该要修改
 * $lowest_version,$web_package_version_dir这两个版本号
 */
header('Content-Type: application/json');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');


$osver = $_INPUT['osver'];
$appver = $_INPUT['appver'];
$modelver = $_INPUT['modelver'];


/*************************此处用于配置********************************/
$lowest_version = '3.0.0'; // 最低版本号，此处不能随意修改
$web_package_version_dir = '3.0.0';//最新版本号的路径文件， 网页最新资源包

$web_package_arr =array(	
		'1.0.5' => array(
					'dir' =>'1.0.5',
					'package_ver' => '1.0.525'
					),
		'1.0.6' => array(
					'dir' =>'1.0.6',
					'package_ver' => '1.0.626'
					),
		'1.1.0' => array(
					'dir' =>'1.1.0',
					'package_ver' => '1.1.000'
					),
		'2.0.0' => array(
					'dir' =>'2.0.0',
					'package_ver' => '2.0.000'
					),
		'2.1.0' => array(
					'dir' =>'2.1.0',
					'package_ver' => '2.1.013'
					),	
		'2.2.0' => array(
					'dir' =>'2.2.0',
					'package_ver' => '2.2.020'
					),
		'3.0.0' => array(
					'dir' =>'3.0.0',
					'package_ver' => '3.0.000'
					)					
	);
/*************************此处用于配置********************************/

foreach ($web_package_arr as $key => $value) 
{
	if(version_compare($appver,$lowest_version,">="))
	{
		$web_package_arr[$key]['dir'] = $web_package_version_dir;
	}
	
}



$output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/package/resource/'.$web_package_arr[$appver]['dir'].'/app.zip';
$output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/package/resource/'.$web_package_arr[$appver]['dir'].'/app.zip';
$output['packageVer'] = $web_package_arr[$appver]['package_ver'];
$output['packageForceUpdate'] = 'http://yp-wifi.yueus.com/mobile/app';
$output['enterUrl'] = 'http://yp-wifi.yueus.com/mobile/app';
$output['no_wifi_enterUrl'] = 'http://yp.yueus.com/mobile/app';
$output['appForceUpdate'] = 0;
$output['isShowAppTips'] = 0;
$output['appUpdateTips'] = mb_convert_encoding("亲，有新内容噢，请刷新！" , 'utf-8','gbk');

echo json_encode($output);

if($_REQUEST['print']==1)
{
	var_dump($output);
	exit;
}

?>