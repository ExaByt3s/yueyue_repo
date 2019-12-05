<?php

/**
 * 获取我的活动列表
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


/**
 * 页面接收参数
 */

$pai_payment_obj = POCO::singleton('pai_payment_class');

$ret = $pai_payment_obj->get_bail_available_balance($yue_login_id);

$output_arr['data'] = $ret;
$output_arr['msg'] = mb_convert_encoding('获取成功', 'gbk','utf-8');
$output_arr['code'] = 1;


mobile_output($output_arr,false);

?>
