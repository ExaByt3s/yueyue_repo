<?php
/** 
 * 
 * 峰会供应商提交页
 * 
 * author 星星
 * 
 * 
 * 2015-4-20
 */


include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$tpl = $my_app_pai->getView('photo_meeting_wap_success.tpl.htm');
$type=trim($_INPUT['type']);
if(!in_array($type,array("user","supplier")))
{
    $type = "user";
}

$tpl->assign("type",$type);
$tpl->output();
?>