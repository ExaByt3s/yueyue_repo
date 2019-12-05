<?php 
/**
* @file risk_check_scalping_seller.php
* @synopsis 刷单商家自动检查
* @author wuhy@yueus.com
* @version null
* @date 2015-10-21
 */

ignore_user_abort(true);
set_time_limit(3600);//超时时间, 触发频率 应调整为合适的值, 避免服务器线程急剧加大
ini_set('memory_limit', '512M');

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$op = trim($_INPUT['op']);
if( $op!='run')
{
	die('op error!');
}

$pai_risk_obj = POCO::singleton('pai_risk_class');
$mall_seller_obj = POCO::singleton('pai_mall_seller_class');
$min_time = strtotime('yesterday');
$max_time = strtotime('today')-1;

$sync_rst = $pai_risk_obj->sync_order($min_time, $max_time);

echo '同步订单结果'. var_export($sync_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n\r\n";

$where_str = "sign_time>={$min_time} AND sign_time<={$max_time}";
$seller_user_list = $pai_risk_obj->get_seller_list_for_check(false, $where_str, 'type_id,id', '0,99999999');
foreach($seller_user_list as $seller)
{
    // $seller_info =  $mall_seller_obj->get_seller_info($user_id,2);
    // if( $seller_info['seller_data']['is_black']==1 ) continue;

    $check_rst = $pai_risk_obj->check_all_rule($seller['seller_user_id'], $min_time, $max_time, $seller['type_id']);
    $log_id = 0;
    if( $check_rst['is_scalping'] )//添加刷单检查记录
    {
        $log_id = $pai_risk_obj->add_scalping_check_log($check_rst['seller_user_id'], $check_rst['rule_type_id'], $check_rst['rule_code_m'], $min_time, $max_time, $check_rst['type_id']);
    }

    if($log_id>0)//添加刷单商家待处理记录
    {
        $remark = date('Y-m-d H:i')." 系统检查，品类：{$check_rst['rule_type_id']}，时段".date('Y/m/d H:i:s', $min_time)."~".date('Y/m/d H:i:s', $max_time)."，命中规则：{$check_rst['rule_code_m']}，检查记录：{$log_id}。";
        $pai_risk_obj->add_scalping_seller_auto($check_rst['seller_user_id'], array('remark' => $remark));
        echo $remark;
    }
    echo '商家ID'.$user_id.' 检查结果：'. var_export($check_rst, true) . ' ' . date("Y-m-d H:i:s")."\r\n";
}
