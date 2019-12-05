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

if(empty($topic_id))
{
	$output_arr['msg']  = 'topic_id 不能为空';
	$output_arr['code'] = 0;
	$output_arr['data'] = $ret;
	mall_mobile_output($output_arr,false);
	exit();
}

foreach($price_arr as $k=>$p_val)
{
    foreach($p_val['price_arr'] as $vk=>$vp_val)
    {
        if(!empty($vp_val['is_select']))
        {
            $new_price_arr[$k]['goods_id'] = $p_val['goods_id'];
            $new_price_arr[$k]['price_arr'][$vk] = $vp_val;
        }
    }
}
//print_r($price_arr);



$mall_topic_obj = POCO::singleton('pai_topic_class');
$ret = $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$new_price_arr);


// 提交成功
if($ret['result'] == 1)
{
      $output_arr['msg']  = $ret['message'];
	  
	  $ret['url'] = './list.php';
}
else
{
	  $output_arr['msg']  = $ret['message'];
}

$output_arr['code'] = $ret['result'];
$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);


?>