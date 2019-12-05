<?php
/**
 * 注意！！！该文件只做通用常量配置，但不进行复制上线
 * 商城卖家通用文件
 * @copyright 2015-06-26
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 

// 设置UA常量
$user_agent_arr = mall_get_user_agent_arr();

define('G_MALL_PROJECT_USER_ONLINE_VERSION',0);

if($user_agent_arr['is_pc'] == 1 )
{
    //****************** pc版 ******************
    define('G_MALL_PROJECT_USER_ROOT',"http://www.yueus.com/mall/user/test");
    define('G_MALL_PROJECT_USER_INDEX_DOMAIN',"http://www.yueus.com/newindex.php");
}
else
{
	define('G_MALL_PROJECT_USER_ROOT',"http://yp.yueus.com/mall/user/test");
	
}


