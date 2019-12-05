<?php
define('YUE_ADMIN_V2_PATH','/disk/data/htdocs232/poco/pai/yue_admin_v2/');  //定义v2的地址

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if (!function_exists('check_auth'))//权限控制
{
    /**
     * @param $user_id   登录者ID
     * @param string $op_code 操作代码
     * @param string $op_url  操作链接
     * @param int $parent_id  父类ID
     */
    function check_auth($user_id,$op_code = '',$op_url ='',$parent_id =0)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $op_ret = $admin_op_obj->check_op($user_id,$op_code,$op_url,$parent_id);
        if (!is_array($op_ret) || empty($op_ret)) pai_admin_op_class::pop_msg('操作失败，权限不足',true);
    }
}


if (!function_exists('create_auth_nav'))//生成菜单
{
    /**
     * @param int $user_id 登录者ID
     * @param string $op_code 操作代码
     * @param string $param 生成右边的时候填入left，其他不用填
     * @return string mixed
     */
    function create_auth_nav($user_id,$op_code,$param ='')
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $str = $admin_op_obj->create_nav_list($user_id,$op_code,$param);
        return $str;
    }
}



?>