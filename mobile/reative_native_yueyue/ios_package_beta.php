<?php
/**
 * ios 请求网页更新
 * 每次发布应该要修改
 * $lowest_version,$web_package_version_dir这两个版本号
 */
header('Content-Type: application/json');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$version = require_once('/disk/data/htdocs232/poco/pai/mobile/config/version_control.conf.php');


$osver = $_INPUT['osver'];
$appver = $_INPUT['appver'];
$modelver = $_INPUT['modelver'];
$reactver = $_INPUT['reactver'];
$bs_version = $_INPUT['bs_version'];

/*************************此处用于配置********************************/
$lowest_version = '3.0.0'; // 最低版本号，此处不能随意修改
$web_package_version_dir = '3.0.0';//最新版本号的路径文件， 网页最新资源包
    $ver_array = explode('.', $appver);
    
    if(count($ver_array) == 3) {
        if($ver_array[2] >= 10) {
            $lowest_version = '3.0.10'; // 最低版本号，此处不能随意修改
            $web_package_version_dir = '3.0.0';//最新版本号的路径文件， 网页最新资源包
        }
    }

$web_package_arr =array(
		'3.0.0' => array(
					'dir' =>'3.0.0',
					'package_ver' => '3.0.001'
					),
        '3.0.10' => array(
                    'dir' =>'3.0.0',
                    'package_ver' => '3.0.001'
                    )
	);
/*************************此处用于配置********************************/

    if($bs_version == '88.8.8') {
        $web_package_arr =array(
                                '3.0.0' => array(
                                                 'dir' =>'3.0.0',
                                                 'package_ver' => '3.0.002'
                                                 ),
                                '3.0.10' => array(
                                                  'dir' =>'3.0.0',
                                                  'package_ver' => '3.0.002'
                                                  )
                                );
        
        foreach ($web_package_arr as $key => $value)
        {
            if(version_compare($appver,$lowest_version,">="))
            {
                $web_package_arr[$key]['dir'] = $web_package_version_dir;
            }
        }
        
        $output['needUpdate']='YES';
        
        $output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/reative_native_yueyue/'.$web_package_arr[$appver]['dir'].'/app_test.zip';
        $output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/reative_native_yueyue/'.$web_package_arr[$appver]['dir'].'/app_test.zip';
    }else {
        foreach ($web_package_arr as $key => $value)
        {
            if(version_compare($appver,$lowest_version,">="))
            {
                $web_package_arr[$key]['dir'] = $web_package_version_dir;
            }
        }
        
        if (version_compare($reactver,$web_package_arr[$appver]['package_ver'],"<")) {
            $output['needUpdate']='YES';
        }else{
            $output['needUpdate']='NO';
        }
        
        $output['packageUrl'] = 'http://yp-wifi.yueus.com/mobile/reative_native_yueyue/'.$web_package_arr[$appver]['dir'].'/app.zip';
        $output['no_wifi_packageUrl'] = 'http://yp.yueus.com/mobile/reative_native_yueyue/'.$web_package_arr[$appver]['dir'].'/app.zip';
    }



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