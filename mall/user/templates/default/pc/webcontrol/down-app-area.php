<?php

/** 
 * pc 
 * ͷ��
 * ��Բ
 * 2015-6-5
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



function _get_wbc_down_app_area($params)
{	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		$tpl	 = $my_app_pai->getView($file_dir . "/down-app-area.tpl.htm",true);

		// �汾��
		$app_ver_str = file_get_contents('http://yp.yueus.com/download/version.txt');
		$tpl->assign('app_ver_str', '3.2.0');
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/down-app-area.tpl.htm");
		
		// �汾��
		$app_ver_str = file_get_contents('http://yp.yueus.com/download/version.txt');
		$tpl->assign('app_ver_str', '3.2.0');
	}	

	$tpl_html = $tpl->result();

	return $tpl_html;
}


?>