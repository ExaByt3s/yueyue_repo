<?php
/**
 * 卖家、商家
 * @author Henry
 * @copyright 2015-04-21
 */

class pai_task_seller_class extends POCO_TDG
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_task_seller_tbl()
	{
		$this->setTableName('task_seller_tbl');
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	public function add_seller($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_seller_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 获取信息
	 * @param int $user_id
	 * @return array
	 */
	public function get_seller_info($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return array();
		}
		$this->set_task_seller_tbl();
		return $this->find("user_id={$user_id}");
	}
	
	/**
	 * 获取手机号码
	 * @param int $user_id
	 * @return string
	 */
	public function get_seller_cellphone($user_id)
	{
		$cellphone = '';
		
		$seller_info = $this->get_seller_info($user_id);
		if( empty($seller_info) )
		{
			return $cellphone;
		}
		$cellphone = trim($seller_info['cellphone']);
		return $cellphone;
		//获取卖家资料
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		$profile_info = $task_profile_obj->get_profile_info($user_id, $seller_info['service_id']);
		if( empty($profile_info) )
		{
			return $cellphone;
		}
		$cellphone = trim($profile_info['cellphone']);
		
		return $cellphone;
	}
	
	/**
	 * 获取邮箱
	 * @param int $user_id
	 * @return string
	 */
	public function get_seller_email($user_id)
	{
		$email = '';
		
		$seller_info = $this->get_seller_info($user_id);
		if( empty($seller_info) )
		{
			return $email;
		}
		
		//获取卖家资料
		$task_profile_obj = POCO::singleton('pai_task_profile_class');
		$profile_info = $task_profile_obj->get_profile_info($user_id, $seller_info['service_id']);
		if( empty($profile_info) )
		{
			return $email;
		}
		$email = trim($profile_info['email']);
		
		return $email;
	}

	/**
	 * 获取列表
	 * @return array
	 */
	public function get_seller_all_list()
	{
		$this->set_task_seller_tbl();
		$sql_where = '';
		$order_by = 'user_id desc';
		$user_list = $this->findAll($sql_where, '',$order_by);
		foreach ($user_list as $key => $val)
		{
			$user_list[$key]['nickname'] = get_user_nickname_by_user_id($val['user_id']);
		}
		return $user_list;
	}	
}
