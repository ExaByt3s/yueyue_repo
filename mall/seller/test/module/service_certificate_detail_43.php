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

//其他认证

//配置固定数组
$certificate_other_identifine_label = pai_mall_load_config('certificate_other_identifine_label');//身份标签

//处理配置数组数据结构
$certificate_other_identifine_label_new = array_to_square_array($certificate_other_identifine_label);
$tpl->assign("certificate_other_identifine_label",$certificate_other_identifine_label_new);


?>