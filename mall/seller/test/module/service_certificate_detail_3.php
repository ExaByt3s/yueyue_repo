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


//化妆服务

//配置特定数组
$certificate_common_years = pai_mall_load_config('certificate_common_years');//时间年限
$certificate_dresser_has_place = pai_mall_load_config('certificate_dresser_has_place');//化妆场地
$certificate_dresser_order_way = pai_mall_load_config('certificate_dresser_order_way');//接单方式
$certificate_dresser_team_num = pai_mall_load_config('certificate_dresser_team_num');//团队规模
$certificate_dresser_do_well = pai_mall_load_config('certificate_dresser_do_well');//擅长的妆容

//处理配置数组数据结构

$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_dresser_has_place_new = array_to_square_array($certificate_dresser_has_place);
$certificate_dresser_order_way_new = array_to_square_array($certificate_dresser_order_way);
$certificate_dresser_team_num_new = array_to_square_array($certificate_dresser_team_num);
$certificate_dresser_do_well_new = array_to_square_array($certificate_dresser_do_well);



$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_dresser_has_place",$certificate_dresser_has_place_new);
$tpl->assign("certificate_dresser_order_way",$certificate_dresser_order_way_new);
$tpl->assign("certificate_dresser_team_num",$certificate_dresser_team_num_new);
$tpl->assign("certificate_dresser_do_well",$certificate_dresser_do_well_new);






?>