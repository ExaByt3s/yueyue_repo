<?php

class pai_yueshe_topic_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
	}
	
	private function set_enroll_tbl()
	{
		$this->setTableName ( 'pai_yueshe_topic_enroll_tbl' );
	}
	
	private function set_order_tbl()
	{
		$this->setTableName ( 'pai_yueshe_topic_order_tbl' );
	}
	
	/*
     * 插入一条报名记录
     * 
     * return bool 
     */
	public function add_enroll($insert_data)
	{
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		if (empty ( $insert_data ['add_time'] ))
		{
			$insert_data ['add_time'] = time ();
		}
		
		$this->set_enroll_tbl ();
		
		return $this->insert ( $insert_data );
	}
	
	/*
     * 插入一条订单记录
     * 
     * return bool 
     */
	public function add_order($insert_data)
	{
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		if (empty ( $insert_data ['add_time'] ))
		{
			$insert_data ['add_time'] = time ();
		}
		
		$this->set_order_tbl ();
		
		return $this->insert ( $insert_data );
	}
	
	/*
     * 更新订单表的信息
     * 
     * @param array $update_data
     * @param int   $id 
     * 
     */
	private function update_order($update_data, $id)
	{
		$id = ( int ) $id;
		
		if (empty ( $update_data ) || empty ( $id ))
		{
			return false;
		}
		
		$this->set_order_tbl ();
		
		$where_str = "id = {$id}";
		$ret = $this->update ( $update_data, $where_str );
		
		return $ret;
	}
	
	/*
     * 获取订单单条信息
     * @param int $id
     * return array
     */
	
	public function get_order_info($id)
	{
		$id = ( int ) $id;
		$this->set_order_tbl ();
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	/*
     * 获取我的订单信息
     */
	public function get_my_order_list($user_id)
	{
		$user_id = ( int ) $user_id;
		$this->set_order_tbl ();
		$ret = $this->findAll ( "user_id={$user_id}", "0,1000", "id desc" );
		return $ret;
	}

	/*
	 * 更新支付状态
	 */
	public function update_pay_status($payment_no,$id)
	{
		$id = ( int ) $id;
		if(empty($id))
		{
			$result['result'] = -1;
			$result['message'] = "ID不能为空";
			return $result;
		}
		
		$order_info = $this->get_order_info($id);
		
		if($order_info['order_status']!='wait')
		{
			$result['result'] = -1;
			$result['message'] = "订单不在待付款状态";
			return $result;
		}
		
		$update_data['payment_no'] = $payment_no;
		$update_data['pay_status'] = 1;
		$update_data['pay_time'] = time();
		$update_data['order_status'] = "confirm";
		$ret = $this->update_order($update_data, $id);
		
		if($ret)
		{
			$result['result'] = 1;
			$result['message'] = "成功";
			return $result;
		}
		else
		{
			$result['result'] = -2;
			$result['message'] = "失败";
			return $result;
		}
	}
}