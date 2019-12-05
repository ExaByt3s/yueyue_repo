<?php
define('YUE_ADMIN_V2_PATH','/disk/data/htdocs232/poco/pai/yue_admin_v2/');  //����v2�ĵ�ַ

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if (!function_exists('check_auth'))//Ȩ�޿���
{
    /**
     * @param $user_id   ��¼��ID
     * @param string $op_code ��������
     * @param string $op_url  ��������
     * @param int $parent_id  ����ID
     */
    function check_auth($user_id,$op_code = '',$op_url ='',$parent_id =0)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $op_ret = $admin_op_obj->check_op($user_id,$op_code,$op_url,$parent_id);
        if (!is_array($op_ret) || empty($op_ret)) pai_admin_op_class::pop_msg('����ʧ�ܣ�Ȩ�޲���',true);
    }
}


if (!function_exists('create_auth_nav'))//���ɲ˵�
{
    /**
     * @param int $user_id ��¼��ID
     * @param string $op_code ��������
     * @param string $param �����ұߵ�ʱ������left������������
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