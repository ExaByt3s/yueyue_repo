<?php
/**
 * Created by PhpStorm.
 * User: hudingwen
 * Date: 15/6/3
 * Time: ����3:29
 */

/**
 * ������Դ�ļ���λ��ע�⣡ȷ������·����ȡ
 */
$file_dir = dirname(__FILE__);

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$head_html = include_once($file_dir. '/../../webcontrol/head.php');

$tpl = $my_app_pai->getView("jg_model.tpl.html");
$tpl ->assign('head_html',$head_html);
$tpl->output();