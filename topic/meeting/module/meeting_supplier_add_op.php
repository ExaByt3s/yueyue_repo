<?php
/** /* 
 * ��ṩӦ�����ҳ,for pc����ͨWAP
 * 
 * author ����
 * 
 * 2015-4-2
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_supplier_obj   = POCO::singleton('pai_summit_meeting_supplier_class');

//��ȡ�������
$name = trim($_INPUT['name']);
$company = trim($_INPUT['company']);
$phone = (int)$_INPUT['phone'];
$intro = trim($_INPUT['intro']);//��֤��

$ajax_status = 1;

//У������
if(empty($name))
{
    $error_tips = "name_empty";
    $ajax_status = 0;

}
//У�鹫˾��
if(empty($company))
{
    $error_tips = "company_empty";
    $ajax_status = 0;

}

//У���ֻ�
if(empty($phone))
{
    $error_tips = "phone_empty";
    $ajax_status = 0;
}

//У���ֻ�
if(empty($intro))
{
    $error_tips = "intro_empty";
    $ajax_status = 0;
}


if($ajax_status==1)
{
    $insert_data['name'] = iconv("UTF-8","GB2312//IGNORE",$name);
    $insert_data['company'] = iconv("UTF-8","GB2312//IGNORE",$company);
    $insert_data['phone'] = $phone;
    $insert_data['intro'] = iconv("UTF-8","GB2312//IGNORE",$intro);
    
    

    $res = $summit_meeting_supplier_obj->add_summit_meeting_supplier($insert_data);
    
}


if(!$res)
{
    $ajax_status = 0;
    $error_tips = "insert_error";
}

$res_arr = array(
"ajax_status"=>$ajax_status,
"error_tips"=>$error_tips
);

echo json_encode($res_arr);

?>