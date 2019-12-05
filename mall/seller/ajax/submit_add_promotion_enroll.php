<?php
/**
 * 提交报名
 * 2015.9.25
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

$price_arr = poco_iconv_arr($_REQUEST['detail'],'UTF-8','GBK');
//$price_arr = $_REQUEST['detail'];
// print_r($price_arr);
$topic_id = intval($_INPUT['topic_id']);

$mall_topic_obj = POCO::singleton('pai_topic_class');

if(empty($topic_id))
{
	$output_arr['msg']  = 'topic_id 不能为空';
	$output_arr['code'] = 0;
	$output_arr['data'] = $ret;
	mall_mobile_output($output_arr,false);
	exit();
}

if(empty($yue_login_id))
{
    $output_arr['msg']  = 'yue_login_id 不能为空';
    $output_arr['code'] = 0;
    $output_arr['data'] = $ret;
    mall_mobile_output($output_arr,false);
    exit();
}
$i=0;
foreach($price_arr as $k=>$p_val)
{
    foreach($p_val['price_arr'] as $vk=>$vp_val)
    {
        if(empty($vp_val['is_select']))
        {

            $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$p_val['goods_id'],$vp_val['type_key'],$vp_val['num'],$vp_val['price_text'],'delete');
        }
        else
        {
            $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$p_val['goods_id'],$vp_val['type_key'],$vp_val['num'],$vp_val['price_text'],'add');
            $i++;
        }
    }
}
//print_r($price_arr);




// 提交成功
$output_arr['msg']  = "恭喜你已成功添加{$i}项服务报名本次活动，报名结果会通过约约客服信息通知你";
	  
$ret['url'] = './list.php';


$output_arr['code'] = 1;
$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);


?>