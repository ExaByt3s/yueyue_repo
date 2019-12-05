<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/25
 * Time: 9:16
 */
include_once 'poco_app_common.inc.php';

$ua_array = mall_get_user_agent_arr();

$url = '';
if($ua_array['is_iphone'] == 1) {
    $url = 'http://www.yueus.com/mall/user/topic/index.php?topic_id=844&online=1';
}else{
    $url = 'http://www.yueus.com/mall/user/topic/index.php?topic_id=849&online=1';
}
header('Location:' . $url);