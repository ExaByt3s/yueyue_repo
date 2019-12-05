<?php 
ignore_user_abort(true);
/**
 * 退出活动
 */

include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

if(empty($yue_login_id))
{
	die('no login');
}


$enroll_id = intval($_INPUT['enroll_id']);

/**
 * 
 * 删除报名
 * @param $enroll_id 报名表主键
 * 
 * */
$ret = del_enroll($enroll_id);

$output_arr['code'] = $ret;
$output_arr['msg'] = $ret ? '退出成功' : '退出失败';
$output_arr['msg'] = mb_convert_encoding($output_arr['msg'],'gbk','utf-8');

mobile_output($output_arr,false); 

?>