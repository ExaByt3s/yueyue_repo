<?php

/**
 * ������֤����ҳ
 *
 * 2015-6-16
 *
 * author  ����
 *
 */

include_once '../common.inc.php';


$type_id = (int)$_INPUT['type_id'];

$pai_mall_certificate_service_obj = POCO::singleton('pai_mall_certificate_service_class');

if(empty($yue_login_id))
{
    echo "<script>top.alert('û�е�¼');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}

$user_id = $yue_login_id;


$type_id_array = pai_mall_load_config('certificate_service_type_id');//type_id����
$type_id_service_type = pai_mall_load_config('certificate_service_type_id_service_type');//type_id

$service_type = $type_id_service_type[$type_id];

if(empty($type_id) || !in_array($type_id,$type_id_array))
{
    echo "<script>top.alert('����ID ����');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}


$patt = "/undefined/";



//��������ѡ��ͬ�Ĵ����
$include_once_url = "mod/pai_mall_certificate_service_op_".$type_id.".php";

if(file_exists($include_once_url))
{

    include_once $include_once_url;

}
else
{
    echo "<script>top.alert('�����ļ�������');top.document.getElementById('J_form_submit').setAttribute('data-lock','unlock');</script>";
    exit();
}










?>