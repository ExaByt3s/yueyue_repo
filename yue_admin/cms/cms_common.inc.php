<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define('G_YUE_CMS_PATH',realpath(dirname(__FILE__))."/");
POCO::import(G_YUE_CMS_PATH."include");//��������·��


//ϵͳ��
include_once(G_YUE_CMS_PATH."include/cms_system_class.inc.php");


/**
 * Ȩ�޿���
 */
if (defined("G_YUE_CMS_CHECK_ADMIN")) 
{
	define("G_DB_GET_REALTIME_DATA");//ʵʱ
	include_once('/disk/data/htdocs232/poco/pai/yue_admin/yue_access_control.inc.php');
    yueyue_admin_check('cms', 'admin');
}



/**
 * ������Ϣ��ʾ�����˳�����
 * @param string $msg ������Ϣ
 */
if (!function_exists("js_pop_msg"))
{
	function js_pop_msg($msg,$b_reload=false,$url=NULL)
	{
		echo "<script language='javascript'>alert('{$msg}');";
		if($url) echo "window.parent.location = '{$url}';";
		if($b_reload) echo "window.parent.location.reload();";
		echo "</script>";
		exit;
	}
}



?>