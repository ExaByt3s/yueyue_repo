<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');


$url = urldecode($_INPUT['url']);
$platform = $_INPUT['platform'];
$phone = POCO::singleton('pai_user_class')->get_phone_by_user_id($yue_login_id);

$trigger_obj = POCO::singleton('pai_trigger_class');
$params['url'] = $url;
$params['user_id'] = $yue_login_id;
$params['cellphone'] = $phone;
$params['platform'] = $platform;
$trigger_obj->weixin_share_after($params);


?>