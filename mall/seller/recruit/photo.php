<?php

// include_once '../common.inc.php';
define('MALL_SELLER_IS_NOT_LOGIN',1);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
define('TASK_TEMPLATES_ROOT',"templates/default/");

if(false!==stripos($_SERVER['HTTP_USER_AGENT'], 'android') || false!==stripos($_SERVER['HTTP_USER_AGENT'], 'iphone'))
{
    //wap°æ
    $pc_wap = 'wap/';
    $tpl = $my_app_pai->getView('../'.TASK_TEMPLATES_ROOT.$pc_wap.'recruit/photo.tpl.htm');


    require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
    $wx = mall_wx_get_js_api_sign_package();
    $wx_json = json_encode($wx);
    $tpl->assign('wx_json', $wx_json);


}
else
{
    //Pc°æ
    $pc_wap = 'pc/';
    $tpl = $my_app_pai->getView('../'.TASK_TEMPLATES_ROOT.$pc_wap.'recruit/photo.tpl.htm');


}




$tpl->output();


?>