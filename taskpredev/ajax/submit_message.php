<?php
define('G_SIMPLE_INPUT_CLEAN_VALUE',1);
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once("/disk/data/htdocs232/poco/pai/mobile/include/output_function.php");
global $yue_login_id;

$quotes_id = $_INPUT['quotes_id'];
$content = $_INPUT['content'];
$content = iconv('utf-8','gbk',$_INPUT['content']);
/**
 * 提交留言
 * @param int $from_user_id
 * @param int $quotes_id
 * @param string $message_type 留言类型，message留言，quotes报价，read_quotes查看报价，read_profile查看资料，hired雇佣，declined谢绝
 * @param string $message_content
 * @param array $more_info
 * @return array
 */
$message_type = 'message';
 $task_message_obj = POCO::singleton('pai_task_message_class');
$ret = $task_message_obj->submit_message($yue_login_id, $quotes_id, $message_type, $content, $more_info=array());

mobile_output($ret,false);

?>