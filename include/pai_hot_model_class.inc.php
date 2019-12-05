<?php
/*
 * 热门模特操作类
 */

class pai_hot_model_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_hot_model_tbl' );
	}
	
	/*
	 * 添加模特
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_model($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$insert_data['add_time'] = time();
		return $this->insert ( $insert_data ,'IGNORE');
	
	}
	
	/*
	 * 获取模特
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
	
	
	public function get_model_info($user_id)
	{
		$user_id = ( int ) $user_id;

		$row = $this->find ( "user_id={$user_id}" );
		return $row;
	}
	
	
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $user_id
	 * @return bool 
	 */
	public function update_model($data, $user_id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':user_id不能为空');
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->update($data, $where_str);
	}
	
	
	/*
	 * 删除模特
	 * 
	 * @param int $user_id
	 */
	public function del_model($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':user_id不能为空' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * 获取热门模特
	 * 
	 * @param string $limit 
	 */
	public function get_hot_model($b_select_count = false, $location_id, $limit = '0,6')
	{
		$where_str = "1 AND is_delete=0";
		if ($location_id)
		{
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		
		$ret = $this->get_model_list ( $b_select_count, $where_str, 'sort desc,user_id desc', $limit );
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}
		return $ret;
	}
	
	
	public function get_hot_model_beta($b_select_count = false, $location_id, $limit = '0,6')
	{
		$where_str = "1 AND is_delete=1";
		if ($location_id)
		{
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		
		$ret = $this->get_model_list ( $b_select_count, $where_str, 'sort desc,user_id desc', $limit );
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}

		return $ret;
	}
}

?>