<?php
defined('TEMPLATES_ROOT') || define('TEMPLATES_ROOT',"templates/");//ģ��λ��
include_once('../common.inc.php');
defined('YUE_ADMIN_V2_CLASS_ROOT') || define('YUE_ADMIN_V2_CLASS_ROOT',YUE_ADMIN_V2_PATH.'new_report/include/');//���屨�����ַ
if (empty($yue_login_id) || !isset($yue_login_id)) 
{
   	echo "<script type='text/javascript'>window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Freport%2Findex.php';</script>";
    exit;
}



/*$admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
$op_p = $admin_op_obj->check_op($yue_login_id,'report');
if(!is_array($op_p) || empty($op_p))
{
    echo "<script type='text/javascript'>window.alert('��û��Ȩ��,����ϵ����Ա��ȡȨ��!');window.top.location.href='http://www.yueus.com/yue_admin/login_e.php?referer_url=http%3A%2F%2Fyp.yueus.com%2Fyue_admin_v2%2Freport%2Findex.php';</script>";
    exit;
}*/


if (!function_exists('check_auth_v2'))//Ȩ�޿���
{
    /**
     * @param $user_id   ��¼��ID
     * @param string $op_code ��������
     * @param int $id  ��������
     */
    function check_auth_v2($user_id,$op_code = '',$id)
    {
        $id = intval($id);
        $result = false;
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $op_ret = $admin_op_obj->check_op($user_id,$op_code);
        if (!is_array($op_ret) || empty($op_ret)) pai_admin_op_class::pop_msg('����ʧ�ܣ�Ȩ�޲���',true);
        if($id >0)//��ҪУ��$id
        {
            $op_location = $op_ret[0]['op_location'];
            $location_arr = explode(',',$op_location);
            if(!is_array($location_arr)) $location_arr = array();
            foreach($location_arr as $location_id)
            {
                if(substr($id,0,6) == $location_id || $location_id == $id)
                {
                    $result = true;
                    break;
                }
            }
            if (empty($result)) pai_admin_op_class::pop_msg('����ʧ�ܣ�Ȩ�޲���',true);
        }
        return $op_ret;
    }
}

if(!function_exists('js_pop_msg_v2'))
{
    /**
     * �Ѻ���ʾ��Ϣ
     * @param $msg ��Ϣ
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
}

?>