<?php
/**
 * ע�⣡�������ļ�ֻ��ͨ�ó������ã��������и�������
 * �̳�����ͨ���ļ�
 * @copyright 2015-06-26
 */
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php'); 

// ����UA����
$user_agent_arr = mall_get_user_agent_arr();

define('G_MALL_PROJECT_USER_ONLINE_VERSION',1);

if($user_agent_arr['is_pc'] == 1 )
{
    //****************** pc�� ******************
    define('G_MALL_PROJECT_USER_ROOT',"http://www.yueus.com/mall/user");
    define('G_MALL_PROJECT_USER_INDEX_DOMAIN',"/");
}
else
{
	define('G_MALL_PROJECT_USER_ROOT',"http://yp.yueus.com/mall/user");
}

