<?php

$pc_wap = 'wap/';


$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'home/collection_list.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��������ʽ��js����
$wap_global_top = _get_wbc_head();
$tpl->assign('wap_global_top', $wap_global_top);

// �ײ������ļ�����
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
$wap_global_footer = _get_wbc_footer();
$tpl->assign('wap_global_footer', $wap_global_footer);

$type_id = intval($_INPUT['type_id']);
$target_type = trim($_INPUT['target_type']);
$sort_by = trim($_INPUT['sort_by']);
$target_type = trim($_INPUT['target_type']);
// ���ҳ�����
$page_params = mall_output_format_data(array
	(
        'user_id'=> $yue_login_id,
		'target_type'=> $target_type,
		'type_id' => $type_id,
		'sort_by' => $sort_by
	));

// ��ȡ�������� ģ�ء���ѵ����Ӱ.....
$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data_arr =array(
    0 => array(
    'id' => 0,
    'name' => "����Ʒ��"
    ),
    2 => array(
    'id' => 31,
    'name' => "Լģ��"
    ),
    3 => array(
    'id' => 40,
    'name' => "Լ��Ӱ"
    ),
    4 => array(
    'id' => 41,
    'name' => "Լ��ʳ"
    ),
    5 => array(
    'id' => 5,
    'name' => "Լ��ѵ"
    ),
    6 => array(
    'id' => 3,
    'name' => "Լ��ױ"
    ),
    7 => array(
    'id' => 43,
    'name' => "Լ��Ȥ"
    ),
    8 => array(
    'id' => 12,
    'name' => "��ҵ����"
    )
);



$last_updata = "goods_last_update_time";
if($target_type=="seller")
{
    $last_updata = "user_last_update_time";
}

$sort_data = array(
    0 => array(
        'name' => 'Ĭ������',
        'sort_by' =>'add_time'
    ),
    1 => array(
        'name' => '���չ�עʱ��',
        'sort_by' => 'add_time'
    ),
    2 => array(
        'name' => '�����������',
        'sort_by' => $last_updata
        )
);

$filter_data = array(
    'type_data' => $type_data,
    'sort_data' => $sort_data
);
$page_filter_page_params = mall_output_format_data($filter_data);
//$sort_data = $page_filter_page_params['sort_data'];
//$type_data = $page_filter_page_params['type_data'];


$tpl->assign('seller_url','./collection_list.php?target_type=seller');
$tpl->assign('goods_url', './collection_list.php?target_type=goods');
$tpl->assign('page_params',$page_params);
$tpl->assign('target_type',$target_type);
$tpl->assign('type_id',$type_id);
$tpl->assign('sort_data',$sort_data);
$tpl->assign('type_data',$type_data_arr);
$tpl->assign('sort_by',$sort_by);
$tpl->assign('page_filter_page_params',$page_filter_page_params);


?>