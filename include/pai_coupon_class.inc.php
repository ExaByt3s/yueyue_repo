<?php
/**
 * 优惠券
 * @author Henry
 * @copyright 2015-03-02
 */

class pai_coupon_class extends POCO_TDG
{
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_coupon_db');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_type_tbl()
	{
		$this->setTableName('coupon_type_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_batch_cate_tbl()
	{
		$this->setTableName('coupon_batch_cate_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_batch_tbl()
	{
		$this->setTableName('coupon_batch_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_index_tbl()
	{
		$this->setTableName('coupon_index_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_scope_tbl()
	{
		$this->setTableName('coupon_scope_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_ref_user_tbl()
	{
		$this->setTableName('coupon_ref_user_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_ref_order_tbl()
	{
		$this->setTableName('coupon_ref_order_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_supply_tbl()
	{
		$this->setTableName('coupon_supply_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_supply_user_tbl()
	{
		$this->setTableName('coupon_supply_user_tbl');
	}
	
	/**
	 * 获取优惠方式列表
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return int|array
	 */
	public function get_type_list($b_select_count=false, $where_str='', $order_by='type_id ASC', $limit='0,20', $fields='*')
	{
		//查询
		$this->set_coupon_type_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		return $this->findAll($where_str, $limit, $order_by, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	public function add_batch_cate($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_batch_cate_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $cate_id
	 * @return boolean
	 */
	public function update_batch_cate($data, $cate_id)
	{
		//检查参数
		$cate_id = intval($cate_id);
		if( !is_array($data) || empty($data) || $cate_id<1 )
		{
			return false;
		}
		//保存
		$this->set_coupon_batch_cate_tbl();
		$affected_rows = $this->update($data, "cate_id={$cate_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取分类信息
	 * @param int $cate_id
	 * @return array
	 */
	public function get_batch_cate_info($cate_id)
	{
		$cate_id = intval($cate_id);
		if( $cate_id<1 )
		{
			return array();
		}
		$this->set_coupon_batch_cate_tbl();
		return $this->find("cate_id={$cate_id}");
	}
	
	/**
	 * 获取批次分类列表
	 * @param int $parent_id -1表示不限制
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_batch_cate_list($parent_id=-1, $b_select_count=false, $where_str='', $order_by='sort ASC,cate_id ASC', $limit='0,20', $fields='*')
	{
		//检查参数
		$parent_id = intval($parent_id);
		
		//整理查询条件
		$sql_where = '';
		if( $parent_id>=0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "parent_id={$parent_id}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_batch_cate_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 获取所有批次分类
	 * @param int $parent_id
	 * @param int $exclude_cate_id
	 * @return array
	 */
	public function get_batch_cate_list_all($parent_id=0, $exclude_cate_id=0)
	{
		//检查参数
		$parent_id = intval($parent_id);
		$exclude_cate_id = intval($exclude_cate_id);
		if( $parent_id<0 )
		{
			return array();
		}
		
		//获取分类
		$ret_list = array();
		$cate_list = $this->get_batch_cate_list($parent_id, false, "cate_id<>{$exclude_cate_id}", 'sort ASC,cate_id ASC', '0,99999999');
		foreach($cate_list as $cate_info)
		{
			$cate_id = intval($cate_info['cate_id']);
			$child_list = $this->get_batch_cate_list_all($cate_id, $exclude_cate_id);
			$ret_list[] = $cate_info;
			if( !empty($child_list) )
			{
				$ret_list = array_merge($ret_list, $child_list);
			}
		}
		return $ret_list;
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	public function add_batch($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_batch_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $batch_id
	 * @return boolean
	 */
	public function update_batch($data, $batch_id)
	{
		//检查参数
		$batch_id = intval($batch_id);
		if( !is_array($data) || empty($data) || $batch_id<1 )
		{
			return false;
		}
		//保存
		$this->set_coupon_batch_tbl();
		$affected_rows = $this->update($data, "batch_id={$batch_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 累积实际数量
	 * @param string $where_str
	 * @param int $quantity
	 * @return boolean
	 */
	private function margin_batch_real_quantity($batch_id, $quantity=1)
	{
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		if( $batch_id<1 || $quantity<1 )
		{
			return false;
		}
		$this->set_coupon_batch_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity+{$quantity} WHERE batch_id={$batch_id} AND (plan_quantity-real_quantity)>={$quantity}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取批次信息
	 * @param int $batch_id
	 * @return array
	 */
	public function get_batch_info($batch_id)
	{
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			return array();
		}
		$this->set_coupon_batch_tbl();
		return $this->find("batch_id={$batch_id}");
	}
	
	/**
	 * 获取列表
	 * @param int $cate_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_batch_list($cate_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$cate_id = intval($cate_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $cate_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "cate_id={$cate_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_batch_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 保存
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @param string $scope_value
	 * @return boolean
	 */
	public function add_scope($batch_id, $scope_type ,$scope_code, $scope_value)
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		$scope_value = trim($scope_value);
		if( $batch_id<1 || !in_array($scope_type, array('white', 'black')) || strlen($scope_code)<1 )
		{
			return false;
		}
		$data = array(
			'batch_id' => $batch_id,
			'scope_type' => $scope_type,
			'scope_code' => $scope_code,
			'scope_value' => $scope_value,
		);
		$this->set_coupon_scope_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
	
	/**
	 * 删除
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @return boolean
	 */
	public function del_scope($batch_id, $scope_type='' ,$scope_code='')
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		if($batch_id < 1)
		{
			return false;
		}
		$where_str = "batch_id='{$batch_id}'";
		if( strlen($scope_type)>0 )
		{
			$where_str .= ' AND scope_type=:x_scope_type';
			sqlSetParam($where_str, 'x_scope_type', $scope_type);
		}
		if( strlen($scope_code)>0 )
		{
			$where_str .= ' AND scope_code=:x_scope_code';
			sqlSetParam($where_str, 'x_scope_code', $scope_code);
		}
		$this->set_coupon_scope_tbl();
		$this->delete($where_str);
		return true;
	}
	
	/**
	 * 获取
	 * @param int $batch_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @return array
	 */
	public function get_scope_info($batch_id, $scope_type, $scope_code)
	{
		$batch_id = intval($batch_id);
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		if( $batch_id<1 || strlen($scope_type)<1 || strlen($scope_code)<1 )
		{
			return array();
		}
		$where_str = "batch_id='{$batch_id}' AND scope_type=:x_scope_type AND scope_code=:x_scope_code";
		sqlSetParam($where_str, 'x_scope_type', $scope_type);
		sqlSetParam($where_str, 'x_scope_code', $scope_code);
		$this->set_coupon_scope_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取列表
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_scope_list($batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$batch_id = intval($batch_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_scope_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_coupon($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_index_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 更新已发放
	 * @param string $coupon_sn
	 * @param array $more_info array('give_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_give($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_give' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_give=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已使用
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新未使用
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_not_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 0,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已兑现
	 * @param string $coupon_sn
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_cash($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_cash' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已结算
	 * @param string $coupon_sn
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_coupon_settle($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_settle' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1 AND is_cash=1 AND is_settle=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_coupon_info($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = 'coupon_sn=:x_coupon_sn';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_index_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取信息
	 * @param int $coupon_id
	 * @return array
	 */
	public function get_coupon_info_by_id($coupon_id)
	{
		$coupon_id = intval($coupon_id);
		if( $coupon_id<1 )
		{
			return array();
		}
		$this->set_coupon_index_tbl();
		return $this->find("coupon_id={$coupon_id}");
	}
	
	/**
	 * 获取信息
	 * @param int $batch_id
	 * @return array
	 */
	private function get_coupon_info_for_give($batch_id)
	{
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			return array();
		}
		$this->set_coupon_index_tbl();
		return $this->find("batch_id={$batch_id} AND is_give=0", "coupon_id ASC");
	}
	
	/**
	 * 获取列表
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_coupon_list($batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$batch_id = intval($batch_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_index_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_ref_user($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return false;
		}
		$this->set_coupon_ref_user_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 更新已使用
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_ref_user_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 1,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=0';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新未使用
	 * @param string $coupon_sn
	 * @param array $more_info array('used_time'=>0)
	 * @return boolean
	 */
	private function update_ref_user_not_used($coupon_sn, $more_info=array())
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_used' => 0,
		);
		$data = array_merge($more_info, $data);
		$where_str = 'coupon_sn=:x_coupon_sn AND is_used=1';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_ref_user_info_by_sn($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = "coupon_sn=:x_coupon_sn";
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_user_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取关联用户列表
	 * @param int $user_id
	 * @param int $batch_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_user_list($user_id, $batch_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		
		//整理查询条件
		$sql_where = '';
		
		if( $user_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "user_id={$user_id}";
		}
		
		if( $batch_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "batch_id={$batch_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_ref_user_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_ref_order($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_ref_order_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 更新已退还
	 * @param int $id
	 * @param array $more_info array('refund_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_refund($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_refund' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已兑现
	 * @param int $id
	 * @param double $cash_amount
	 * @param array $more_info array('cash_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_cash($id, $cash_amount, $more_info=array())
	{
		$id = intval($id);
		$cash_amount = $cash_amount*1;
		if( $id<1 || $cash_amount<0 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'cash_amount' => $cash_amount,
			'is_cash' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND used_amount>={$cash_amount} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已结算
	 * @param int $id
	 * @param array $more_info array('settle_time'=>0)
	 * @return boolean
	 */
	private function update_ref_order_settle($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_settle' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=1 AND is_settle=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 删除
	 * @param int $id
	 * @return boolean
	 */
	private function del_ref_order($id)
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		$this->set_coupon_ref_order_tbl();
		$affected_rows = $this->delete("id={$id} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取列表
	 * @param string $order_type
	 * @param int $order_rid
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_order_list($b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		//查询
		$this->set_coupon_ref_order_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		$list = $this->findAll($where_str, $limit, $order_by, $fields);
		return $list;
	}
	
	/**
	 * 获取列表
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array
	 */
	public function get_ref_order_list_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return array();
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		return $this->get_ref_order_list(false, $where_str, 'id ASC', '0,99999999');
	}
	
	/**
	 * 合计优惠金额
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return string 保留2位小数
	 */
	public function sum_ref_order_used_amount_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(used_amount) AS used_amount");
		return number_format($row['used_amount']*1, 2, '.', '');
	}
	
	/**
	 * 合计兑现金额
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return string 保留2位小数
	 */
	public function sum_ref_order_cash_amount_by_oid($channel_module, $channel_oid)
	{
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND channel_oid=:x_channel_oid";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_channel_oid', $channel_oid);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * 合计兑现金额
	 * @param string $channel_module
	 * @param int $event_id
	 * @return string 保留2位小数
	 */
	public function sum_ref_event_cash_amount_by_event_id($channel_module, $event_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		if( strlen($channel_module)<1 || $event_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id=:x_event_id";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_event_id', $event_id);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * 合计批次兑现金额
	 * @param int|array $batch_ids
	 * @return string 保留2位小数
	 */
	public function sum_ref_order_cash_amount_by_batch_id($batch_ids)
	{
		$batch_id_arr = array();
		
		//检查参数
		if( !is_array($batch_ids) )
		{
			$batch_ids = array($batch_ids);
		}
		foreach($batch_ids as $val)
		{
			$val = intval($val);
			if( $val>0 ) $batch_id_arr[] = $val;
		}
		if( empty($batch_id_arr) )
		{
			return 0;
		}
		$batch_id_str = implode(',', $batch_id_arr);
		$where_str = "batch_id IN ({$batch_id_str}) AND is_cash=1";
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(cash_amount) AS cash_amount");
		return number_format($row['cash_amount']*1, 2, '.', '');
	}
	
	/**
	 * 合计收入者的兑现金额
	 * @param string $channel_module
	 * @param int $event_id
	 * @param int $in_user_id
	 * @return string 保留2位小数
	 */
	public function sum_ref_order_in_amount_by_in_user_id($channel_module, $event_id, $in_user_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		$in_user_id = intval($in_user_id);
		if( strlen($channel_module)<1 || $event_id<1 || $in_user_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id={$event_id} AND in_user_id={$in_user_id}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(in_amount) AS in_amount");
		return number_format($row['in_amount']*1, 2, '.', '');
	}
	
	/**
	 * 合计机构的兑现金额
	 * @param string $channel_module
	 * @param int $event_id
	 * @param int $in_user_id
	 * @param int $org_user_id
	 * @return string 保留2位小数
	 */
	public function sum_ref_order_org_amount_by_org_user_id($channel_module, $event_id, $in_user_id, $org_user_id)
	{
		$channel_module = trim($channel_module);
		$event_id = intval($event_id);
		$in_user_id = intval($in_user_id);
		$org_user_id = intval($org_user_id);
		if( strlen($channel_module)<1 || $event_id<1 || $in_user_id<1 || $org_user_id<1 )
		{
			return 0;
		}
		$where_str = "channel_module=:x_channel_module AND event_id={$event_id} AND in_user_id={$in_user_id} AND org_user_id={$org_user_id}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($where_str, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * 获取信息
	 * @param int $id
	 * @return array
	 */
	public function get_ref_order_info($id)
	{
		$id = intval($id);
		if( $id<1 )
		{
			return array();
		}
		$this->set_coupon_ref_order_tbl();
		return $this->find("id={$id}");
	}
	
	/**
	 * 获取关联订单，使用了此优惠券
	 * @param string $coupon_sn
	 * @return array
	 */
	private function get_ref_order_info_by_sn($coupon_sn)
	{
		$coupon_sn = trim($coupon_sn);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		$where_str = "coupon_sn=:x_coupon_sn AND is_refund=0";
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);
		$this->set_coupon_ref_order_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取未结算的条件
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $where_str
	 * @return string
	 */
	private function get_unsettle_where_str($org_user_id, $channel_module='', $where_str='')
	{
		$org_user_id = intval($org_user_id);
		$channel_module = trim($channel_module);
		
		//整理查询条件
		$sql_where = '';
		
		if( $org_user_id>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id={$org_user_id}";
		}
		else
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id>0";
		}
		
		//约拍或外拍
		if( strlen($channel_module)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "channel_module=:x_channel_module";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		
		//未退还
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_refund=0";
		
		//已兑现
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_cash=1";
		
		//未结算
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_settle=0";
		
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		return $sql_where;
	}
	
	/**
	 * 获取机构的未结算金额
	 * @param int $org_user_id
	 * @param string $channel_module yuepai约拍 waipai外拍
	 * @param string $where_str
	 * @return double
	 */
	public function get_unsettle_org_amount($org_user_id, $channel_module='', $where_str='')
	{
		$sql_where = $this->get_unsettle_where_str($org_user_id, $channel_module, $where_str);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($sql_where, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * 获取机构的未结算列表
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_unsettle_ref_order_list($org_user_id, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
	{
		$sql_where = $this->get_unsettle_where_str($org_user_id, $channel_module, $where_str);
		return $this->get_ref_order_list($b_select_count, $sql_where, $order_by, $limit, $fields);
	}
	
	/**
	 * 获取已结算的条件
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $where_str
	 * @return string
	 */
	private function get_settle_where_str($org_user_id, $channel_module='', $where_str='')
	{
		$org_user_id = intval($org_user_id);
		$channel_module = trim($channel_module);
		
		//整理查询条件
		$sql_where = '';
		
		if( $org_user_id>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id={$org_user_id}";
		}
		else
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "org_user_id>0";
		}
		
		//约拍或外拍
		if( strlen($channel_module)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= "channel_module=:x_channel_module";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		
		//未退还
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_refund=0";
		
		//已兑现
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_cash=1";
		
		//已结算
		if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
		$sql_where .= "is_settle=1";
		
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where)>0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		return $sql_where;
	}
	
	/**
	 * 获取机构的已结算金额
	 * @param int $org_user_id
	 * @param string $channel_module yuepai约拍 waipai外拍
	 * @param string $where_str
	 * @return double
	 */
	public function get_settle_org_amount($org_user_id, $channel_module='', $where_str='')
	{
		$sql_where = $this->get_settle_where_str($org_user_id, $channel_module, $where_str);
		$this->set_coupon_ref_order_tbl();
		$row = $this->find($sql_where, null, "SUM(org_amount) AS org_amount");
		return number_format($row['org_amount']*1, 2, '.', '');
	}
	
	/**
	 * 获取机构的已结算列表
	 * @param int $org_user_id
	 * @param string $channel_module
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_settle_ref_order_list($org_user_id, $channel_module='', $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
	{
		$sql_where = $this->get_settle_where_str($org_user_id, $channel_module, $where_str);
		return $this->get_ref_order_list($b_select_count, $sql_where, $order_by, $limit, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_supply($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_supply_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 获取信息
	 * @param int $supply_id
	 * @return array
	 */
	private function get_supply_info($supply_id)
	{
		$supply_id = intval($supply_id);
		if( $supply_id<1 )
		{
			return array();
		}
		$this->set_coupon_supply_tbl();
		return $this->find("supply_id={$supply_id}");
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_supply_user($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_supply_user_tbl();
		return $this->insert($data, 'IGNORE');
	}
		
	/**
	 * 更新已消息通知
	 * @param int $id
	 * @param array $more_info array('message_time'=>0)
	 * @return boolean
	 */
	private function update_supply_user_message($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_message' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_supply_user_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_message=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 更新已领取
	 * @param int $id
	 * @param array $more_info array('coupon_sn'=>'', 'give_time'=>0)
	 * @return boolean
	 */
	private function update_supply_user_give($id, $more_info=array())
	{
		$id = intval($id);
		if( $id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_give' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_coupon_supply_user_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_give=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param int $supply_id
	 * @param int $user_id
	 * @return array
	 */
	public function get_supply_user_info($supply_id, $user_id)
	{
		$supply_id = intval($supply_id);
		$user_id = intval($user_id);
		if( $supply_id<1 || $user_id<1 )
		{
			return array();
		}
		$this->set_coupon_supply_user_tbl();
		return $this->find("supply_id={$supply_id} AND user_id={$user_id}");
	}
	
	/**
	 * 获取随机字符串
	 * @param int $length
	 * @return string
	 */
	private function get_rand_str($length)
	{
		$code = '';
		$length = intval($length);
		if( $length<1 )
		{
			return $code;
		}
		//第一位规定是1，预留2至9为以后扩展用
		$code = '1';
		$length--;
		$pattern = '5348029716';
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern, mt_rand(0,9), 1);
		}
		return $code;
	}
	
	/**
	 * 创建批次
	 * @param array $data
	 * @return int 成功则返回批次ID，失败则返回0
	 */
	public function create_batch_old($data)
	{
		//检查参数
		$cate_id = intval($data['cate_id']);
		$batch_name = trim($data['batch_name']);
		$coupon_type_id = intval($data['coupon_type_id']);
		$coupon_face_value = number_format($data['coupon_face_value']*1, 2, '.', '')*1;
		$coupon_start_time = intval($data['coupon_start_time']);
		$coupon_end_time = intval($data['coupon_end_time']);
		$scope_module_type = trim($data['scope_module_type']);
		$scope_order_total_amount = number_format($data['scope_order_total_amount'], 2, '.', '')*1;
		$scope_model_user_id = trim($data['scope_model_user_id']);
		$scope_org_user_id = trim($data['scope_org_user_id']);
		$scope_event_id = trim($data['scope_event_id']);
		$scope_event_user_id = trim($data['scope_event_user_id']);
		$scope_service_id = trim($data['scope_service_id']);
		$scope_mall_type_id = trim($data['scope_mall_type_id']);
		$scope_seller_user_id = trim($data['scope_seller_user_id']);
		if( $cate_id<1 || strlen($batch_name)<1 || $coupon_type_id<1 || $coupon_face_value<=0 || $coupon_start_time<1 || $coupon_end_time<1 )
		{
			return 0;
		}
		//新增批次
		unset($data['scope_event_user_id']);
		unset($data['scope_mall_type_id']);
		unset($data['scope_seller_user_id']);
		$batch_data = $data;
		$batch_data['cate_id'] = $cate_id;
		$batch_data['batch_name'] = $batch_name;
		$batch_data['coupon_type_id'] = $coupon_type_id;
		$batch_data['coupon_face_value'] = $coupon_face_value;
		$batch_data['coupon_start_time'] = $coupon_start_time;
		$batch_data['coupon_end_time'] = $coupon_end_time;
		$batch_data['scope_module_type'] = $scope_module_type;
		$batch_data['scope_order_total_amount'] = $scope_order_total_amount;
		$batch_data['scope_model_user_id'] = $scope_model_user_id;
		$batch_data['scope_org_user_id'] = $scope_org_user_id;
		$batch_data['scope_event_id'] = $scope_event_id;
		if( !isset($batch_data['add_time']) ) $batch_data['add_time'] = time();
		$batch_id = $this->add_batch($batch_data);
		if( $batch_id<1 )
		{
			return 0;
		}
		//保存范围，可参数方法check_scope_code的说明
		if( strlen($scope_module_type)>0 )
		{
			$this->add_scope($batch_id, 'white', 'module_type', $scope_module_type);
		}
		if( $scope_order_total_amount>0 )
		{
			$this->add_scope($batch_id, 'white', 'order_total_amount', $scope_order_total_amount);
		}
		if( strlen($scope_model_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'model_user_id', $scope_model_user_id);
		}
		if( strlen($scope_org_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'org_user_id', $scope_org_user_id);
		}
		if( strlen($scope_event_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'event_id', $scope_event_id);
		}
		if( strlen($scope_event_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'event_user_id', $scope_event_user_id);
		}
		if( strlen($scope_service_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'service_id', $scope_service_id);
		}
		if( strlen($scope_mall_type_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'mall_type_id', $scope_mall_type_id);
		}
		if( strlen($scope_seller_user_id)>0 )
		{
			$this->add_scope($batch_id, 'white', 'seller_user_id', $scope_seller_user_id);
		}
		return $batch_id;
	}
	
	/**
	 * 创建批次
	 * @param array $data
	 * @return int 成功则返回批次ID，失败则返回0
	 */
	public function create_batch($data)
	{
		//检查参数
		$cate_id = intval($data['cate_id']);
		$batch_name = trim($data['batch_name']);
		$coupon_type_id = intval($data['coupon_type_id']);
		$coupon_face_value = number_format($data['coupon_face_value']*1, 2, '.', '')*1;
		$coupon_start_time = intval($data['coupon_start_time']);
		$coupon_end_time = intval($data['coupon_end_time']);
		$scope_module_type = trim($data['scope_module_type']);
		$scope_order_total_amount = number_format($data['scope_order_total_amount'], 2, '.', '')*1;
		if( $cate_id<1 || strlen($batch_name)<1 || $coupon_type_id<1 || $coupon_face_value<=0 || $coupon_start_time<1 || $coupon_end_time<1 )
		{
			return 0;
		}
		//新增批次
		$batch_data = $data;
		$batch_data['cate_id'] = $cate_id;
		$batch_data['batch_name'] = $batch_name;
		$batch_data['coupon_type_id'] = $coupon_type_id;
		$batch_data['coupon_face_value'] = $coupon_face_value;
		$batch_data['coupon_start_time'] = $coupon_start_time;
		$batch_data['coupon_end_time'] = $coupon_end_time;
		$batch_data['scope_module_type'] = $scope_module_type;
		$batch_data['scope_order_total_amount'] = $scope_order_total_amount;
		if( !isset($batch_data['add_time']) ) $batch_data['add_time'] = time();
		return $this->add_batch($batch_data);
	}
	
	/**
	 * 生成优惠券
	 * @param int $batch_id	批次ID
	 * @param array $more_info 修正或追加优惠券信息 array('start_time'=>0, 'end_time'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function create_coupon($batch_id, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		//检查参数
		$batch_id = intval($batch_id);
		if( $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		
		//获取批次信息
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -2;
			$result['message'] = '批次不存在';
			return $result;
		}
		$type_id = intval($batch_info['coupon_type_id']);
		$face_value = number_format($batch_info['coupon_face_value']*1, 2, '.', '')*1;
		$face_max = number_format($batch_info['coupon_face_max']*1, 2, '.', '')*1;
		$start_time = intval($batch_info['coupon_start_time']);
		$end_time = intval($batch_info['coupon_end_time']);
		if( isset($more_info['start_time']) ) $start_time = intval($more_info['start_time']);
		if( isset($more_info['end_time']) ) $end_time = intval($more_info['end_time']);
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//累积优惠券实际数量
		$ret = $this->margin_batch_real_quantity($batch_id, 1);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = '已达到预发数量';
			return $result;
		}
		
		$coupon_id = 0;
		$coupon_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			$rand_str = $this->get_rand_str(16);
			if( strlen($rand_str)<1 )
			{
				//优惠券码为空
				break;
			}
			$tmp = $this->get_coupon_info($rand_str);
			if( !empty($tmp) )
			{
				//优惠券码已存在
				$while_count++;
				continue;
			}
			$coupon_data = array(
				'coupon_sn' => $rand_str,
				'batch_id' => $batch_id,
				'type_id' => $type_id,
				'face_value' => $face_value,
				'face_max' => $face_max,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'add_time' => time(),
			);
			$id = $this->add_coupon($coupon_data);
			if( $id<1 )
			{
				//可能有另一个请求也在生成优惠券
				$while_count++;
				continue;
			}
			$coupon_id = $id;
			$coupon_sn = $rand_str;
			break;
		}
		if( $coupon_id<1 || strlen($coupon_sn)<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '生成失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * 生成多个优惠券
	 * @param int $batch_id 批次ID
	 * @param int $quantity 此次生成数量
	 * @param array $more_info 修正或追加优惠券信息 array('start_time'=>0, 'end_time'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'quantity'=>0)
	 */
	public function create_coupon_multiple($batch_id, $quantity, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'quantity'=>0);
		
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		if( $batch_id<1 || $quantity<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取批次信息
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -2;
			$result['message'] = '批次不存在';
			return $result;
		}
		
		//计算此次生成数量
		$plan_quantity = intval($batch_info['plan_quantity']); //计划数量
		$real_quantity = intval($batch_info['real_quantity']); //实际数量
		$remain_quantity = $plan_quantity - $real_quantity; //剩余数量
		if( $remain_quantity<1 )
		{
			$result['result'] = -3;
			$result['message'] = '已达到预发数量';
			return $result;
		}
		if( $quantity>$remain_quantity ) $quantity = $remain_quantity;
		
		//生成优惠券
		$create_quantity = 0; //实际生成数量
		for($i=1; $i<=$quantity; $i++)
		{
			$create_ret = $this->create_coupon($batch_id, $more_info);
			if( $create_ret['result']!=1 )
			{
				break;
			}
			$create_quantity++;
		}
		if( $create_quantity<1 )
		{
			$result['result'] = -4;
			$result['message'] = '生成失败';
			return $result;
		}
		if( $create_quantity<$quantity )
		{
			$result['result'] = 1;
			$result['message'] = '部分成功';
			$result['quantity'] = $create_quantity;
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['quantity'] = $create_quantity;
		return $result;
	}
	
	/**
	 * 发放优惠券
	 * @param int $user_id
	 * @param string $coupon_sn
	 * @param boolean $b_valid 是否检查优惠券有效性
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon($user_id, $coupon_sn, $b_valid=true)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		//检查参数
		$user_id = intval($user_id);
		$coupon_sn = trim($coupon_sn);
		if( $user_id<1 || strlen($coupon_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//除了优惠码，其余的当套餐码处理
		if( !preg_match('/^[0-8]\d{15}$/isU', $coupon_sn) )
		{
			$coupon_package_obj = POCO::singleton('pai_coupon_package_class');
			return $coupon_package_obj->exchange_package($user_id, $coupon_sn);
		}
		
		//获取优惠券信息
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		if( $coupon_info['is_give']!=0 )
		{
			$result['result'] = -3;
			$result['message'] = '此券已发放';
			return $result;
		}
		if( $b_valid )
		{
			$valid_ret = $this->check_coupon_valid($coupon_info);
			if( $valid_ret['result']!=1 )
			{
				$result['result'] = -4;
				$result['message'] = $valid_ret['message'];
				return $result;
			}
		}
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//更新已发放
		$ret = $this->update_coupon_give($coupon_sn, array('give_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '此券已发放';
			return $result;
		}
		
		//新增关联
		$ref_data = array(
			'user_id' => $user_id,
			'coupon_sn' => $coupon_sn,
			'batch_id' => $coupon_info['batch_id'],
			'type_id' => $coupon_info['type_id'],
			'start_time' => $coupon_info['start_time'],
			'end_time' => $coupon_info['end_time'],
			'is_used' => $coupon_info['is_used'],
			'used_time' => $coupon_info['used_time'],
			'add_time' => $cur_time,
		);
		$id = $this->add_ref_user($ref_data);
		if( $id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '此券发放失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * 生成并发放优惠券
	 * @param int $user_id
	 * @param int $batch_id 批次ID
	 * @param array $more_info 修正或追加优惠券信息 array('start_time'=>0, 'end_time'=>0)
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon_by_create($user_id, $batch_id, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		if( $user_id<1 || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//检查批次是否审核
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) || $batch_info['check_status']!=1 )
		{
			$result['result'] = -1;
			$result['message'] = '批次未审核';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//生成优惠码
		$create_ret = $this->create_coupon($batch_id, $more_info);
		if( $create_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -2;
			$result['message'] = '生成失败';
			return $result;
		}
		$coupon_sn = trim($create_ret['coupon_sn']);
		
		//发放优惠券
		$give_ret = $this->give_coupon($user_id, $coupon_sn, false);
		if( $give_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -3;
			$result['message'] = '发放失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * 查找并发放优惠券
	 * @param int $user_id
	 * @param int $batch_id 批次ID
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function give_coupon_by_find($user_id, $batch_id)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$batch_id = intval($batch_id);
		if( $user_id<1 || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//检查批次是否审核
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) || $batch_info['check_status']!=1 )
		{
			$result['result'] = -1;
			$result['message'] = '批次未审核';
			return $result;
		}
		
		//采用循环尝试，没有采用锁表
		$coupon_sn = '';
		$coupon_sn_tmp = '';
		$while_count = 0;
		while($while_count<9999)
		{
			//查找优惠码
			$coupon_info_tmp = $this->get_coupon_info_for_give($batch_id);
			if( empty($coupon_info_tmp) )
			{
				//没有优惠券了
				$result['result'] = -2;
				$result['message'] = '发放完了';
				return $result;
			}
			if( $coupon_sn_tmp==$coupon_info_tmp['coupon_sn'] )
			{
				//与上次循环的一样
				break;
			}
			$coupon_sn_tmp = trim($coupon_info_tmp['coupon_sn']);
			
			//发放优惠券
			$give_ret = $this->give_coupon($user_id, $coupon_sn_tmp, false);
			if( $give_ret['result']!=1 )
			{
				$while_count++;
				continue;
			}
			
			$coupon_sn = $coupon_sn_tmp;
			break;
		}
		if( strlen($coupon_sn)<1 )
		{
			$result['result'] = -3;
			$result['message'] = '发放失败';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * 检查优惠券的有效性
	 * 此方法不检查is_give，请自行检查判断
	 * @param array $coupon_info
	 * @param int $cur_time 当前时间
	 * @param bool $check_is_used 是否检查
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function check_coupon_valid($coupon_info, $cur_time=0, $check_is_used=true)
	{
		$result = array('result'=>0, 'message'=>'');
		
		if( !is_array($coupon_info) || empty($coupon_info) )
		{
			$result['result'] = -1;
			$result['message'] = '此券不存在';
			return $result;
		}
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		$coupon_sn = trim($coupon_info['coupon_sn']);
		if( strlen($coupon_sn)<1 )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		
		$is_used = intval($coupon_info['is_used']);
		if( $check_is_used && $is_used==1 )
		{
			$result['result'] = -3;
			$result['message'] = '此券已使用';
			return $result;
		}
		
		$is_cash = intval($coupon_info['is_cash']);
		if( $is_cash==1 )
		{
			$result['result'] = -4;
			$result['message'] = '此券已使用';
			return $result;
		}
		
		$start_time = intval($coupon_info['start_time']);
		if( $start_time>$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '此券未到有效期';
			return $result;
		}
		
		$end_time = intval($coupon_info['end_time']);
		if( $end_time<$cur_time )
		{
			$result['result'] = -6;
			$result['message'] = '此券已过有效期';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '有效';
		return $result;
	}
	
	/**
	 * 获取适用范围数组
	 * @return array
	 */
	public function get_scope_code_arr()
	{
		return array(
			'all' => '全部',
			'order_total_amount' => '订单金额',
			'org_user_id' => '机构用户ID',
			'module_type' => '模块类型',
			'mall_order_type' => '订单类型（商城系统）',
			'mall_type_id' => '服务品类ID（商城系统）',
			'seller_user_id' => '商家用户ID（商城系统、外拍系统）',
			'mall_goods_id' => '商品ID（商城系统）',
			'mall_stage_id' => '活动场次ID（商城系统）',
			'event_id' => '活动ID（外拍系统）',
			'event_user_id' => '组织者用户ID（外拍系统）',
			'model_user_id' => '模特用户ID（约拍系统）',
			'location_id' => '地区ID（外拍系统）',
			'service_id' => '服务类型ID（TT系统）',
		);
	}
	
	/**
	 * 检查是否有在范围内
	 * @param array $scope_info
	 * @param array $param_info
	 * @param int $cur_time 当前时间
	 * @return boolean 没参数或参数错误，返回false
	 * @tutorial
	 * 
	 * $scope_code $scope_value举例：
	 * module_type yuepai#waipai#topic 模块类型
	 * order_amount 5.00 订单金额
	 * model_user_id 1#2 模特用户ID
	 * org_user_id 1#2 机构用户ID
	 * event_id 1#2 活动ID
	 * 
	 */
	private function check_scope_code($scope_info, $param_info, $cur_time=0)
	{
		$result = false;
		
		//检查参数
		if( !is_array($scope_info) || empty($scope_info) )
		{
			return $result;
		}
		if( !is_array($param_info) ) $param_info = array();
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		//整理范围
		$scope_code = strtolower(trim($scope_info['scope_code']));
		$scope_value = trim($scope_info['scope_value']);
		
		$scope_value_arr = array();
		$scope_value_arr_tmp = explode('#', $scope_value);
		foreach ($scope_value_arr_tmp as $val)
		{
			$val = trim($val);
			if( strlen($val)<1 ) continue;
			$scope_value_arr[] = $val;
		}
		unset($val, $scope_value_arr_tmp);
		
		//判断范围
		switch($scope_code)
		{
			case 'all':
				$result = true;
				break;
			case 'module_type':
				$module_type = trim($param_info['module_type']);
				if( in_array($module_type, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'order_total_amount':
				$order_total_amount = $param_info['order_total_amount']*1;
				if( $order_total_amount>0 && $order_total_amount>=$scope_value*1 )
				{
					$result = true;
				}
				break;
			case 'model_user_id':
				$model_user_id = trim($param_info['model_user_id']);
				if( in_array($model_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'org_user_id':
				$org_user_id = trim($param_info['org_user_id']);
				if( in_array($org_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'location_id':
				$location_id_str = trim($param_info['location_id']);
				$location_id_arr = explode(',', $location_id_str);
				foreach($location_id_arr as $location_id)
				{
					$location_id = trim($location_id);
					if( strlen($location_id)<1 ) continue;
					foreach($scope_value_arr as $tmp)
					{
						if( preg_match('/^\d+$/isU', $tmp) && preg_match('/^'.$tmp.'/isU', $location_id) )
						{
							$result = true;
						}
					}
				}
				break;
			case 'event_id':
				$event_id = trim($param_info['event_id']);
				if( in_array($event_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'event_user_id':
				$event_user_id = trim($param_info['event_user_id']);
				if( in_array($event_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'service_id':
				$service_id = trim($param_info['service_id']);
				if( in_array($service_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_type_id':
				$mall_type_id = trim($param_info['mall_type_id']);
				if( in_array($mall_type_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'seller_user_id':
				$seller_user_id = trim($param_info['seller_user_id']);
				if( in_array($seller_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_goods_id':
				$mall_goods_id = trim($param_info['mall_goods_id']);
				if( in_array($mall_goods_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_order_type':
				$mall_order_type = trim($param_info['mall_order_type']);
				if( in_array($mall_order_type, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'mall_stage_id':
				$mall_stage_id = trim($param_info['mall_stage_id']);
				if( in_array($mall_stage_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			default:
				break;
		}
		
		return $result;
	}
	
	/**
	 * 检查适用范围
	 * @param array $batch_info
	 * @param array $param_info
	 * @param int $cur_time 当前时间
	 * @return array array('result'=>0, 'message'=>'', 'message_white_false'=>'', 'message_black_true'=>'')
	 */
	private function check_coupon_scope($batch_info, $param_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'', 'message_white_false'=>'', 'message_black_true'=>'');
		
		$batch_id = intval($batch_info['batch_id']);
		if( !is_array($batch_info) || empty($batch_info) || $batch_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '批次不存在';
			return $result;
		}
		if( !is_array($param_info) ) $param_info = array();
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
		
		//检查审核状态
		$check_status = intval($batch_info['check_status']);
		if( $check_status!=1 )
		{
			$result['result'] = -2;
			$result['message'] = '批次审核状态错误';
			return $result;
		}
		
		//获取配置
		$scope_list = $this->get_scope_list($batch_id, false, '', '', '0,99999999');
		if( !is_array($scope_list) || empty($scope_list) )
		{
			$result['result'] = -3;
			$result['message'] = '批次没有适用范围';
			return $result;
		}
		
		//整理黑白名单
		$white_list = array();
		$black_list = array();
		foreach ($scope_list as $val)
		{
			$scope_type = strtolower(trim($val['scope_type']));
			if( $scope_type=='white' )
			{
				$white_list[] = $val;
			}
			elseif( $scope_type=='black' )
			{
				$black_list[] = $val;
			}
		}
		if( empty($white_list) && empty($black_list) )
		{
			$result['result'] = -4;
			$result['message'] = '批次适用范围错误';
			return $result;
		}
		
		//检查白名单
		$ret_white = true;
		$white_info_false = array();
		foreach ($white_list as $white_info)
		{
			$ret = $this->check_scope_code($white_info, $param_info, $cur_time);
			if( !$ret )
			{
				//不在条件的范围内，则跳出
				$ret_white = false;
				$white_info_false = $white_info;
				break;
			}
		}
		
		//检查黑名单
		$ret_black = false;
		$black_info_true = array();
		foreach ($black_list as $black_info)
		{
			$ret = $this->check_scope_code($black_info, $param_info, $cur_time);
			if( $ret )
			{
				//在条件的范围内，则跳出
				$ret_black = true;
				$black_info_true = $black_info;
				break;
			}
		}
		
		if( $ret_white==true && $ret_black==false)
		{
			$result['result'] = 1;
			$result['message'] = '适用';
		}
		else
		{
			$result['result'] = -5;
			$result['message'] = '不适用';
			$result['message_white_false'] = http_build_query($white_info_false);
			$result['message_black_true'] = http_build_query($black_info_true);
		}
		return $result;
	}
	
	/**
	 * 检查特别限制
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @param int $cur_time
	 * @return array
	 */
	private function check_coupon_limit($user_id, $type_id, $param_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$cur_time = intval($cur_time);
		if( $user_id<1 || $type_id<1 || !is_array($param_info) || empty($param_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( $cur_time<1 ) $cur_time = time();
		
		//约拍单
		if( $param_info['channel_module']=='yuepai' )
		{
			$model_user_id_tmp = intval($param_info['model_user_id']);
			
			//检查模特是否已审核
			$model_audit_obj = POCO::singleton('pai_model_audit_class');
			$is_approval_tmp = $model_audit_obj->check_model_is_approval_for_coupon($model_user_id_tmp);
			if( !$is_approval_tmp && !in_array($model_user_id_tmp, array(100028, 103511)) )
			{
				$result['result'] = -2;
				$result['message'] = '由于该模特未经系统审核，本次交易无法使用优惠劵';
				return $result;
			}
			
			//检查买家优惠券使用次数
			$date_id_str = '';
			$date_id_sp = '';
			$today_begin_time = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$today_end_time = strtotime(date('Y-m-d 23:59:59', $cur_time));
			$channel_oid_list_tmp = $this->get_ref_order_list(false, "channel_module='yuepai' AND user_id={$user_id} AND type_id=1 AND is_refund=0 AND {$today_begin_time}<=add_time AND add_time<={$today_end_time}", '', '0,99999999', 'channel_oid');
			foreach($channel_oid_list_tmp as $channel_oid_info_tmp)
			{
				$date_id_str .= $date_id_sp . $channel_oid_info_tmp['channel_oid']*1;
				$date_id_sp = ',';
			}
			if( strlen($date_id_str)>0 )
			{
				include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
				$event_date_obj = POCO::singleton('event_date_class');
				
				//特殊处理，光线收款模特113621
				if( in_array($model_user_id_tmp, array(113621)) )
				{
					$today_tmp = $event_date_obj->get_all_event_date(true, "date_id IN ({$date_id_str}) AND pay_status=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time} AND to_date_id={$model_user_id_tmp}");
					if( $today_tmp>=3 )
					{
						$result['result'] = -3;
						$result['message'] = '光线课程优惠券每天只能使用三张';
						return $result;
					}
				}
				else
				{
					$today_tmp = $event_date_obj->get_all_event_date(true, "date_id IN ({$date_id_str}) AND pay_status=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time}");
					if( $today_tmp>=1 )
					{
						$result['result'] = -4;
						$result['message'] = '约拍优惠券每天只能使用一张，每天0：00为更新时间';
						return $result;
					}
				}
			}
		}
        elseif( $param_info['channel_module']=='mall_order' && $param_info['mall_order_type']=='activity' )//活动单
        {
            $activity_id = intval($param_info['mall_goods_id']);
            $stage_id = intval($param_info['mall_stage_id']);
            if( $activity_id<1 || $stage_id<1 )
            {
                $result['result'] = -6;
                $result['message'] = '参数错误';
                return $result;
            }
            $mall_order_obj = POCO::singleton('pai_mall_order_class');
            $count = $mall_order_obj->get_order_list_by_activity_stage($activity_id, $stage_id, -1, true, "is_pay=1 AND is_use_coupon=1 AND buyer_user_id={$user_id}");

            if( $count>=1 )
            {
                $result['result'] = -6;
                $result['message'] = '此场次只能使用一张优惠券';
                return $result;
            }
        }
		elseif( $param_info['channel_module']=='mall_order' ) //商城单
		{
			$mall_type_id_tmp = intval($param_info['mall_type_id']);
			$seller_user_id_tmp = intval($param_info['seller_user_id']);
			
			//检查买家优惠券使用次数
			$order_id_str = '';
			$order_id_sp = '';
			$today_begin_time = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$today_end_time = strtotime(date('Y-m-d 23:59:59', $cur_time));
			$channel_oid_list_tmp = $this->get_ref_order_list(false, "channel_module='mall_order' AND user_id={$user_id} AND type_id=1 AND is_refund=0 AND {$today_begin_time}<=add_time AND add_time<={$today_end_time}", '', '0,99999999', 'channel_oid');
			foreach($channel_oid_list_tmp as $channel_oid_info_tmp)
			{
				$order_id_str .= $order_id_sp . $channel_oid_info_tmp['channel_oid']*1;
				$order_id_sp = ',';
			}
			if( strlen($order_id_str)>0 )
			{
				$mall_order_obj = POCO::singleton('pai_mall_order_class');
				
				//特殊处理
				if( in_array($seller_user_id_tmp, array(103511, 130968)) )
				{
					$today_tmp = $mall_order_obj->get_order_list($mall_type_id_tmp, -1, true, "order_id IN ({$order_id_str}) AND is_use_coupon=1 AND is_pay=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time} AND seller_user_id={$seller_user_id_tmp}");
					if( $today_tmp>=3 )
					{
						$result['result'] = -5;
						$result['message'] = '此类服务每天只能使用3张优惠券';
						return $result;
					}
				}
				else
				{
					$today_tmp = $mall_order_obj->get_order_list($mall_type_id_tmp, -1, true, "order_id IN ({$order_id_str}) AND is_use_coupon=1 AND is_pay=1 AND {$today_begin_time}<=pay_time AND pay_time<={$today_end_time}");
					if( $today_tmp>=1 )
					{
						$result['result'] = -5;
						$result['message'] = '此类服务每天只能使用一张优惠券';
						return $result;
					}
				}
			}
		}
		
		$result['result'] = 1;
		$result['message'] = '适用';
		return $result;
	}
	
	/**
	 * 计算使用金额
	 * 限制：优惠金额要小于订单金额
	 * @param array $coupon_info
	 * @param array $param_info array( 'order_total_amount'=>0 )
	 * @return double 保留2位小数
	 */
	public function cal_used_amount($coupon_info, $param_info)
	{
		//检查参数
		$type_id = intval($coupon_info['type_id']);
		$face_value = number_format($coupon_info['face_value']*1, 2, '.', '')*1;
		$face_max = number_format($coupon_info['face_max']*1, 2, '.', '')*1;
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $type_id<1 || $face_value<=0 || $order_total_amount<=0 )
		{
			return 0;
		}
		
		//计算
		$used_amount = 0;
		if( $type_id==1 ) //现金券
		{
			if( $face_value<$order_total_amount ) //限制，优惠金额要小于订单金额
			{
				$used_amount = $face_value;
			}
		}
		elseif( $type_id==2 ) //折扣券
		{
			if( $face_value>0 && $face_value<100 && $face_max>0 ) //限制，优惠比例要合理，并且要有限额
			{
				$used_amount = $order_total_amount - round($order_total_amount*$face_value/100, 2);
				$used_amount = min($used_amount, $face_max);
				if( $used_amount>=$order_total_amount ) $used_amount = 0;
			}
		}
		elseif( $type_id==3 ) //改价券
		{
			if( $face_value<$order_total_amount && $face_max>0 ) //限制，指定金额要小于订单金额，并且要有限额
			{
				$used_amount = $order_total_amount - $face_value;
				$used_amount = min($used_amount, $face_max);
				if( $used_amount>=$order_total_amount ) $used_amount = 0;
			}
		}
		
		if( $used_amount<=0 )
		{
			return 0;
		}
		return number_format($used_amount, 2, '.', '')*1;
	}
	
	/**
	 * 检查优惠券有效性、适用范围
	 * 用于使用前，对优惠券进行检查
	 * @param int $type_id
	 * @param string $coupon_sn
	 * @param array $param_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //订单类型
	 *  'channel_oid'=>0, //订单ID
	 *  'module_type'=>'yuepai', //模块类型 waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //订单总金额
	 *  'model_user_id'=>0, //模特用户ID（约拍、专题）
	 *  'org_user_id'=>0, //机构用户ID
	 *  'event_id'=>'', //活动ID
	 * );
	 * 
	 */
	public function check_coupon($user_id, $type_id, $coupon_sn, $param_info)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$coupon_sn = trim($coupon_sn);
		if( !is_array($param_info) ) $param_info = array();
		$channel_module = trim($param_info['channel_module']);
		$channel_oid = intval($param_info['channel_oid']);
		$module_type = trim($param_info['module_type']);
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $user_id<1 || $type_id<1 || strlen($coupon_sn)<1 || strlen($module_type)<1 || $order_total_amount<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$cur_time = time();	//当前时间，以下统一使用此时间
		
		//获取优惠券信息
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		$type_id = intval($coupon_info['type_id']);
		/*
		if( $type_id!=$coupon_info['type_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '此券类型不正确';
			return $result;
		}
		*/
		if( $coupon_info['is_give']==0 )
		{
			$result['result'] = -4;
			$result['message'] = '此券未兑换';
			return $result;
		}
		//检查券是不是发放给此用户了
		$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
		if( empty($ref_user_info) || $ref_user_info['user_id']!=$user_id )
		{
			$result['result'] = -5;
			$result['message'] = '此券已被兑换';
			return $result;
		}
		//检查关联订单，是否有使用了此优惠券
		$ref_order_info = $this->get_ref_order_info_by_sn($coupon_sn);
		if( (empty($ref_order_info) && $coupon_info['is_used']==1) || (!empty($ref_order_info) && $coupon_info['is_used']==0))
		{
			$result['result'] = -6;
			$result['message'] = '此券已使用';
			return $result;
		}
		if( !empty($ref_order_info) )
		{
			if($ref_order_info['channel_module']!=$channel_module || $ref_order_info['channel_oid']!=$channel_oid)
			{
				$result['result'] = -7;
				$result['message'] = '此券已使用';
				return $result;
			}
		}
		if( $coupon_info['is_cash']==1 )
		{
			$result['result'] = -8;
			$result['message'] = '此券已使用';
			return $result;
		}
		
		//获取批次信息
		$batch_id = intval($coupon_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -9;
			$result['message'] = '批次不存在';
			return $result;
		}
		
		//检查优惠券有效性
		$valid_ret = $this->check_coupon_valid($coupon_info, $cur_time, false);
		if( $valid_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $valid_ret['message'];
			return $result;
		}
		
		//检查优惠券适应范围
		$scope_ret = $this->check_coupon_scope($batch_info, $param_info, $cur_time);
		if( $scope_ret['result']!=1 )
		{
			$result['result'] = -11;
			$result['message'] = $scope_ret['message'];
			$result['message_white_false'] = $scope_ret['message_white_false'];
			$result['message_black_true'] = $scope_ret['message_black_true'];
			return $result;
		}
		
		//计算使用金额
		$used_amount = $this->cal_used_amount($coupon_info, $param_info);
		if( $used_amount<=0 )
		{
			$result['result'] = -13;
			$result['message'] = '此券不适用';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '此券可用';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * 使用优惠券
	 * 暂时限制：一个订单只能使用一张优惠券
	 * 长久限制：一张优惠券只能用在一个订单
	 * @param int $user_id
	 * @param int $type_id
	 * @param string $coupon_sn
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @param array $param_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'module_type'=>'yuepai', //模块类型 waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //订单总金额
	 *  'model_user_id'=>0, //模特用户ID（约拍、专题）
	 *  'org_user_id'=>0, //机构用户ID
	 *  'event_id'=>'', //活动ID
	 * );
	 * 
	 */
	public function use_coupon($user_id, $type_id, $coupon_sn, $channel_module, $channel_oid, $param_info)
	{
		//日志
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'use_coupon', 'coupon');
		
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//检查参数
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		$coupon_sn = trim($coupon_sn);
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( !is_array($param_info) ) $param_info = array();
		$module_type = trim($param_info['module_type']);
		$order_total_amount = number_format($param_info['order_total_amount']*1, 2, '.', '')*1;
		if( $user_id<1 || $type_id<1 || strlen($coupon_sn)<1 || strlen($channel_module)<1 || $channel_oid<1 || strlen($module_type)<1 || $order_total_amount<=0 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$param_info['channel_module'] = $channel_module;
		$param_info['channel_oid'] = $channel_oid;
		$cur_time = time();	//当前时间，以下统一使用此时间
		
		//获取优惠券信息
		$coupon_info = $this->get_coupon_info($coupon_sn);
		if( empty($coupon_info) )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		$type_id = intval($coupon_info['type_id']);
		/*
		if( $type_id!=$coupon_info['type_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '此券类型不正确';
			return $result;
		}
		*/
		if( $coupon_info['is_give']==0 )
		{
			$result['result'] = -4;
			$result['message'] = '此券未兑换';
			return $result;
		}
		//检查券是不是发放给此用户了
		$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
		if( empty($ref_user_info) || $ref_user_info['user_id']!=$user_id )
		{
			$result['result'] = -5;
			$result['message'] = '此券已被兑换';
			return $result;
		}
		//检查关联订单，是否有使用了此优惠券
		$ref_order_info = $this->get_ref_order_info_by_sn($coupon_sn);
		if( (empty($ref_order_info) && $coupon_info['is_used']==1) || (!empty($ref_order_info) && $coupon_info['is_used']==0))
		{
			$result['result'] = -6;
			$result['message'] = '此券已使用';
			return $result;
		}
		if( !empty($ref_order_info) || $coupon_info['is_used']==1 )
		{
			$result['result'] = -7;
			$result['message'] = '此券已使用';
			return $result;
		}
		if( $coupon_info['is_cash']==1 )
		{
			$result['result'] = -8;
			$result['message'] = '此券已使用';
			return $result;
		}
		
		//获取批次信息
		$batch_id = intval($coupon_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			$result['result'] = -9;
			$result['message'] = '批次不存在';
			return $result;
		}
		$is_need_cash = intval($batch_info['is_need_cash']);
		$need_cash_rate = trim($batch_info['need_cash_rate']);
		$need_cash_max = trim($batch_info['need_cash_max']);
		
		//判断此用户是否可以使用优惠券，防止刷单
		$check_ret = $this->check_coupon_limit($user_id, $type_id, $param_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $check_ret['message'];
			return $result;
		}
		
		//检查优惠券有效性
		$valid_ret = $this->check_coupon_valid($coupon_info, $cur_time);
		if( $valid_ret['result']!=1 )
		{
			$result['result'] = -10;
			$result['message'] = $valid_ret['message'];
			return $result;
		}
		
		//检查优惠券适应范围
		$scope_ret = $this->check_coupon_scope($batch_info, $param_info, $cur_time);
		if( $scope_ret['result']!=1 )
		{
			$result['result'] = -11;
			$result['message'] = $scope_ret['message'];
			$result['message_white_false'] = $scope_ret['message_white_false'];
			$result['message_black_true'] = $scope_ret['message_black_true'];
			return $result;
		}
		
		//暂时限制：一个订单只能使用一张优惠券
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( !empty($ref_order_list) )
		{
			$result['result'] = -12;
			$result['message'] = '此单已使用了券';
			return $result;
		}
		
		//计算使用金额
		$used_amount = $this->cal_used_amount($coupon_info, $param_info);
		if( $used_amount<=0 )
		{
			$result['result'] = -13;
			$result['message'] = '此券不适用';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//设置已使用
		$ret = $this->update_coupon_used($coupon_sn, array('used_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -14;
			$result['message'] = '使用失败';
			return $result;
		}
		
		//设置已使用
		$ret = $this->update_ref_user_used($coupon_sn, array('used_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -15;
			$result['message'] = '使用失败';
			return $result;
		}
		
		//保存关联订单
		$ref_data = array(
			'channel_module' => $channel_module,
			'channel_oid' => $channel_oid,
			'user_id' => $user_id,
			'coupon_sn' => $coupon_sn,
			'batch_id' => $batch_id,
			'is_need_cash' => $is_need_cash,
			'need_cash_rate' => $need_cash_rate,
			'need_cash_max' => $need_cash_max,
			'type_id' => $type_id,
			'face_value' => $coupon_info['face_value'],
			'face_max' => $coupon_info['face_max'],
			'used_amount' => $used_amount,
			'add_time' => $cur_time,
		);
		$id = $this->add_ref_order($ref_data);
		if( $id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -16;
			$result['message'] = '使用失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * 不使用优惠券
	 * 调用此方法之前，需要先确定订单未支付
	 * 若订单已支付，请调用refund_coupon
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	public function not_use_coupon_by_oid($channel_module, $channel_oid)
	{
		//日志
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'not_use_coupon_by_oid', 'coupon');
		
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//检查参数
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( empty($ref_order_list) )
		{
			$result['result'] = 1;
			$result['message'] = '此单没有使用券';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//循环处理
		$used_amount = 0;
		foreach ($ref_order_list as $ref_order_info)
		{
			$not_use_ret = $this->not_use_coupon($ref_order_info['id']);
			if( $not_use_ret['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -3;
				$result['message'] = $not_use_ret['message'];
				return $result;
			}
			$used_amount += ($not_use_ret['used_amount']*1);
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['used_amount'] = number_format($used_amount, 2, '.', '');
		return $result;
	}
	
	/**
	 * 不使用优惠券
	 * 调用此方法之前，需要先确定订单未支付
	 * 若订单已支付，请调用refund_coupon
	 * @param int $id
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	public function not_use_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);
		
		//检查参数
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '不存在';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = '无法不使用券';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//设置未使用
		$ret = $this->update_coupon_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '失败';
			return $result;
		}
		
		//设置未使用
		$ret = $this->update_ref_user_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '失败';
			return $result;
		}
		
		//获取订单关联
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '失败';
			return $result;
		}
		
		//删除订单关联
		$ret = $this->del_ref_order($id);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -7;
			$result['message'] = '失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['used_amount'] = $ref_order_info['used_amount'];
		return $result;
	}
	
	/**
	 * 退还优惠券
	 * @param string $channel_module
	 * @param int $channel_oid
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_coupon_by_oid($channel_module, $channel_oid)
	{
		//日志
		$log_arr = array(
			'func_get_args' => func_get_args(),
		);
		pai_log_class::add_log($log_arr, 'refund_coupon_by_oid', 'coupon');
		
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( empty($ref_order_list) )
		{
			$result['result'] = 1;
			$result['message'] = '此单没有使用券';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//循环处理
		foreach ($ref_order_list as $ref_order_info)
		{
			$refund_ret = $this->refund_coupon($ref_order_info['id']);
			if( $refund_ret['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -3;
				$result['message'] = $refund_ret['message'];
				return $result;
			}
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 退还优惠券
	 * @param int $id
	 * @return array
	 */
	public function refund_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '不存在';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = '券无法退还';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time(); 
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//设置未使用
		$ret = $this->update_coupon_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '失败';
			return $result;
		}
		
		//设置未使用
		$ret = $this->update_ref_user_not_used($coupon_sn, array('used_time'=>0));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '失败';
			return $result;
		}
		
		//设置已退还
		$ret = $this->update_ref_order_refund($id, array('refund_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 兑现优惠券
	 * @param int $id
	 * @param double $cash_amount
	 * @param array $more_info array('event_id'=>0, 'in_user_id'=>0, 'org_user_id'=>0, 'need_amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'')
	 * @return array
	 */
	public function cash_coupon($id, $cash_amount, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$id = intval($id);
		$cash_amount = number_format($cash_amount*1, 2, '.', '')*1;
		$event_id = intval($more_info['event_id']);
		$in_user_id = intval($more_info['in_user_id']);
		$org_user_id = intval($more_info['org_user_id']);
		$need_amount = number_format($more_info['need_amount']*1, 2, '.', '')*1;
		$org_amount = number_format($more_info['org_amount']*1, 2, '.', '')*1;
		$in_amount = bcsub($cash_amount, $org_amount, 2);	//收入者兑现金额，兑现金额 减去 机构兑现金额
		$subject = trim($more_info['subject']);
		if( $id<1 || $cash_amount<0 || $need_amount<0 || $org_amount<0 || $in_amount<0 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '不存在';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = '券无法兑现';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//设置已兑现
		$ret = $this->update_coupon_cash($coupon_sn, array('cash_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = '失败';
			return $result;
		}
		
		//设置已兑现
		$data = array(
			'subject' => $subject,
			'event_id' => $event_id,
			'in_user_id' => $in_user_id,
			'org_user_id' => $org_user_id,
			'need_amount' => $need_amount,
			'in_amount' => $in_amount,
			'org_amount' => $org_amount,
			'cash_time' => $cur_time,
		);
		$ret = $this->update_ref_order_cash($id, $cash_amount, $data);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 结算优惠券
	 * @param int $id
	 * @return array
	 */
	public function settle_coupon($id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取订单关联信息
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '不存在';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==0 )
		{
			$result['result'] = -3;
			$result['message'] = '券无法结算';
			return $result;
		}
		$coupon_sn = trim($ref_order_info['coupon_sn']);
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//设置已结算
		$ret = $this->update_coupon_settle($coupon_sn, array('settle_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
				
			$result['result'] = -4;
			$result['message'] = '失败';
			return $result;
		}
		
		//设置已结算
		$ret = $this->update_ref_order_settle($id, array('settle_time'=>$cur_time));
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}
	
	/**
	 * 获取优惠券详情
	 * @param string $coupon_sn
	 * @param int $user_id 检查优惠券是否属于此用户
	 * @return array
	 */
	public function get_coupon_detail_by_sn($coupon_sn, $user_id=0)
	{
		$coupon_sn = trim($coupon_sn);
		$user_id = intval($user_id);
		if( strlen($coupon_sn)<1 )
		{
			return array();
		}
		
		//检查优惠券是否属于此用户
		if( $user_id>0 )
		{
			$ref_user_info = $this->get_ref_user_info_by_sn($coupon_sn);
			if( empty($ref_user_info) || $user_id!=$ref_user_info['user_id'])
			{
				return array();
			}
		}
		
		//获取优惠券信息
		$detail_list = $this->fill_coupon_detail_list(array(array('coupon_sn'=>$coupon_sn)));
		if( empty($detail_list) )
		{
			return array();
		}
		$coupon_info = $detail_list[0];
		if( !is_array($coupon_info) ) $coupon_info = array();
		return $coupon_info;
	}
	
	/**
	 * 获取用户的优惠券列表，我的优惠券页面
	 * @param string $tab 可使用available 已使用used 已过期expired
	 * @param int $user_id
	 * @param boolean $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|int
	 */
	public function get_user_coupon_list_by_tab($tab, $user_id, $b_select_count=false, $order_by='coupon_id ASC', $limit='0,20')
	{
		$tab = trim($tab);
		$user_id = intval($user_id);
		if( !in_array($tab, array('available', 'used', 'expired')) || $user_id<1 )
		{
			return $b_select_count?0:array();
		}
		
		$cur_time = time();
		
		//整理条件
		$where_str = '';
		if( $tab=='available' )
		{
			$where_str = "is_used=0 AND {$cur_time}<=end_time";
		}
		elseif( $tab=='used' )
		{
			$where_str = "is_used=1";
		}
		elseif( $tab=='expired' )
		{
			$where_str = "is_used=0 AND end_time<{$cur_time}";
		}
		
		//获取关联用户列表
		$ref_user_list = $this->get_ref_user_list($user_id, 0, $b_select_count, $where_str, 'id ASC', '0,99999999', 'coupon_sn');
		if( $b_select_count )
		{
			return $ref_user_list;
		}
		
		//查询优惠券列表
		$coupon_list = $this->fill_coupon_detail_list($ref_user_list, $order_by, $limit);
		return $coupon_list;
	}
	
	/**
	 * 获取用户的可用优惠券，订单支付页面
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @param boolean $b_select_count
	 * @param string $order_by
	 * @param bool $limit_coupon 是否限制优惠券
	 * @param string $limit_message 限制优惠券的提示内容
	 * @return array|int
	 * @tutorial
	 * 
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //订单类型
	 *  'channel_oid'=>0, //订单ID
	 *  'module_type'=>'yuepai', //模块类型 waipai yuepai topic
	 *  'order_total_amount'=>'100.00', //订单总金额
	 *  'model_user_id'=>0, //模特用户ID（约拍、专题）
	 *  'org_user_id'=>0, //机构用户ID
	 *  'event_id'=>'', //活动ID
	 * );
	 * 
	 */
	public function get_user_coupon_list_by_check($user_id, $type_id, $param_info, $b_select_count=false, $order_by='coupon_id ASC', &$limit_coupon=false, &$limit_message='')
	{
		$user_id = intval($user_id);
		$type_id = intval($type_id);
		if( $user_id<1 || $type_id<1 )
		{
			return $b_select_count?0:array();
		}
		
		$cur_time = time();
		
		//判断此用户是否可以使用优惠券，防止刷单
		$limit_coupon = false;
		$limit_message = '';
		$check_ret = $this->check_coupon_limit($user_id, $type_id, $param_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$limit_coupon = true;
			$limit_message = trim($check_ret['message']);
			return $b_select_count?0:array();
		}
		
		//获取此单正占用的优惠券
		$coupon_sn_arr = array();
		$coupon_sn_str = '';
		$coupon_sn_sp = '';
		$ref_order_list = $this->get_ref_order_list_by_oid($param_info['channel_module'], $param_info['channel_oid']);
		foreach ($ref_order_list as $ref_order_info)
		{
			$coupon_sn_arr[] = trim($ref_order_info['coupon_sn']);
			$coupon_sn_str .= $coupon_sn_sp . "'" . mysql_escape_string($ref_order_info['coupon_sn']) . "'";
			$coupon_sn_sp = ',';
		}
		
		//获取关联用户列表
		$where_str = "start_time<={$cur_time} AND {$cur_time}<=end_time";
		if( strlen($coupon_sn_str)>0 )
		{
			$where_str .= " AND (is_used=0 OR coupon_sn IN ({$coupon_sn_str}))";
		}
		else
		{
			$where_str .= " AND is_used=0";
		}
		$ref_user_list = $this->get_ref_user_list($user_id, 0, false, $where_str, 'id ASC', '0,99999999', 'coupon_sn');
		
		//查询优惠券列表
		$coupon_list = $this->fill_coupon_detail_list($ref_user_list, $order_by, '0,99999999');
		
		//检查可用优惠券
		$result_list = array();
		$result_count = 0;
		foreach ($coupon_list as $coupon_info)
		{
			//检查是否适用
			$check_ret = $this->check_coupon($user_id, $type_id, $coupon_info['coupon_sn'], $param_info);
			if( $check_ret['result']!=1 )
			{
				continue;
			}
			
			//重置优惠券使用状态
			$coupon_sn_tmp = trim($coupon_info['coupon_sn']);
			if( in_array($coupon_sn_tmp, $coupon_sn_arr, true) )
			{
				$coupon_info['is_used'] = 0;
				$coupon_info['tab'] = 'available';
				$coupon_info['tab_str'] = '可使用';
			}
			
			$result_list[] = $coupon_info;
			$result_count++;
		}
		if($b_select_count)
		{
			return $result_count;
		}
		$result_list = array_slice($result_list, 0, 100); //仅输出100张，若数量太多，前端页面会卡住
		return $result_list;
	}
	
	/**
	 * 填充优惠列表
	 * @param array $list
	 * @param string $order_by
	 * @param string $limit
	 * @param bool $b_detail
	 * @return array
	 */
	private function fill_coupon_detail_list($list, $order_by='coupon_id ASC', $limit='0,20', $b_detail=true)
	{
		if( !is_array($list) || empty($list) )
		{
			return array();
		}
		//整理
		$coupon_sn_str = '';
		$coupon_sn_sp = '';
		foreach ($list as $info)
		{
			$coupon_sn_tmp = trim($info['coupon_sn']);
			if( strlen($coupon_sn_tmp)>1 )
			{
				$tmp = ':x_coupon_sn';
				sqlSetParam($tmp, "x_coupon_sn", $coupon_sn_tmp);
				$coupon_sn_str .= $coupon_sn_sp . $tmp;
				$coupon_sn_sp = ',';
			}
		}
		if( strlen($coupon_sn_str)<1 )
		{
			return array();
		}
		$coupon_list = $this->get_coupon_list(0, false, "coupon_sn IN ({$coupon_sn_str})", $order_by, $limit);
		
		//获取批次信息
		if( $b_detail )
		{
			$cur_time = time();
			
			foreach ($coupon_list as $coupon_key=>$coupon_info)
			{
				$batch_id = intval($coupon_info['batch_id']);
				$batch_info = $this->get_batch_info($batch_id);
				if( empty($batch_info) )
				{
					continue;
				}
				
				$start_time = intval($coupon_info['start_time']);
				$start_time_str = date('Y.m.d', $start_time);
				$coupon_info['start_time_str'] = $start_time_str;
				
				$end_time = intval($coupon_info['end_time']);
				$end_time_str = date('Y.m.d', $end_time);
				$coupon_info['end_time_str'] = $end_time_str;

				$used_time = intval($coupon_info['used_time']);
				$used_time_str 	= date('Y.m.d', $used_time);
				$coupon_info['used_time_str'] = $used_time_str;				

				if( $coupon_info['is_used']==0 )
				{
					if( $cur_time<=$end_time )
					{
						$coupon_info['tab'] = 'available';
						$coupon_info['tab_str'] = '可使用';
					}
					else
					{
						$coupon_info['tab'] = 'expired';
						$coupon_info['tab_str'] = '已过期';
					}
				}
				else
				{
					$coupon_info['tab'] = 'used';
					$coupon_info['tab_str'] = '已使用';
				}
				
				$coupon_info['batch_name'] = trim($batch_info['batch_name']);
				$coupon_info['batch_desc'] = trim($batch_info['batch_desc']);
				$coupon_info['scope_module_type'] = trim($batch_info['scope_module_type']);
				$coupon_info['scope_module_type_name'] = trim($batch_info['scope_module_type_name']);
				$coupon_info['scope_order_total_amount'] = trim($batch_info['scope_order_total_amount']);
				
				$coupon_list[$coupon_key] = $coupon_info;
			}
		}
		
		return $coupon_list;
	}

	/**
	 * 获取领取详情
	 * @param int $supply_id
	 * @return array
	 */
	public function get_supply_detail($supply_id)
	{
		$supply_id = intval($supply_id);
		if( $supply_id<1 )
		{
			return array();
		}
		
		//获取领取信息
		$supply_info = $this->get_supply_info($supply_id);
		if( empty($supply_info) )
		{
			return array();
		}
		
		//获取批次信息
		$batch_id = intval($supply_info['batch_id']);
		$batch_info = $this->get_batch_info($batch_id);
		if( empty($batch_info) )
		{
			return array();
		}
		
		//整理成优惠券字段，方便页面使用
		$supply_info['batch_name'] = trim($batch_info['batch_name']);
		$supply_info['batch_desc'] = trim($batch_info['batch_desc']);
		$supply_info['type_id'] = trim($batch_info['coupon_type_id']);
		$supply_info['face_value'] = trim($batch_info['coupon_face_value']);
		$supply_info['start_time'] = trim($batch_info['coupon_start_time']);
		$supply_info['end_time'] = trim($batch_info['coupon_end_time']);
		
		$start_time = intval($supply_info['start_time']);
		$start_time_str = date('Y.m.d', $start_time);
		$supply_info['start_time_str'] = $start_time_str;
		
		$end_time = intval($supply_info['end_time']);
		$end_time_str = date('Y.m.d', $end_time);
		$supply_info['end_time_str'] = $end_time_str;
		
		$supply_info['scope_module_type'] = trim($batch_info['scope_module_type']);
		$supply_info['scope_module_type_name'] = trim($batch_info['scope_module_type_name']);;
		$supply_info['scope_order_total_amount'] = trim($batch_info['scope_order_total_amount']);
		
		return $supply_info;
	}
	
	/**
	 * 领取券
	 * @param int $supply_id
	 * @param int $user_id
	 * @return array
	 */
	public function give_supply_coupon($supply_id, $user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$supply_id = intval($supply_id);
		$user_id = intval($user_id);
		if( $supply_id<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$cur_time = time();
		
		//获取领取信息
		$supply_info = $this->get_supply_info($supply_id);
		if( empty($supply_info) )
		{
			$result['result'] = -2;
			$result['message'] = '参数错误';
			return $result;
		}
		$supply_begin_time = intval($supply_info['supply_begin_time']);
		$supply_end_time = intval($supply_info['supply_end_time']);
		$supply_status = intval($supply_info['supply_status']);
		if($cur_time<$supply_begin_time)
		{
			$result['result'] = -3;
			$result['message'] = '未开始';
			return $result;
		}
		if($supply_end_time<$cur_time)
		{
			$result['result'] = -4;
			$result['message'] = '已结束';
			return $result;
		}
		if( $supply_status!=1 )
		{
			$result['result'] = -5;
			$result['message'] = '已暂停';
			return $result;
		}
		
		//检查是否已领取
		$supply_user_info = $this->get_supply_user_info($supply_id, $user_id);
		if( empty($supply_user_info) )
		{
			$result['result'] = -6;
			$result['message'] = '已结束';
			return $result;
		}
		if( $supply_user_info['is_give']==1 )
		{
			$result['result'] = -7;
			$result['message'] = '已领取';
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$batch_id = intval($supply_info['batch_id']);
		$ret = $this->give_coupon_by_create($user_id, $batch_id);
		if( $ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -8;
			$result['message'] = '已抢光';
			return $result;
		}
		$coupon_sn = trim($ret['coupon_sn']);
		
		$id = intval($supply_user_info['id']);
		$more_info = array('coupon_sn'=>$coupon_sn, 'give_time'=>$cur_time);
		$ret = $this->update_supply_user_give($id, $more_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = '领取错误';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn;
		return $result;
	}
	
	/**
	 * 获取用户的最好可用优惠券，支付页面默认选中优惠券
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @return array
	 */
	public function get_user_coupon_info_best($user_id, $type_id, $param_info)
	{
		$list = $this->get_user_coupon_list_by_check($user_id, $type_id, $param_info, false, 'face_value DESC,end_time ASC,coupon_id DESC');
		if( empty($list) || !is_array($list[0]) || empty($list[0]) )
		{
			return array();
		}
		return $list[0];
	}
	
	/**
	 * 获取用户的可用优惠券共多少元，约拍价格界面
	 * @param int $user_id
	 * @param int $type_id
	 * @param array $param_info
	 * @return double
	 * @tutorial
	 *
	 * $param_info = array(
	 *  'channel_module'=>'yuepai', //订单类型
	 *  'channel_oid'=>0, //订单ID
	 *  'module_type'=>'yuepai', //模块类型 waipai yuepai
	 *  'order_total_amount'=>'100.00', //订单总金额
	 *  'model_user_id'=>0, //模特用户ID（约拍、专题）
	 *  'org_user_id'=>0, //机构用户ID
	 *  'event_id'=>'', //活动ID
	 * );
	 * 
	 */
	public function get_user_coupon_total_face_value($user_id, $type_id, $param_info)
	{
		$total_face_value = 0;
		$list = $this->get_user_coupon_list_by_check($user_id, $type_id, $param_info, false, 'coupon_id ASC');
		foreach ($list as $info)
		{
			$total_face_value = $total_face_value + $info['face_value']*1;
		}
		return $total_face_value;
	}
	
	/**
	 * 获取发放统计
	 * @param int $begin_time
	 * @param int $end_time
	 * @param int $batch_id
	 * @return array
	 */
	public function get_stat_give_list($begin_time, $end_time, $batch_id=0)
	{
		$begin_time = intval($begin_time);
		$end_time = intval($end_time);
		$batch_id = intval($batch_id);
		if( $begin_time<1 || $end_time<1 || $begin_time>$end_time )
		{
			return array();
		}
		
		$where_str = "add_time>={$begin_time} AND add_time<={$end_time}";
		if( $batch_id>0 )
		{
			$where_str .= " AND batch_id={$batch_id}";
		}
		
		$this->set_coupon_ref_user_tbl();
		$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(add_time),'%Y-%m-%d') AS add_date,batch_id,count(batch_id) AS quantity FROM {$this->_db_name}.{$this->_tbl_name}";
		$sql .= " WHERE {$where_str} GROUP BY add_date,batch_id ORDER BY add_date DESC,batch_id ASC";
		return $this->findBySql($sql);
	}
	
	/**
	 * 获取兑现统计
	 * @param string $channel_module
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_stat_cash_list($channel_module, $begin_time, $end_time)
	{
		$channel_module = trim($channel_module);
		$begin_time = intval($begin_time);
		$end_time = intval($end_time);
		if( $begin_time<1 || $end_time<1 || $begin_time>$end_time )
		{
			return array();
		}
		
		$where_str = "is_cash=1 AND cash_time>={$begin_time} AND cash_time<={$end_time}";
		if( strlen($channel_module)>0 )
		{
			if( strlen($where_str)>0 ) $where_str .= ' AND ';
			$where_str .= "channel_module=:x_channel_module";
			sqlSetParam($where_str, 'x_channel_module', $channel_module);
		}
		
		$this->set_coupon_ref_order_tbl();
		$sql = "SELECT DATE_FORMAT(FROM_UNIXTIME(cash_time),'%Y-%m-%d') AS cash_date,COUNT(DISTINCT user_id) AS uv,COUNT(id) AS pv,SUM(used_amount) AS used_amount,SUM(cash_amount) AS cash_amount FROM {$this->_db_name}.{$this->_tbl_name}";
		$sql .= " WHERE {$where_str} GROUP BY cash_date ORDER BY cash_date DESC";
		$list = $this->findBySql($sql);
		
		return $list;
	}
	
	/**
	 * 获取优惠券账户
	 * @return array
	 */
	public function get_coupon_account_info()
	{
		$payment_obj = POCO::singleton('pai_payment_class');
		$coupon_user_id = $payment_obj->get_coupon_user_id();
		return $payment_obj->get_user_account_info($coupon_user_id);
	}
	
	/**
	 * 获取优惠券账户统计
	 * @param int $begin_time
	 * @param int $end_time
	 * @return array
	 */
	public function get_stat_coupon_list($begin_time, $end_time)
	{
		$payment_obj = POCO::singleton('pai_payment_class');
		$coupon_user_id = $payment_obj->get_coupon_user_id();
		$account_info = $payment_obj->get_user_account_info($coupon_user_id);
		return $payment_obj->get_stat_account_list('actual', $account_info['account_id'], $begin_time, $end_time);
	}
	
}
