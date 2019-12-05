<?php
/** 
 * 
 * 留言
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
	js_pop_msg("请先登录");
}

if(empty($content))
{
	js_pop_msg("请输入留言");
}

if(empty($quotes_id))
{
	js_pop_msg("参数错误");
}


/**
 * 提交留言
 * @param int $from_user_id
 * @param int $quotes_id
 * @param string $message_type 留言类型，message留言，quotes报价，read_quotes查看报价，read_profile查看资料，hired雇佣，declined谢绝
 * @param string $message_content
 * @param array $more_info
 * @return array
 */
$task_message_obj = POCO::singleton('pai_task_message_class');
$ret = $task_message_obj->submit_message($yue_login_id, $quotes_id, "message", $content, $more_info=array());

if($ret['result']==1)
{
	js_pop_msg("发送成功",false,"./order_detail.php?quotes_id=".$quotes_id);
}
else
{
	js_pop_msg($ret['message']);
}


 ?>