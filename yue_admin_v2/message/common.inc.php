<?php
define('TEMPLATES_ROOT',"templates/");//ģ��λ��
include_once('../common.inc.php');
define('YUE_ADMIN_V2_CLASS_ROOT',YUE_ADMIN_V2_PATH.'message/include/');//���屨�����ַ
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3a%2f%2fyp.yueus.com%2fyue_admin_v2%2fmessage%2findex.php'</script>";
    exit;
}
$admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
$op_p = $admin_op_obj->check_op($yue_login_id,'message');
//�Ƿ�Ϊ��������Ա
if(!is_array($op_p) || empty($op_p))
{
    echo "<script type='text/javascript'>window.alert('��û��Ȩ��,����ϵ����Ա��ȡȨ��!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3a%2f%2fyp.yueus.com%2fyue_admin_v2%2fmessage%2findex.php'</script>";
    exit;
}


/**
 * �Ѻ���ʾ��Ϣ
 * @param $msg ��Ϣ
 * @param bool $b_reload
 * @param null $url
 */
if (!function_exists('js_pop_msg_v2'))
{
    function js_pop_msg_v2($msg,$b_reload = false,$url=NULL,$parent = true)
    {
        echo "<script language='javascript'>alert('{$msg}');";
        if($url && $parent) echo "parent.location.href = '{$url}';";
        if($url && !$parent) echo "location.href = '{$url}';";
        if($b_reload) echo "history.back();";
        echo "</script>";
        exit;
    }
}





?>