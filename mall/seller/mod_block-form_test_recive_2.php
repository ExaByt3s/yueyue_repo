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
    $msg  =  'please  inpu  your time long ' ;
}
else
{
        $code  = 1 ;
        $msg  =  'right' ;

}





// xxxxxxxxxxx




$arr  =  array
(
            "code"  =>  $code ,
            "msg" =>  $msg 
);





echo json_encode($arr );






// if (empty($shoot_time_val)) 
// {
//     echo iconv('GB2312', 'UTF-8', "�����롣������");
//     exit();
// }
// if (!$negative_number_val) 
// {
//     echo "�������Ƭ����";
//     exit();
// }
// if (!$retouching_time_val) 
// {
//     echo "��������Ƭʱ��";
//     exit();
// }
// if (!$retouching_number_val) 
// {
//     echo "�����뾫������";
//     exit();
// }
// if (!$clothing_select_val || $clothing_select_val == "��ѡ��") 
// {
//     echo "��ѡ���װ";
//     exit();
// }
// if (!$makeup_select_val || $makeup_select_val=="��ѡ��") 
// {
//     echo "��ѡ��ױ";
//     exit();
// }
// if (!$phone_select_val || $phone_select_val=="��ѡ��") 
// {
//     echo "��ѡ�����";
//     exit();
// }
// if (!$raw_radio_val) 
// {
//     echo "��ѡ��ԭƬ";
//     exit();
// }



// ����У��






?>