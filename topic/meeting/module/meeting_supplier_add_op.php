<?php
/** /* 
 * 峰会供应商添加页,for pc跟普通WAP
 * 
 * author 星星
 * 
 * 2015-4-2
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
define("G_DB_GET_REALTIME_DATA",1);
$summit_meeting_supplier_obj   = POCO::singleton('pai_summit_meeting_supplier_class');

//获取相关数据
$name = trim($_INPUT['name']);
$company = trim($_INPUT['company']);
$phone = (int)$_INPUT['phone'];
$intro = trim($_INPUT['intro']);//验证码

$ajax_status = 1;

//校验名字
if(empty($name))
{
    $error_tips = "name_empty";
    $ajax_status = 0;

}
//校验公司名
if(empty($company))
{
    $error_tips = "company_empty";
    $ajax_status = 0;

}

//校验手机
if(empty($phone))
{
    $error_tips = "phone_empty";
    $ajax_status = 0;
}

//校验手机
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