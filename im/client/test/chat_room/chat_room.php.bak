<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
include_once '../api_rest.php';

// ========================= 初始化接口 start =======================

// ========================= 初始化接口 end =======================


// ========================= 区分pc，wap模板与数据格式整理 start  =======================
$obj = POCO::singleton('pai_mall_operate_agent_class');
$c_ret = $obj->check_permit($yue_login_id);
if(!$c_ret)
{
	dump('无权限');
	die();
}
if(IM_UA_IS_PC == 1)
{

    //****************** pc版 ******************
    include_once './face-pc.php';
}
else
{
    //****************** wap版 ******************
    include_once './face-wap.php';
}



// ========================= 最终模板输出  =======================
$tpl->output();
?>
