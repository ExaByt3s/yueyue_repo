<?php

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

define('MALL_USER_DIR_APP',"../../../../");
include_once MALL_USER_DIR_APP.'common.inc.php';

function _get_wbc_tips($params = array())
{	
	
	$file_dir = dirname(__FILE__);

    global $my_app_pai;
    global $yue_login_id;

    // 设置UA常量
	$user_agent_arr = mall_output_format_data(mall_get_user_agent_arr());
    
    $tpl	 = $my_app_pai->getView($file_dir . "/global_tips.tpl.htm",true);
    $tpl->assign('text','下载约约，随时随地，体验服务');
    $tpl->assign('user_agent_arr',$user_agent_arr);
    $tpl->assign('yue_login_id',$yue_login_id);

    $tpl_html = $tpl->result();

    return $tpl_html;
}


?>