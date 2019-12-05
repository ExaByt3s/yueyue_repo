<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// 新版首页的变量
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}

//****************** pc版头部通用 start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');


// ================== pc版头部通用 end ==================
?> 