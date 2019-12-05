<?php
/*
 * OA订单操作类
 */

class pai_model_oa_order_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_order_tbl' );
	}
	
	
	/*
	 * 添加LOG
	 * @param string  $type LOG标识
	 * @param int  $order_id 订单号
	 * @param array  $log_array 日志信息
	 * 
	 */
	public function add_log($type,$order_id,$log_array)
	{
		global $yue_login_id;
		
		$log_data ['order_id'] = $order_id;
		$log_data ['user_id'] = $yue_login_id;
		$log_data ['type'] = $type;
		$log_data ['log'] = print_r($log_array,true);
		$log_data ['add_time'] = time ();
		
		$log_str = db_arr_to_update_str($log_data);
		$sql = "INSERT IGNORE pai_log_db.model_oa_log_tbl SET ".$log_str;
		$this->findBySql($sql);
		
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_order($insert_data)
	{
		
		if (empty ( $insert_data ['cameraman_nickname'] ))
		{
			trace ( "摄影师昵称不能为空", basename ( __FILE__ ) . " 行:" . __LINE__ . " 方法:" . __METHOD__ );
			return false;
		}
		
		if (empty ( $insert_data ['cameraman_phone'] ))
		{
			trace ( "联系电话不能为空", basename ( __FILE__ ) . " 行:" . __LINE__ . " 方法:" . __METHOD__ );
			return false;
		}
		
		
		if (empty ( $insert_data ['source'] ))
		{
			trace ( "订单来源不能为空", basename ( __FILE__ ) . " 行:" . __LINE__ . " 方法:" . __METHOD__ );
			return false;
		}
		
		$insert_data ['add_time'] = time ();
		
		$num = str_replace(array("个","以","上"),"",$insert_data ['model_num']);
		$num = (int)$num;
		
		$insert_data ['receivable_amount'] = $insert_data ['hour']*$insert_data ['budget']*$num;
		$insert_data ['payable_amount'] = $insert_data ['hour']*$insert_data ['budget']*$num;
		
		$order_id = $this->insert ( $insert_data );
		
		$this->add_log("add_order",$order_id,$insert_data);
		
		return $order_id;
	
	}
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_order($data, $order_id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$order_id = ( int ) $order_id;
		if (empty ( $order_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		
		$where_str = "order_id = {$order_id}";
		
		if($data['cameraman_phone'])
		{
			//编辑订单LOG
			$this->add_log("edit",$order_id,$data);
		}
		return $this->update ( $data, $where_str );
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
	public function get_order_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_order_info($order_id)
	{
		$order_id = ( int ) $order_id;
		$ret = $this->find ( "order_id={$order_id}" );
		return $ret;
	}
	
	public function get_order_status($order_id)
	{
		$order_id = ( int ) $order_id;
		$ret = $this->find ( "order_id={$order_id}" );
		return $ret['order_status'];
	}
	
	public function get_wait_order_info_by_phone($phone)
	{
		$phone = ( int ) $phone;
		$ret = $this->find ( "order_status='wait' and cameraman_phone={$phone}" );
		
		//地区引用
		include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
		
		$ret ['city_name'] = get_poco_location_name_by_location_id ( $ret ['location_id'] );
		
		
		return $ret;
	}
	
	/*
	 * 检查在等待审核状态是否重复提交
	 */
	public function check_duplicate_submit($cellphone)
	{
		$cellphone = ( int ) $cellphone;
		
		if (empty ( $cellphone ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':手机不能为空' );
		}
		
		$where_str = "audit_status='wait' and cameraman_phone={$cellphone}";
		$ret = $this->findCount ( $where_str );
		if ($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * 确认下单
	 */
	public function order_confirm_order($order_id)
	{
		$order_id = ( int ) $order_id;
		
		global $yue_login_id;
		
		if(!$yue_login_id)
		{
			return false;
		}
		
		$order_status = $this->get_order_status($order_id);
		//等待下单状态才能确认下单
		if(!in_array($order_status,array("wait","complete_recommend")))
		{
			return -1;
		}
		
		$data ['order_status'] = 'confirm_order';
		$data ['audit_time'] = time();
		$ret = $this->update_order ( $data, $order_id );
		if ($ret == 1)
		{
			$this->audit_pass ( $order_id );
		}
		
		//加LOG
		$this->add_log("confirm_order",$order_id);
		
		return $ret;
	}
	
	/*
	 * 完成模特推荐
	 */
	public function order_complete_recommend($order_id)
	{
		$order_id = ( int ) $order_id;
		
		global $yue_login_id;
		
		if(!$yue_login_id)
		{
			return false;
		}
		
		$information_obj = POCO::singleton ('pai_information_push');
		
		$order_info = $this->get_order_info($order_id);
		//已下单状态才可以执行推荐模特操作
		if($order_info['order_status']!='confirm_order')
		{
			return -1;
			
		}
		
		$data ['order_status'] = 'complete_recommend';
		$ret = $this->update_order ( $data, $order_id );
		
/*		if($ret && $order_info['source']==4)
		{
			$pai_user_obj = POCO::singleton ( 'pai_user_class' );
			$user_id = $pai_user_obj->get_user_id_by_phone($order_info['cameraman_phone']);
			
			$send_data['media_type'] = 'card';
			$send_data['card_style'] = '2';
			$send_data['card_text1'] = '根据你的需求发布，约约为你精心筛选了几个模特';
			$send_data['card_title'] = '查看模特详情';
			$send_data['link_url']     = 'http://yp.yueus.com/mobile/app?from_app=1#camera_demand/model_push/' .$order_id ;
        	$send_data['wifi_url']     = 'http://yp-wifi.yueus.com/mobile/app?from_app=1#camera_demand/model_push/' . $order_id;
			
			$information_obj->send_msg_for_system(10006,$user_id,$send_data);
		}*/
		
		//加LOG
		$this->add_log("complete_recommend",$order_id);
		
		
		return $ret;
	}
	
	/*
	 * 拍摄确认
	 */
	public function order_shoot_confirm($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//已完成推荐才能操作拍摄确认
		if($order_status!='complete_recommend')
		{
			return -1;
		}
		
		$data ['order_status'] = 'shoot_confirm';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("shoot_confirm",$order_id);
		
		return $ret;
	}
	
	/*
	 * 付款确认
	 */
	public function order_pay_confirm($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//拍摄确认才能收款
		if($order_status!='shoot_confirm')
		{
			return -1;
		}
		
		$data ['order_status'] = 'pay_confirm';
		$data ['payment_status'] = 'done';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("pay_confirm",$order_id);
		
		return $ret;
	}
	
	/*
	 * 结单
	 */
	public function order_close($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['order_status'] = 'close';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("close",$order_id);
		
		return $ret;
	}
	
	/*
	 * 退款
	 */
	public function order_refund($order_id)
	{
		$order_id = ( int ) $order_id;
		
		//结单后不可以再更新订单状态
		if($order_status=='close')
		{
			return -1;
		}
		
		$data ['order_status'] = 'refund';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("refund",$order_id);
		
		return $ret;
	}
	
	/*
	 * 订单取消
	 */
	public function order_cancel($order_id,$cancel_reason='')
	{
		$order_id = ( int ) $order_id;
		
		$order_info = $this->get_order_info($order_id);
		//结单后不可以再取消了
		if($order_info['order_status']=='close')
		{
			return -1;
		}
		
		$data ['order_status'] = 'cancel';
		$data ['cancel_reason'] = $cancel_reason;
		$ret = $this->update_order ( $data, $order_id );
		
/*		if($order_info['source']=='4')
		{
			$pai_user_obj = POCO::singleton ( 'pai_user_class' );
			$user_id = $pai_user_obj->get_user_id_by_phone($order_info['cameraman_phone']);
			$content = "很抱歉，由于你填写的信息存在问题，您的需求发布未能通过约约系统的审核，原因为“{$cancel_reason}”，若有疑问，请回复“发需求”和您的手机号码到需求助手，工作人员会与您取得联系";
			send_message_for_10006($user_id, $content);
		}*/
		
		//加LOG
		$this->add_log("cancel",$order_id);
		
		return $ret;
	}
	
	/*
	 * 等待拍摄
	 */
	public function order_wait_shoot($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//已确认收款才能执行等待拍摄
		if($order_status!='pay_confirm')
		{
			return -1;
		}
		
		
		$data ['order_status'] = 'wait_shoot';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("wait_shoot",$order_id);
		
		return $ret;
	}
	
	/*
	 * 等待结单
	 */
	public function order_wait_close($order_id)
	{
		$order_id = ( int ) $order_id;
		
		$order_status = $this->get_order_status($order_id);
		//状态为等待拍摄下一步才能等待结单
		if($order_status!='wait_shoot')
		{
			return -1;
		}
		
		$data ['order_status'] = 'wait_close';
		$ret = $this->update_order ( $data, $order_id );
		
		//加LOG
		$this->add_log("wait_close",$order_id);
		
		return $ret;
	}
	
	/*
	 * 审核通过
	 */
	public function audit_pass($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['audit_status'] = 'pass';
		$ret = $this->update_order ( $data, $order_id );	
		return $ret;
	}
	
	/*
	 * 审核不通过
	 */
	public function audit_not_pass($order_id)
	{
		$order_id = ( int ) $order_id;
		$data ['audit_status'] = 'not_pass';
		$ret = $this->update_order ( $data, $order_id );
		return $ret;
	}
	
	/* 
	 * 获取问卷数据
	 */
	public function get_requirement_list($b_select_count=false,$where='',$limit='')
	{
		$now = date("Y-m-d H:i:s");
		$where_str = "order_status in ('confirm_order','complete_recommend','close') and source=4 and date_time>'{$now}' ";
		
		if($where)
		{
			$where_str .= $where;
		}
		
		
		$order_by = "order_id desc";
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, '*' );
		}
		
		return $ret;
	}

}

?>