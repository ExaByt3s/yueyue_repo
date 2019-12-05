<?php
include_once 'config.php';
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$login_id = intval($_INPUT['login_id']);
$pw = trim($_INPUT['pw']);

$obj = POCO::singleton('pai_mall_operate_agent_class');
    /*
     * 代运营登录验证
     * @param int $user_id
     * @param string $pwd
     * @return array
     */
$ret=$obj->user_login($login_id,$pw);

$output_arr = $ret;

mobile_output($output_arr,false);

?>