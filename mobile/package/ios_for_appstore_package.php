<?php
/**
 * ios ������ҳ����
 * ÿ�η���Ӧ��Ҫ�޸�
 * $lowest_version,$web_package_version_dir�������汾��
 */
header('Content-Type: application/json');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');


$osver = $_INPUT['osver'];
$appver = $_INPUT['appver'];
$modelver = $_INPUT['modelver'];


/*************************�˴���������********************************/
$lowest_version = '2.2.10'; // ��Ͱ汾�ţ��˴����������޸�
$web_package_version_dir = '2.2.10';//���°汾�ŵ�·���ļ��� ��ҳ������Դ��

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
					'dir' =>'1.1.1',
					'package_ver' => '1.1.100'
					),
		'1.1.10' => array(
					'dir' =>'1.1.1',
					'package_ver' => '1.1.100'
					),
		'1.1.10' => array(
					'dir' =>'1.1.1',
					'package_ver' => '1.1.100'
					),
		'2.0.10' => array(
					'dir' =>'2.0.10',
					'package_ver' => '2.0.0100'
					),
		'2.1.10' => array(
					'dir' =>'2.1.10',
					'package_ver' => '2.1.101'
					),
		'2.2.10' => array(
					'dir' =>'2.2.10',
					'package_ver' => '2.2.100'
					)			
	);
/*************************�˴���������********************************/

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
$output['appUpdateTips'] = mb_convert_encoding("�ף����������ޣ���ˢ�£�" , 'utf-8','gbk');

echo json_encode($output);

if($_REQUEST['print']==1)
{
	var_dump($output);
	exit;
}

?>