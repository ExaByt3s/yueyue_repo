<?php
/**
 * 商城卖家通用文件
 * @copyright 2015-06-18
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
include_once(dirname(__FILE__).'/include/output_function.php');
include_once(dirname(__FILE__).'/no_copy_online_config.inc.php');
define('TASK_TEMPLATES_ROOT',IM_USER_DIR_APP."templates/default/");

// 设置UA常量
$user_agent_arr = mall_get_user_agent_arr();
if($user_agent_arr['is_pc'] == 1 )
{
	define('IM_UA_IS_PC',1);
}
else
{
	define('IM_UA_IS_MOBILE',1);
	
	if($user_agent_arr['is_weixin'] == 1 )
	{
		define('IM_UA_IS_WEIXIN',1);
	}
	else if($user_agent_arr['is_yueyue_app'] == 1 )
	{
		define('IM_UA_IS_YUEYUE',1);
	} 
}

