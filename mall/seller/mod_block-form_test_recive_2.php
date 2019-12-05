<?php

/**
 * 商品服务操作页（添加或者编辑）
 *
 * 2015-6-17
 *
 * author  星星
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');


$shoot_time_val = $_INPUT['shoot_time_val']; //接受参数
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
//     echo iconv('GB2312', 'UTF-8', "请输入。。。。");
//     exit();
// }
// if (!$negative_number_val) 
// {
//     echo "请输入底片张数";
//     exit();
// }
// if (!$retouching_time_val) 
// {
//     echo "请输入修片时间";
//     exit();
// }
// if (!$retouching_number_val) 
// {
//     echo "请输入精修张数";
//     exit();
// }
// if (!$clothing_select_val || $clothing_select_val == "请选择") 
// {
//     echo "请选择服装";
//     exit();
// }
// if (!$makeup_select_val || $makeup_select_val=="请选择") 
// {
//     echo "请选择化妆";
//     exit();
// }
// if (!$phone_select_val || $phone_select_val=="请选择") 
// {
//     echo "请选择相册";
//     exit();
// }
// if (!$raw_radio_val) 
// {
//     echo "请选择原片";
//     exit();
// }



// 数据校验






?>