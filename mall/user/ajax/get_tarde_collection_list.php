<?php
/**
 * ��ȡ�̼��б�
 * 2015.10.28
 * @author hgc
 */
include_once 'config.php';

// ���ղ���
$page = intval($_INPUT['page']);

if(empty($page))
{
	$page = 1;
}
$type_id = intval($_INPUT['type_id']) ? intval($_INPUT['type_id']) : '' ;
$target_type = trim($_INPUT['target_type']);
$sort_by = trim($_INPUT['sort_by']);
// ��ҳʹ�õ�page_count
$page_count = 11;

if($page > 1)
{
	$limit_start = ($page - 1)*($page_count - 1);
}
else
{
	$limit_start = ($page - 1)*$page_count;
}

$limit = "{$limit_start},{$page_count}";

$ret = get_api_result('customer/favor_list.php',array
	(
	    'user_id'=> $yue_login_id,
        'limit' => $limit,
		'target_type'=> $target_type,
		'type_id' => $type_id,
		'sort_by' => $sort_by
    )
);


// ���ǰ���й������һ�����ݣ�������ʵ���
$rel_page_count = 10;

$has_next_page = (count($ret['data']['list'])>$rel_page_count);

if($has_next_page)
{
	array_pop($ret['data']['list']);
}

$output_arr['page'] = $page;

$output_arr['has_next_page'] = $has_next_page;

$output_arr['list'] = $ret['data']['list'];

$output_arr['share'] = $ret['data']['share'];

// �������
mall_mobile_output($output_arr,false);

?>