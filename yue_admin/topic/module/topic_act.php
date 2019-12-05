<?php 
include_once '../common.inc.php';

$act = $_INPUT['act'];
$title = $_INPUT['title'];
$begin_time = $_INPUT['begin_time'];
$end_time = $_INPUT['end_time'];
$alter_topic_id = (int)$_INPUT['alter_topic_id'];

$alert_price_obj = POCO::singleton('pai_alter_price_class');
$alert_price_user_obj = POCO::singleton('pai_alter_price_user_class');

switch ($act) {
	case 'add':
		if(!$title)
		{
			js_pop_msg("标题不能为空");
			exit;
		}
		
		if(!$begin_time)
		{
			js_pop_msg("开始时间不能为空");
			exit;
		}
		
		if(!$end_time)
		{
			js_pop_msg("结束时间不能为空");
			exit;
		}
	
		$insert_data['title'] = $title;
		$insert_data['begin_time'] = strtotime($begin_time);
		$insert_data['end_time'] = strtotime($end_time);
		$ret = $alert_price_obj->add_topic($insert_data);
		
		if($ret)
		{
			$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$ret}";
			js_pop_msg("新建成功",false,$url);
			
			exit;
		}
	break;
	
	
	case 'edit':
		$user_ret = $alert_price_user_obj->get_user_list ( false, "alter_topic_id={$alter_topic_id}" );
		
		if($user_ret)
		{
			foreach($user_ret as $k=>$val)
			{
				$user_arr[] = $val['user_id'];
			}
			
			$check_repeat = $alert_price_obj->check_repeat_user($alter_topic_id,$user_arr,strtotime($begin_time),strtotime($end_time));
			
			if($check_repeat)
			{
				js_pop_msg("当前专题的用户ID和其他专题在生效时间里有重复关联");
			}
		}
	
		$update_data['title'] = $title;
		$update_data['begin_time'] = strtotime($begin_time);
		$update_data['end_time'] = strtotime($end_time);
		$ret = $alert_price_obj->update_topic($update_data, $alter_topic_id);
		
		
		$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php?alter_topic_id={$alter_topic_id}";
		js_pop_msg("修改成功",false,$url);
		
		exit;
		
	break;
	
	case 'del':
		if(!$alter_topic_id)
		{
			js_pop_msg("请选择专题");
			exit;
		}
		$ret = $alert_price_obj->update_topic(array("del_status"=>1), $alter_topic_id);
		
		$url = "http://www.yueus.com/yue_admin/topic/alter_price_list.php";
		js_pop_msg("删除成功",false,$url);
		
		exit;
	break;
}

?>