<?php
$task_templates_root = TASK_TEMPLATES_ROOT;

// �°���ҳ�ı���
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}
//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// ͷ��css���
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');
// ����tips ����
include_once($task_templates_root.$pc_wap. '/webcontrol/global_tips.php');


$pc_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();
$wap_global_tips =  _get_wbc_tips();

$tpl->assign('wap_global_top', $pc_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
$tpl->assign('wap_global_tips', $wap_global_tips);

//****************** wap�� ͷ��ͨ�� end  ******************

// hudw ����������������
// 2015.11.10
$search_url = G_MALL_PROJECT_USER_ROOT . '/search/search.php?type=goods';
$search_icon_arr = array(
	0 => array(
		'class' => 'y-mt',
		'url'   => $search_url.'&type_id=31',
		'con'   => 'Լģ��'
	),
	1 => array(
		'class' => 'y-px',
		'url'   => $search_url.'&type_id=5',
		'con'   => 'Լ��ѵ'
	),
	2 => array(
		'class' => 'y-hz',
		'url'   => $search_url.'&type_id=3',
		'con'   => 'Լ��ױ'
	),
	3 => array(
		'class' => 'y-dz',
		'url'   => $search_url.'&type_id=12',
		'con'   => '��ҵ����'
	),
	4 => array(
		'class' => 'y-hd',
		'url'   => $search_url.'&type_id=42',
		'con'   => 'Լ�'
	),
	5 => array(
		'class' => 'y-sy',
		'url'   => $search_url.'&type_id=40',
		'con'   => 'Լ��Ӱ'
	),	
	6 => array(
		'class' => 'y-ms',
		'url'   => $search_url.'&type_id=41',
		'con'   => 'Լ��ʳ'
	),	
	7 => array(
		'class' => 'y-yx',
		'url'   => $search_url.'&type_id=43',
		'con'   => 'Լ��Ȥ'
	)
);
$tpl->assign('search_icon_arr', $search_icon_arr);
$tpl->assign('ret_index_v2', $ret_index_v2['data']);


// �ײ�����
// ��ҳ 
$tpl->assign('index_link', G_MALL_PROJECT_USER_ROOT);
// �ҵ�
$tpl->assign('my_link', G_MALL_PROJECT_USER_ROOT. '/home/');

$tpl->assign('location_id',$_COOKIE['yue_location_id']);


?>