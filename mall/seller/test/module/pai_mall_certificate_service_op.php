<?php

/**
 * 服务认证操作页
 *
 * 2015-6-16
 *
 * author  星星
 *
 */

include_once '../common.inc.php';


$type_id = (int)$_INPUT['type_id'];

$pai_mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');

if(empty($yue_login_id))
{
    echo "<script>top.alert('没有登录');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

$user_id = $yue_login_id;


$type_id_array = pai_mall_load_config('certificate_service_type_id');//type_id数组
$type_id_service_type = pai_mall_load_config('certificate_service_type_id_service_type');//type_id

$service_type = $type_id_service_type[$type_id];

if(empty($type_id) || !in_array($type_id,$type_id_array))
{
    echo "<script>top.alert('类型ID 错误');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}


$patt = "/undefined/";



//根据类型选择不同的代码段
$include_once_url = "mod/pai_mall_certificate_service_op_".$type_id.".php";

if(file_exists($include_once_url))
{

    include_once $include_once_url;

}
else
{
    echo "<script>top.alert('处理文件不存在');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}










?>