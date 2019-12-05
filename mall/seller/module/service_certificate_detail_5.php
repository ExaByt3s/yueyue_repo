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





//摄影培训

//配置特定数组
$certificate_common_years = pai_mall_load_config('certificate_common_years');//时间年限
$certificate_teacher_class_way = pai_mall_load_config('certificate_teacher_class_way');//上课方式
$certificate_common_work_type = pai_mall_load_config('certificate_common_work_type');//作品类型
$certificate_teacher_type = pai_mall_load_config('certificate_teacher_type');//作品类型

//处理配置数组数据结构

$certificate_common_years_new = array_to_square_array($certificate_common_years);
$certificate_teacher_class_way_new = array_to_square_array($certificate_teacher_class_way);
$certificate_common_work_type_new = array_to_square_array($certificate_common_work_type);
$certificate_teacher_type_new = array_to_square_array($certificate_teacher_type);



$tpl->assign("certificate_common_years",$certificate_common_years_new);
$tpl->assign("certificate_teacher_class_way",$certificate_teacher_class_way_new);
$tpl->assign("certificate_common_work_type",$certificate_common_work_type_new);
$tpl->assign("certificate_teacher_type",$certificate_teacher_type_new);



?>