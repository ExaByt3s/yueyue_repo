<?php
/*
 *
 * 服务认证模块
 *
 *
 *
 *
 *
 */

//摄影服务

//配置特定数组
$certificate_cameror_work_type = pai_mall_load_config('certificate_cameror_work_type');//作品类型
$certificate_common_years = pai_mall_load_config('certificate_common_years');//时间年限
$certificate_cameror_order_income = pai_mall_load_config('certificate_cameror_order_income');//摄影师月均收入
$certificate_cameror_team = pai_mall_load_config('certificate_cameror_team');//团队构成
//处理配置数组数据结构
$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_cameror_work_type_new = array_to_square_array($certificate_cameror_work_type);
$certificate_cameror_order_income_new = array_to_square_array($certificate_cameror_order_income);
$certificate_cameror_team_new = array_to_square_array($certificate_cameror_team);

$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_cameror_work_type",$certificate_cameror_work_type_new);
$tpl->assign("certificate_cameror_order_income",$certificate_cameror_order_income_new);
$tpl->assign("certificate_cameror_team",$certificate_cameror_team_new);



?>