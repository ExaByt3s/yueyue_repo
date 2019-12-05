<?php
defined('TEMPLATES_ROOT') || define('TEMPLATES_ROOT',"templates/");//模板位置
define('YUE_PA_CLASS_ROOT','/disk/data/htdocs232/poco/pai/pa/links_push/include/');
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	echo "<script type='text/javascript'>window.top.location.href='http://pa.yueus.com/links_push/login_e.php';</script>";
    exit;
}


$op_p = check_pa_op($yue_login_id);
if(empty($op_p))
{
    echo "<script type='text/javascript'>window.alert('你没有权限,请联系管理员获取权限!');window.top.location.href='http://pa.yueus.com/links_push/login_e.php';</script>";
    exit;
}

if(!function_exists('js_pop_msg_v2'))
{
    /**
     * 友好提示信息
     * @param $msg 信息
     * @param bool $b_reload
     * @param null $url
     * @param bool $parent  true|false
     */
    function js_pop_msg_v2($msg,$b_reload = false,$url=NULL,$parent= false)
    {
        echo "<script language='javascript'>alert('{$msg}');";
        if($url && $parent) echo "parent.location.href = '{$url}';";
        if($url && !$parent) echo "location.href = '{$url}';";
        if($b_reload) echo "history.back();";
        echo "</script>";
        exit;
    }
}

function check_pa_op($yue_login_id)
{
    $yue_login_id = (int)$yue_login_id;
    if($yue_login_id <1) return false;
    $user_array = array(100293,102822);
    if(in_array($yue_login_id,$user_array)) return true;
    return false;
}

?>