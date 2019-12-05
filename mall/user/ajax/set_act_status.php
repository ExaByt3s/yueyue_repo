<?php
/**
 * 
 *
 * @author hudingwen
 * @version $Id$
 * @copyright , 14 July, 2015
 * @package default
 */

/**
 * Define 处理活动状态
 */
include_once('../common.inc.php');	

$type = trim($_INPUT['type']);

switch($type)
{
	case 'del_enroll':
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
	break;
	
	
	case 'cancel' :
	$event_id = intval($_INPUT['event_id']);

	/**
	 * 
	 * 活动取消
	 * @param $event_id 
	 * 
	 * */
	$ret = set_event_status_by_cancel($event_id);

	$output_arr['code'] = $ret >0 ? 1 : 0;

	if($ret==-2){
		$output_arr['msg'] = "活动取消失败";
	}elseif($ret==-1){
		$output_arr['msg'] = "活动已有人签到，不能取消活动";
	}elseif($ret==1){
		$output_arr['msg'] = "活动取消成功";
	}
	break;
	
	case 'end':
	$event_id = intval($_INPUT['event_id']);
	/**
	 * 
	 * 活动结束
	 * @param $event_id 
	 * 
	 * */
	$ret = set_event_end_v2($event_id);

	$output_arr['code'] = $ret;

	if($ret==1){
		$output_arr['msg'] = '确认成功';
	}elseif($ret==0){
		$output_arr['msg'] = '确认不成功';
	}elseif($ret==-1){
		$output_arr['msg'] = '还没有人签到，请稍后再试';
	}elseif($ret==-2){
		$output_arr['msg'] = '状态异常';
	}
	break;
	
}

mall_mobile_output($output_arr,false);

?>