<?php
/**
 * �̼ҳ��������б�
 * @author rong
 * @copyright 2015-04-09
 */
define('YUE_INPUT_CHECK_TOKEN', FALSE);
require(dirname(dirname(__FILE__)) . '/protocol_input.inc.php');

//��ȡ����
$user_id = intval($client_data['data']['param']['user_id']);
$profile_id = intval($client_data['data']['param']['profile_id']);

//��ʼ������
$task_profile_obj = POCO::singleton('pai_task_profile_class');


$list = $task_profile_obj->get_profile_faq_list($profile_id);

$data = array();
$data['faq_text'] = "�����˽���";
$data['faq_list'] = $list;

$options['data'] = $data;
$cp->output($options);
