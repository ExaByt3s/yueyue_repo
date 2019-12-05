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

//美食达人

//配置固定数组
$certificate_diet_identification = pai_mall_load_config('certificate_diet_identification');//认证身份
$certificate_diet_job = pai_mall_load_config('certificate_diet_job');//职业
$certificate_diet_max_forward = pai_mall_load_config('certificate_diet_max_forward');//微博转发量
$certificate_diet_years = pai_mall_load_config('certificate_diet_years');//作品类型

//处理配置数组数据结构
$certificate_diet_identification_new = array_to_square_array($certificate_diet_identification);
$certificate_diet_job_new = array_to_square_array($certificate_diet_job);
$certificate_diet_max_forward_new = array_to_square_array($certificate_diet_max_forward);
$certificate_diet_years_new = array_to_square_array($certificate_diet_years);




$tpl->assign("certificate_diet_identification",$certificate_diet_identification_new);
$tpl->assign("certificate_diet_job",$certificate_diet_job_new);
$tpl->assign("certificate_diet_max_forward",$certificate_diet_max_forward_new);
$tpl->assign("certificate_diet_years",$certificate_diet_years_new);


?>