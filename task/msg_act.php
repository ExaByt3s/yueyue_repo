<?php
/** 
 * 
 * ����
 * 
 * 2015-4-11
 * 
 */
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);

$file_dir = dirname(__FILE__);

include_once($file_dir.'/./task_common.inc.php');

$quotes_id = (int)$_INPUT['quotes_id'];
$content = trim($_INPUT['content']);

if(empty($yue_login_id))
{
	js_pop_msg("���ȵ�¼");
}

if(empty($content))
{
	js_pop_msg("����������");
}

if(empty($quotes_id))
{
	js_pop_msg("��������");
}


/**
 * �ύ����
 * @param int $from_user_id
 * @param int $quotes_id
 * @param string $message_type �������ͣ�message���ԣ�quotes���ۣ�read_quotes�鿴���ۣ�read_profile�鿴���ϣ�hired��Ӷ��declinedл��
 * @param string $message_content
 * @param array $more_info
 * @return array
 */
$task_message_obj = POCO::singleton('pai_task_message_class');
$ret = $task_message_obj->submit_message($yue_login_id, $quotes_id, "message", $content, $more_info=array());

if($ret['result']==1)
{
	js_pop_msg("���ͳɹ�",false,"./order_detail.php?quotes_id=".$quotes_id);
}
else
{
	js_pop_msg($ret['message']);
}


 ?>