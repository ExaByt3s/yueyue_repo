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

//影棚租赁

//配置特定数组
$certificate_studio_area = pai_mall_load_config('certificate_studio_area');//影棚面积
$certificate_studio_can_photo = pai_mall_load_config('certificate_studio_can_photo');//可拍摄类型
$certificate_studio_photo_type = pai_mall_load_config('certificate_studio_photo_type');//拍摄类型
$certificate_studio_lighter = pai_mall_load_config('certificate_studio_lighter');//灯光设备
$certificate_studio_other = pai_mall_load_config('certificate_studio_other');//其他配套
//处理配置数组数据结构

$certificate_studio_area_new = array_to_square_array($certificate_studio_area);
$certificate_studio_can_photo_new = array_to_square_array($certificate_studio_can_photo);
$certificate_studio_photo_type_new = array_to_square_array($certificate_studio_photo_type);
$certificate_studio_lighter_new = array_to_square_array($certificate_studio_lighter);
$certificate_studio_other_new = array_to_square_array($certificate_studio_other);


$tpl->assign("certificate_studio_area",$certificate_studio_area_new);
$tpl->assign("certificate_studio_can_photo",$certificate_studio_can_photo_new);
$tpl->assign("certificate_studio_photo_type",$certificate_studio_photo_type_new);
$tpl->assign("certificate_studio_lighter",$certificate_studio_lighter_new);
$tpl->assign("certificate_studio_other",$certificate_studio_other_new);




?>
