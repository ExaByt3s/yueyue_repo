<?php
/**
 * android ������ҳ����
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
$lowest_version = '1.0.2'; // ��Ͱ汾�ţ��˴����������޸�
$web_package_version_dir = '1.0.2';//���°汾�ŵ�·���ļ��� ��ҳ������Դ��

$web_package_arr =array(
		'1.0.0' => array(
					'dir' =>'0.0.2',
					'package_ver' => '1.0.023'
					),
		'1.0.1' => array(
					'dir' =>'0.0.3',
					'package_ver' => '1.0.114'
					),
		'1.0.2' => array(
					'dir' =>'1.0.2',
					'package_ver' => '1.0.2'
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
$output['isShowAppTips'] = 1;
$output['appUpdateTips'] = mb_convert_encoding("�ף����������ޣ���ˢ�£�" , 'utf-8','gbk');

echo json_encode($output);

if($_REQUEST['print']==1)
{
	var_dump($output);
	exit;
}

?>