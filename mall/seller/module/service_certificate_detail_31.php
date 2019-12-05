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

//模特服务

//配置特定数组
$certificate_common_sex = pai_mall_load_config('certificate_common_sex');//模特性别
//处理配置数组数据结构
$certificate_common_sex_new = array_to_square_array($certificate_common_sex);


$tpl->assign("certificate_common_sex",$certificate_common_sex_new);
//配置模特CUP数组
$cup_num_array = array(
    array("cup"=>"30(65)"),
    array("cup"=>"32(70)"),
    array("cup"=>"34(75)"),
    array("cup"=>"36(80)"),
    array("cup"=>"38(85)")
);//cup的数字形式数组
$cup_english_array = array(
    array("cup"=>"A"),
    array("cup"=>"B"),
    array("cup"=>"C"),
    array("cup"=>"D"),
    array("cup"=>"E+")
);//cup的英文形式数组
$tpl->assign("cup_num_array",$cup_num_array);
$tpl->assign("cup_english_array",$cup_english_array);


?>