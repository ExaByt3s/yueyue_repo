<?php

/** 
 * pc 
 * ͷ��
 * ��Բ
 * 2015-6-5
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



function _get_wbc_head($params)
{	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;
  

	if(preg_match('/yueus.com/',$_SERVER['HTTP_HOST']))
	{
		
		$tpl	 = $my_app_pai->getView($file_dir . "/head.tpl.htm",true);
		
	}
	else
	{
		$tpl = new SmartTemplate($file_dir . "/head.tpl.htm");
	}	

	$tpl->assign('is_pc',MALL_UA_IS_PC);

	$tpl->assign('project_root',G_MALL_PROJECT_USER_ROOT);

	$tpl_html = $tpl->result();

	return $tpl_html;
}


?>