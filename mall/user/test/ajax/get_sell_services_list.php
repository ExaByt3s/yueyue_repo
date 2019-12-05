<?php
/**
 * ��ȡ�����б�
 * 2015.10.12
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

// ���ղ���
$page = intval($_INPUT['page']);
$seller_user_id = intval($_INPUT['seller_user_id']);
$img_size = trim($_INPUT['img_size']) ? trim($_INPUT['img_size']) : 'big';


if(empty($page))
{
	$page = 1;
}

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

$ret = get_api_result('customer/sell_services_list',array
	(
    'seller_user_id' => $seller_user_id,    
    'limit' => $limit,     
    'service_status' => $service_status,
    'goods_type'=>$goods_type
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

$output_arr['title'] = "�����б�";

// �������
mall_mobile_output($output_arr,false);

?>