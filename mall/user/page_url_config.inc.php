<?php
include('no_copy_online_config.inc.php');

// ��Ŀ·����ʹ�õ��Ǿ���·�� 
$base_url = G_MALL_PROJECT_USER_ROOT;


$app_to_webpage_config = array(
    '1220094' => $base_url.'/goods/category.php', // ������ҳ
	'1220130' => $base_url.'/topic/index.php', // ר������ҳ
	'1220109' => $base_url.'/seller/service_list.php?tag=seller', //�̼ҷ����б�
	'1220111' => $base_url.'/seller/detail.php',// �̼��������ҳ
	'1220103' => $base_url.'/seller/index.php',// �̼���ҳ 
	'1220147' => $base_url.'/seller/seller_list.php',// �̼��б� ��ͼ
	'1220102' => $base_url.'/goods/service_detail.php', // ��������


	'1220101' => $base_url.'/goods/service_list.php?tag=hp',//���ŷ����б�
	'1220122' => $base_url.'/goods/service_list.php?tag=dt', // ��ͼ�����б�
	'1220128' => $base_url.'/goods/service_list.php?tag=lp', // ���ŷ����б�
	
	'1220152' => $base_url.'/act/detail.php',// �����
	'1220130' => $base_url.'/topic/index.php',// ר������
	'0000001' => $base_url.'/act/list.php',// �����б� ���⴦��������
	'1220144' => $base_url.'/category/index.php',// Ʒ����ҳ
	'1220146' => $base_url.'/seller/seller_list.php?img_size=small',// �̼��б� Сͼ
	'1220145' => $base_url.'/channel/index.php',// ��Ƶ���б�
	'1220075' => $base_url.'/seller/comment_list.php',// �����б�ҳ
	'1220098' => $base_url.'/search/search.php?type_id=99',// ����ҳ,
	'1220124' => $base_url.'/search/search.php',// ����ҳ
	'1220125' => $base_url.'/search/index.php?search_type=seller',// �̼�����ҳ
	'1220126' => $base_url.'/search/index.php?search_type=goods'// ��������ҳ
);


if($user_agent_arr['is_pc'] == 1 )
{	// ����ɾ��
	$app_to_webpage_config['1220124'] = $base_url.'/search/index.php?search_type=goods';// ��������ҳ
}


return $app_to_webpage_config;
?>