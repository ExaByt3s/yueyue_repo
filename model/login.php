<?php
/** 
 * 
 * ԼԼģ�ص�¼ҳ
 * 
 * author ����
 * 
 * 
 * 2015-1-23
 * 
 * 
 */
 
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
 
$tpl = $my_app_pai->getView('login.tpl.htm');
if(!empty($yue_login_id))
{
    header("location:./edit_model_card.php");
} 

// ������ʽ��js����
$pc_global_top = $my_app_pai->webControl('pc_global_top', array(), true);
$tpl->assign('pc_global_top', $pc_global_top);

// ͷ������
$header_html = $my_app_pai->webControl('pc_model_card_header', array(), true);
$tpl->assign('header_html', $header_html);

$tpl->output();
 ?>