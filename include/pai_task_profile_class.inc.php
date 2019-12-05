<?php
/*
 * 商家资料操作类
 */

class pai_task_profile_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_task_db' );
		
	}
	
	
	private function set_task_profile_tbl() {
		$this->setTableName ( 'task_profile_tbl' );
	}	

	
	private function set_task_profile_faq_tbl() {
		$this->setTableName ( 'task_profile_faq_tbl' );
	}
	
	private function set_task_seller_tbl() {
		$this->setTableName ( 'task_seller_tbl' );
	}	

	/*
	 * 获取商家资料
	 * @param int $user_id
	 * @param int $service_id
	 * 
	 * return array
	 */
	public function get_profile_info($user_id,$service_id)
	{
		$this->set_task_profile_tbl();
		
		$user_id = ( int ) $user_id;
		$service_id = ( int ) $service_id;
		
		$where_str = "user_id={$user_id} and service_id={$service_id}";
		
		$ret = $this->find ( $where_str);
		
		$ret ['city_name'] = get_poco_location_name_by_location_id ( $ret ['location_id'] );
		
		return $ret;
	}
	
	/*
	 * 获取商家资料
	* @param int $profile_id
	* @return array
	*/
	public function get_profile_info_by_id($profile_id)
	{
		$profile_id = intval($profile_id);
		if( $profile_id<1 )
		{
			return array();
		}
		$where_str = "profile_id={$profile_id}";
		$this->set_task_profile_tbl();
		$ret = $this->find($where_str);
		$ret ['city_name'] = get_poco_location_name_by_location_id ( $ret ['location_id'] );
		return $ret;
	}
	
	/*
	 * 获取商家资料
	* @param int $user_id
	* @return array
	*/
	public function get_profile_info_by_user_id($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return array();
		}
		$task_seller_obj = POCO::singleton('pai_task_seller_class');
		$seller_info = $task_seller_obj->get_seller_info($user_id);
		if( empty($seller_info) )
		{
			return array();
		}
		$service_id = intval($seller_info['service_id']);
		return $this->get_profile_info($user_id, $service_id);
	}
	
	/**
	 * 获取列表
	 * @param int $user_id
	 * @param int $service_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_profile_list($user_id, $service_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$user_id = intval($user_id);
		$service_id = intval($service_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}
		
		if( $service_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "service_id={$service_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_task_profile_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/*
	 * 获取商家FAQ
	 * @param int $profile_id 商家ID
	 * @param string $limit 
	 * return array
	 */
	public function get_profile_faq_list($profile_id,$limit='0,1000')
	{
		$this->set_task_profile_faq_tbl();
		
		$profile_id = ( int ) $profile_id;
		
		$where_str = "profile_id={$profile_id}";
		
		$ret = $this->findAll ( $where_str, $limit, 'faq_id asc', '*' );
		return $ret;
	}
	
	
	/*
	 * 更新评价总数，评论分数，平均分
	 * @param int $profile_id
	 * @param int $score
	 * @return int
	 */
	public function update_average_review($profile_id, $score=0)
	{
		$profile_id = intval($profile_id);
		$score = intval($score);
		$sql = "update pai_task_db.task_profile_tbl set total_score=total_score+{$score},reviews=reviews+1,rank=total_score/reviews where profile_id={$profile_id}";
		return db_simple_getdata($sql,false,101);
	}
	
	/*
	 * 获取所有商家用户ID
	 * @return array
	 */
	public function get_all_profile_user_id()
	{
		$this->set_task_profile_tbl();
		$ret = $this->findAll ( $where_str, "0,100000", 'user_id asc', 'distinct(user_id)' );
		foreach($ret as $val)
		{
			$user_arr[] = $val['user_id'];
		}
		
		return $user_arr;
	}


	/*
	 * 判断是否为商家
	 * @return array
	 */
	public function check_seller_by_user_id($user_id)
	{
		$this->set_task_seller_tbl();
		$user_id = (int)$user_id;
		if(!$user_id)
		{
			return array();
		}
		$ret = $this->find ("user_id={$user_id}");		
		return $ret;		
	}	


	/*
	 * 获取用户商家列表
	 * @return array
	 */
	public function get_user_profile_list($user_id)
	{
		$this->set_task_profile_tbl();
		$user_id = (int)$user_id;
		if(!$user_id)
		{
			return array();
		}
		$ret = $this->findAll ("user_id={$user_id}");		
		return $ret;		
	}

	

}

?>