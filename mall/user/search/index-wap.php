<?php

//****************** wap�� ͷ��ͨ�� start  ******************
$pc_wap = 'wap/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'search/index.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');

$wap_global_top = _get_wbc_head();
$wap_global_footer = _get_wbc_footer();

$tpl->assign('wap_global_top', $wap_global_top);
$tpl->assign('wap_global_footer', $wap_global_footer);
//****************** wap�� ͷ��ͨ�� end  ******************

// �ռ�����
$type_id = intval($_INPUT["type_id"]);


// �����ʽ����
// ��ʼ������ť
if($search_type == 'seller')
{
	$sort_btn = array(
		0 => array(
			'text' => 'Ĭ������',
			'orderby' => '-1',
			'selected' => true
						 
		),
		1 => array(
			'text' => '�����ߵ���',
			'orderby' => '1'			 
		),
		2 => array(
			'text' => '���ָߵ���',
			'orderby' => '3'
		)
	);
}
else
{
	$sort_btn = array(
		0 => array(
			'text' => 'Ĭ������',
			'orderby' => '-1',
			'selected' => true
					 
		),
		1 => array(
			'text' => '�����ߵ���',
			'orderby' => '1'	
		),
		2 => array(
			'text' => '�۸�ߵ���',	
			'orderby' => '3'
		),
		3 => array(
			'text' => '�۸�͵���',
			'orderby' => '4'
		),
		4 => array(
			'text' => '�����ߵ���',
			'orderby' => '5'
		),
		5 => array(
			'text' => '���ָߵ���',
			'orderby' => '7'
		)
	);
}

// ������������
if(!empty($orderby))
{
	foreach ($sort_btn as $key => $value) 
	{
		$sort_btn[$key]['selected'] = $value['orderby'] == $orderby ? true : false;
	}
}

// ���ҳ������
$output_arr['filter_data'] = $filter_data;
$output_arr['sort_data'] = $sort_btn;
$output_arr = mall_output_format_data($output_arr);

$screen_query = $_GET;
unset($screen_query['type_id']);
unset($screen_query['search_type']);
unset($screen_query['keywords']);
unset($screen_query['page']);

$_GET['screen_query'] = http_build_query($screen_query);

$page_params = mall_output_format_data($_GET);
//$keywords =  urldecode(mb_convert_encoding($keywords, 'GBK','UTF-8'));

$tpl->assign('page_params',$page_params);
$tpl->assign('page_data',$output_arr);
$tpl->assign('search_type',$search_type);
$tpl->assign('type_id',$type_id);
$tpl->assign('keywords',$keywords);

?>