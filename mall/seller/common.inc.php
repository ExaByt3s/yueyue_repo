<?php
/**
 * 商城卖家通用文件
 * @copyright 2015-06-18
 */

include_once('no_copy_online_config.inc.php');
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
require_once('/disk/data/htdocs232/poco/pai/yue_admin/task/include/basics.fun.php');
include_once(dirname(__FILE__).'/include/output_function.php');



if(!defined('MALL_SELLER_DIR_APP') || !MALL_SELLER_DIR_APP)
{
    $root = "templates/default/";
}
else
{
    $root = MALL_SELLER_DIR_APP."templates/default/";
}

define('TASK_TEMPLATES_ROOT',$root);

// 默认设置是登录的
if( !defined('MALL_SELLER_IS_NOT_LOGIN') || !MALL_SELLER_IS_NOT_LOGIN )
{
    $seller_info = mall_check_seller_permissions($yue_login_id);
}
if(!$seller_info)
{
    $mall_obj = POCO::singleton('pai_mall_seller_class');
    $seller_info=$mall_obj->get_seller_info($yue_login_id,2);
}
