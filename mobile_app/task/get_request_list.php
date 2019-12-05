<?php
/**
 * �����б������б�
 * @author Henry
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//��ȡ����
$user_id = intval($client_data['data']['param']['user_id']);
$service_id = intval($client_data['data']['param']['service_id']);

//��ʼ������
$task_request_obj = POCO::singleton('pai_task_request_class');
$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
$task_service_obj = POCO::singleton('pai_task_service_class');

//������������
$hired_quotes_info = array();
$request_list_tmp = $task_request_obj->get_request_detail_list_by_user_id($user_id, $service_id);
$request_count = count($request_list_tmp);
$request_list = array();
foreach($request_list_tmp as $request_info_tmp)
{
	$status_code_tmp = trim($request_info_tmp['status_code']);
	$quotes_count_tmp = trim($request_info_tmp['quotes_count']);
	$quotes_list_tmp = $request_info_tmp['quotes_list'];
	if( !is_array($quotes_list_tmp) ) $quotes_list_tmp = array();
	$service_id_tmp = trim($request_info_tmp['service_id']);
	
	//��ȡ������Ϣ
	$service_info_tmp = $task_service_obj->get_service_info($service_id_tmp);
	
	//�����۵�����
	$quotes_list = array();
	foreach($quotes_list_tmp as $quotes_info_tmp)
	{
		$quotes_id_tmp = trim($quotes_info_tmp['quotes_id']);
		
		//״̬��ɫ
		$quotes_is_gray_tmp = $task_quotes_obj->get_quotes_is_gray($quotes_info_tmp['status'], $request_info_tmp['status_code']);
		
		$remind_num_tmp = $task_quotes_obj->get_quotes_remind_num($user_id, $quotes_id_tmp);
		$remind_num_tmp = trim($remind_num_tmp);
		
		if( $quotes_info_tmp['status']==1 )
		{
			$hired_quotes_info = $quotes_info_tmp;
		}
		
		$quotes_list[] = array(
			'quotes_id' => $quotes_info_tmp['quotes_id'],
			'user_id' => $quotes_info_tmp['user_id'],
			'user_icon' => $quotes_info_tmp['user_icon'],
			'is_vip' => $quotes_info_tmp['is_vip'],
			'status' => $quotes_info_tmp['status'],
			'is_gray' => $quotes_is_gray_tmp,
			'remind_num' => $remind_num_tmp,
		);
	}
	
	$tip_title_tmp = ''; //��ʾ����
	$tip_content_tmp = ''; //��ʾ����
	if( $status_code_tmp=='started' ) //����Ӷ�������ڣ��ޱ���
	{
		$tip_title_tmp = '�ȴ�������';
		$tip_content_tmp = '���ǻὫ��������͸����з����ʸ�Ĺ�Ӧ�ߣ������24Сʱ���յ��ظ�';
	}
	elseif( $status_code_tmp=='introduced' ) //����Ӷ�������ڣ��б���
	{
		$tip_title_tmp = '';
		$tip_content_tmp = "{$quotes_count_tmp}��{$service_info_tmp['profession_name']}��Ϊ���ṩ����";
	}
	elseif( $status_code_tmp=='closed' ) //����Ӷ���ѹ��ڣ��ޱ���
	{
		$tip_title_tmp = '��ʱû�з���Ҫ�����ѡ';
		$tip_content_tmp = '���ź�����ʱû����Ϊ�����Ĺ�Ӧ�߷�����������';
	}
	elseif( $status_code_tmp=='quoted' ) //����Ӷ���ѹ��ڣ��б���
	{
		$tip_title_tmp = '';
		$tip_content_tmp = "{$quotes_count_tmp}��{$service_info_tmp['profession_name']}��Ϊ���ṩ����";
	}
	elseif( $status_code_tmp=='canceled' )
	{
		$canceled_time_str = date('Y.m.d', $request_info_tmp['canceled_time']);
		$tip_title_tmp = '';
		$tip_content_tmp = "����{$canceled_time_str}ȡ���˸ö���";
	}
	elseif( in_array($status_code_tmp, array('hired', 'paid', 'reviewed')) )
	{
		$tip_title_tmp = '';
		$tip_content_tmp = "��ѡ�С�{$hired_quotes_info['user_nickname']}��";
	}
	
	$request_list[] = array(
		'request_id' => $request_info_tmp['request_id'],
		'service_id' => $request_info_tmp['service_id'],
		'title' => $request_info_tmp['title'],
		'add_time_str' => $request_info_tmp['add_time_str'],
		'status_color' => $request_info_tmp['status_color'],
		'status_code' => $request_info_tmp['status_code'],
		'status_name' => $request_info_tmp['status_name'],
		'tip_title' => $tip_title_tmp,
		'tip_content' => $tip_content_tmp,
		'request_icon' => 'http://img16.poco.cn/yueyue/20150416/2015041614590034524576.png?64x64_130',
		'quotes_list' => $quotes_list,
	);
}

$data = array();
$data['request_count'] = trim($request_count);
$data['request_list'] = $request_list;
$data['mid'] = '122OD04002'; //ģ��ID

$options['data'] = $data;
$cp->output($options);
