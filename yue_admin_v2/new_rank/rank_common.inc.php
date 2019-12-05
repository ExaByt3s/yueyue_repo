<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/9/1
 * Time: 11:06
 */
define('CMS_RANK_TEMPLATES_ROOT',"templates/");//模板位置
include_once('../common.inc.php');
include_once('pai_cms_rank_class.inc.php');
$type_list = include_once ('cat_config.inc.php');//分类配置
$versions_list = include_once ('versions_config.inc.php');//版本配置

if (empty($yue_login_id) || !isset($yue_login_id))
{
    echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Fnew_rank%2Findex.php';</script>";
    exit;
}

$admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
$op_p = $admin_op_obj->check_op($yue_login_id,'new_rank');
if(!is_array($op_p) || empty($op_p))
{
    echo "<script type='text/javascript'>window.alert('你没有权限,请联系管理员获取权限!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Fnew_rank%2Findex.php';</script>";
    exit;
}

/**
 * 友好提示信息
 * @param $msg 信息
 * @param bool $b_reload
 * @param null $url
 */
function js_pop_msg_v2($msg,$b_reload = false,$url=NULL)
{
    echo "<script language='javascript'>alert('{$msg}');";
    if($url) echo "parent.location.href = '{$url}';";
    if($b_reload) echo "history.back();";
    echo "</script>";
    exit;
}