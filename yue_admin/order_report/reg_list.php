<?php

include_once 'common.inc.php';

$tpl = new SmartTemplate("reg_list.tpl.htm");

$month = $_INPUT['month'] ? $_INPUT['month'] : "2015-10";


$sql = "SELECT * FROM test.temp_reg_user_tj_tbl";
$list = db_simple_getdata($sql);

$sql = "SELECT * FROM test.temp_reg_user_tj_tbl WHERE month='{$month}'";
$one_month = db_simple_getdata($sql,true);

$sql = "SELECT sum(reg_num) as num,ROUND(SUM(reg_num)/COUNT(*)) as average_reg FROM test.temp_reg_user_tj_tbl WHERE month<='{$month}'";
$reg_num_arr = db_simple_getdata($sql,true);
$total_reg_num = $reg_num_arr['num'];
$total_average_reg = $reg_num_arr['average_reg'];

$current_month_ratio = round(($one_month['reg_num']/$total_reg_num)*100,2);

$one_month['total_reg_num'] = $total_reg_num;
$one_month['average_reg'] = $total_average_reg;
$one_month['current_month_ratio'] = $current_month_ratio;

$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_MOBILE_ADMIN_REPORT_HEADER);
$tpl->assign('reg_json', json_encode($list));
$tpl->assign('month', $month);
$tpl->assign('one_month', $one_month);


$tpl->output();

?>