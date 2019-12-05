<?php

/**
 * 获取用户信息
 */

include_once('../common.inc.php');

$event_id = intval($_INPUT['event_id']);
$enroll_id = intval($_INPUT['enroll_id']);

$ret = get_act_ticket_detail($event_id,$enroll_id);


// 二维码图片转换 start 
$activity_code_obj = POCO::singleton('pai_activity_code_class');
foreach ($ret as $k => $val) 
{
    $qr_code_url_img = $activity_code_obj->get_qrcode_img($val['qr_code_url']);
    $ret[$k]['qr_code_url_img'] = $qr_code_url_img;
}
// 二维码图片转换 end

$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);




?>
