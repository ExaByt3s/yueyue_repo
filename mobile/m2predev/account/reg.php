<?php 
/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$account = trim(intval($_INPUT['account']));
$password = trim($_INPUT['password']);

$head_html = include_once($file_dir. '/../webcontrol/head.php');
$tpl = $my_app_pai->getView('reg.tpl.html');

$tpl ->assign('head_html',$head_html);
$tpl->output();




?>