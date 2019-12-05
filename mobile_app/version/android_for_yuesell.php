<?php
$osver		= $_REQUEST['osver'];
$appver		= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi		= $_REQUEST['iswifi'];

//版本号整理，去掉后续
$array_str  = explode('_', $appver);
$appver     = $array_str[0];

$latest_version		= '1.2.0';   //最新版本
$latest_down_url	= 'http://c.poco.cn/ys201510301335.apk'; //版本下载地址
$latest_notice		= '1、全新首页：入口明了清晰，轻松管理交易；
2、新增发布服务功能：各大品类服务，手机端随时编辑；
3、新增促销中心：开放多种促销活动报名入口 ；
4、新增接单中心：更多接单机会，尽在手中；';

$lowest_version     = '1.2.0';
$lowest_notice      = '你还可以继续使用，但建议更新到最新版本';

$array_result['result']     = "0000";         
$array_result['update']     = 0;

//if($_REQUEST['uid'] == 100008)
{
    if($appver){
        if(version_compare($appver, $lowest_version, '<'))
        {
            //版本低于最低版本，强制更新
            $array_result['result']     = "0000";
            $array_result['update']     = 2;
            $array_result['version']    = '2.2.1';
            $array_result['app_url']    = $latest_down_url;
            $array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
        }elseif(version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')){
            //版本低于最新版本，高于最低版本，建议更新
            $array_result['result']     = "0000";
            $array_result['update']     = 1;
            $array_result['version']    = '2.2.1';
            $array_result['app_url']    = $latest_down_url;
            $array_result['detail']     = iconv('GBK', 'UTF-8', $lowest_notice);
        }else{
            //不需要更新
            $array_result['result']     = "0000";
            $array_result['update']     = 0;
        }
    }
}


echo json_encode($array_result);

?>