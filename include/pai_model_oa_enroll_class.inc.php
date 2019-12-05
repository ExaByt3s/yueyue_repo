<?php
/*
 * 模特报名操作类
 */

class pai_model_oa_enroll_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_enroll_tbl' );
	}
	
	/*
	 * 问卷模特报名
	 */
	public function add_model_enroll($user_id,$order_id)
	{
		$this->setTableName ( 'model_oa_enroll_tbl' );
		
		$user_id = (int)$user_id;
		$order_id = (int)$order_id;
		
		$pai_user_obj = POCO::singleton ( 'pai_user_class' );
		$oa_order_obj = POCO::singleton ( 'pai_model_oa_order_class' );
		$push_obj = POCO::singleton('pai_information_push');
		
		$role = $pai_user_obj->check_role($user_id);
		
		$insert_data['order_id'] = $order_id;
		$insert_data['user_id'] = $user_id;
		$insert_data['add_time'] = time();
		
		
		if($role!='model')
		{
			return false;
		}
		
		$ret = $this->insert ( $insert_data );
		
		if($ret)
		{
			$order_info = $oa_order_obj->get_order_info($order_id);
			$cameraman_user_id = $pai_user_obj->get_user_id_by_phone($order_info['cameraman_phone']);
			$style_name = $order_info['style'].'-'.$order_info['question_style'];
			$content = "你好，我已经报名了你发布的“{$style_name}“风格的活动，等待你呼唤哦。";
			
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = '2';
			$send_data['card_text1'] = $content;
			$send_data['card_text2'] = $content;
			$send_data['card_title'] = '查看详情';
			$send_data['link_url']   = 'http://yp.yueus.com/mobile/app?from_app=1#camera_demand/detail/'.$order_info['order_id'];
			$send_data['wifi_url']   = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/detail/'.$order_info['order_id'];
			
			$push_obj->send_msg_data($user_id, $cameraman_user_id, $send_data, $send_data, 0, 10);
		}
		
		return $ret;
	}
    
	
	/*
	 * 是否已添加
	 */
	public function check_repeat($user_id,$order_id)
	{
		$user_id = (int)$user_id;
		$order_id = (int)$order_id;
		
		$where_str = "user_id={$user_id} and order_id={$order_id}";
		$ret = $this->findCount ( $where_str );
		
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/*
	 * 订单报名人数
	 */
	public function count_enroll($order_id)
	{
		$where_str = "order_id={$order_id}";
		$ret = $this->findCount ( $where_str );
		return $ret;
	}

	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_model_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	

}

?>