<?php

/**
 * ��Ʒ�������ҳ����ӻ��߱༭��
 *
 * 2015-6-17
 *
 * author  ����
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');


$goods_id = (int)$_INPUT['goods_id']; //���ܲ���



// ����У��

$shoot_time_val = $_INPUT['shoot_time_val']; //���ܲ���
$negative_number_val = $_INPUT['negative_number_val'];
$retouching_time_val = $_INPUT['retouching_time_val'];
$retouching_number_val = $_INPUT['retouching_number_val'];
$clothing_select_val = $_INPUT['clothing_select_val'];
$makeup_select_val = $_INPUT['makeup_select_val'];
$phone_select_val = $_INPUT['phone_select_val'];
$raw_radio_val = $_INPUT['raw_radio_val'];





if (empty($shoot_time_val) )
{
    $code  = 2 ;
    $msg  =  'please  input  your time ' ;
}
elseif (empty($negative_number_val)) 
{
    $code  = 2 ;
    $msg  =  'please  input  your number ' ;
} 
elseif (empty($retouching_time_val)) 
{
    $code  = 2 ;
    $msg  =  'please  input  your time ' ;
} 
elseif (empty($retouching_number_val)) 
{
    $code  = 2 ;
    $msg  =  'please  input  your number ' ;
} 
elseif (!$makeup_select_val || $makeup_select_val == "��ѡ��") 
{
    $code  = 2 ;
    $msg  =  'please  choose ' ;
} 
elseif (!$makeup_select_val || $makeup_select_val == "��ѡ��") 
{
    $code  = 2 ;
    $msg  =  'please  choose ' ;
} 
elseif (!$phone_select_val || $phone_select_val == "��ѡ��") 
{
    $code  = 2 ;
    $msg  =  'please  choose ' ;
} 
elseif (empty($raw_radio_val) ) 
{
    $code  = 2 ;
    $msg  =  'please  choose radio ' ;
} 
else
{
        $code  = 1 ;
        $msg  =  'right' ;

}



$arr  =  array
(
            "code"  =>  $code ,
            "msg" =>  $msg 
);





echo json_encode($arr );








?>