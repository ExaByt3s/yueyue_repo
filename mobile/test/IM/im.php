<?php

 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$area_config = include_once('/disk/data/htdocs232/poco/pai/m/config/area.conf.php');

if($yue_login_id == '100001' || $yue_login_id == '100029' || $yue_login_id == '100261' || $yue_login_id == '100049')
{
	$tpl = $my_app_pai->getView('im.tpl.htm');
}



$tpl->assign('time', time());  //�����

$tpl->output();

 ?>