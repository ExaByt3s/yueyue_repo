<?php


// include_once '../common.inc.php';
define('MALL_SELLER_IS_NOT_LOGIN',1);
include_once '../../common.inc.php';


$cellphone =  iconv("UTF-8","GBK",$_INPUT['cellphone']);
$name = iconv("UTF-8","GBK",$_INPUT['name']);
$type_name = iconv("UTF-8","GBK",$_INPUT['type_name']);


if (empty($name)) 
{
    $msg = '请输入联系人';

}


if (empty($cellphone)) 
{
    $msg = '请输入手机号码';
}


if (empty($type_name)) 
{
    $msg = '请选择品类';
}


$obj = POCO::singleton( 'pai_topic_class' );
$ret = $obj -> add_daiyunying_enroll($cellphone, $name, $type_name);

$output_arr = array(
    'msg'=> $msg,
    'code'=> $ret
);

echo json_encode($output_arr);


?>