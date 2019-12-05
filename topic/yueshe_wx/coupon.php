<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$phone = $_INPUT['phone'];

if(!$phone)
{
	exit;
}

$insert_data['phone'] = $phone;
$insert_data['add_time'] = time();
$insert_str = db_arr_to_update_str($insert_data);


$sql = "insert into pai_db.pai_yueshe_weixin_topic_coupon_tbl set ".$insert_str;
db_simple_getdata($sql,false,101);


$trigger_obj = POCO::singleton('pai_trigger_class');
$params['url'] = "http://www.yueus.com/topic/yueshe_wx/";
$params['cellphone'] = $phone;
$trigger_obj->weixin_share_after($params);


?>