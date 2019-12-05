<?php

 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$quotes_id = $_INPUT['quotes_id'];

$tpl = $my_app_pai->getView('pay.tpl.htm');

$tpl->assign('time', time());  //随机数

$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$quote_info = $task_quotes_obj->get_quotes_detail_info_by_id($quotes_id);

$quote_info['pay_coins'] = $quote_info['pay_coins']*1;

$tpl->assign('quote_info', $quote_info);



/*
 * 获取用户生意卡数
 * @param int $user_id
 * @return array
 */
 $task_coin_obj = POCO::singleton('pai_task_coin_class');
$coin_info = $task_coin_obj->get_coin_info($yue_login_id);
$balance = $coin_info['balance'];
$tpl->assign('balance', $balance*1);

/*
 * 获取当前服务需要的生意卡数
 * @param int $service_id
 * @return array
 */
$task_service_obj = POCO::singleton('pai_task_service_class');
$service_info = $task_service_obj->get_service_info($lead_info['service_id']);
$pay_coins = $service_info['pay_coins'];
$tpl->assign('pay_coins', $pay_coins);

$tpl->output();
 ?>