<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$url = trim($_GET['url']);

//$app_id = 'wx8a082d58347117f7';	//ԼԼ���Ժ�
$app_id = 'wx25fbf6e62a52d11e';	//ԼԼ��ʽ��

$weixin_helper_obj = POCO::singleton('pai_weixin_helper_class');

$ret = $weixin_helper_obj->wx_get_js_api_sign_package_by_app_id($app_id, $url);


//��ʱ��־
$payment_obj = POCO::singleton('pai_payment_class');
ecpay_log_class::add_log($_GET, __FILE__ , 'pai_weixin_js_api');

mobile_output($ret,false);

?>