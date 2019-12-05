<?php
/**
 * 优惠券套餐
 * @author Henry
 * @copyright 2015-05-09
 */

class pai_coupon_package_class extends POCO_TDG
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
	private function set_coupon_package_tbl()
	{
		$this->setTableName('coupon_package_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_package_ref_batch_tbl()
	{
		$this->setTableName('coupon_package_ref_batch_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_package_cate_tbl()
	{
		$this->setTableName('coupon_package_cate_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_exchange_tbl()
	{
		$this->setTableName('coupon_exchange_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_coupon_exchange_ref_coupon_tbl()
	{
		$this->setTableName('coupon_exchange_ref_coupon_tbl');
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_package($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_package_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public function update_package($data, $package_id)
	{
		$package_id = intval($package_id);
		if( !is_array($data) || empty($data) || $package_id<1 )
		{
			return false;
		}
		$this->set_coupon_package_tbl();
		$affected_rows = $this->update($data, "package_id={$package_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 累积实际已使用数
	 * @param string $where_str
	 * @param int $number
	 * @return boolean
	 */
	private function margin_package_real_number($package_id, $number=1)
	{
		$package_id = intval($package_id);
		$number = intval($number);
		if( $package_id<1 || $number<1 )
		{
			return false;
		}
		$this->set_coupon_package_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_number=real_number+{$number} WHERE package_id={$package_id} AND (plan_number-real_number)>={$number}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param string $package_sn
	 * @return array
	 */
	public function get_package_info($package_sn)
	{
		$package_sn = trim($package_sn);
		if( strlen($package_sn)<1 )
		{
			return array();
		}
		$where_str = 'package_sn=:x_package_sn';
		sqlSetParam($where_str, 'x_package_sn', $package_sn);
		$this->set_coupon_package_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取信息
	 * @param int $package_id
	 * @return array
	 */
	public function get_package_info_by_id($package_id)
	{
		$package_id = intval($package_id);
		if( $package_id<1 )
		{
			return array();
		}
		$this->set_coupon_package_tbl();
		return $this->find("package_id={$package_id}");
	}

	/**
	 * 获取信息
	 * @param string $coupon_sn
	 * @return array
	 */
	public function get_package_info_by_coupon_sn($coupon_sn)
	{
        $coupon_sn = trim($coupon_sn);
        if( strlen($coupon_sn)<1 )
        {
            return array();
        }
		$where_str = 'coupon_sn=:x_coupon_sn';
		sqlSetParam($where_str, 'x_coupon_sn', $coupon_sn);

        $this->set_coupon_exchange_ref_coupon_tbl();
        $package_sn_info = $this->find($where_str, null, 'package_sn');
        if( empty($package_sn_info) )
        {
            return array();
        }
        $package_sn = trim($package_sn_info['package_sn']);
        return $this->get_package_info($package_sn);
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
	public function get_package_list($cate_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
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
		$this->set_coupon_package_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return bool
	 */
	private function add_ref_batch($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return false;
		}
		$this->set_coupon_package_ref_batch_tbl();
		$this->insert($data);
		return true;
	}
	
	/**
	 * 保存
	 * @param int $package_id
	 * @param int $batch_id
	 * @param int $quantity
	 * @param int $coupon_days
	 * @return boolean
	 */
	public function save_ref_batch($package_id, $batch_id, $quantity, $coupon_days)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		$quantity = intval($quantity);
		$coupon_days = intval($coupon_days);
		if( $package_id<1 || $batch_id<1 || $quantity<1 || $coupon_days<0 )
		{
			return false;
		}
		$data = array(
			'package_id' => $package_id,
			'batch_id' => $batch_id,
			'quantity' => $quantity,
			'coupon_days' => $coupon_days,
		);
		$this->set_coupon_package_ref_batch_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}
	
	/**
	 * 删除
	 * @param int $package_id
	 * @param int $batch_id
	 * @return boolean
	 */
	public function del_ref_batch($package_id, $batch_id=0)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		if( $package_id<1)
		{
			return false;
		}
		$where_str = "package_id='{$package_id}'";
		if( $batch_id>0 )
		{
			$where_str .= " AND batch_id='{$batch_id}'";
		}
		$this->set_coupon_package_ref_batch_tbl();
		$this->delete($where_str);
		return true;
	}
	
	/**
	 * 获取
	 * @param int $package_id
	 * @param int $batch_id
	 * @return array
	 */
	public function get_ref_batch_info($package_id, $batch_id)
	{
		$package_id = intval($package_id);
		$batch_id = intval($batch_id);
		if( $package_id<1 || $batch_id<1 )
		{
			return array();
		}
		$where_str = "package_id='{$package_id}' AND batch_id='{$batch_id}'";
		$this->set_coupon_package_ref_batch_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取列表
	 * @param int $package_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_batch_list($package_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$package_id = intval($package_id);
		if( $package_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		
		//整理查询条件
		$sql_where = "package_id={$package_id}";
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_coupon_package_ref_batch_tbl();
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
	public function add_cate($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_package_cate_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 修改
	 * @param array $data
	 * @param int $cate_id
	 * @return boolean
	 */
	public function update_cate($data, $cate_id)
	{
		//检查参数
		$cate_id = intval($cate_id);
		if( !is_array($data) || empty($data) || $cate_id<1 )
		{
			return false;
		}
		//保存
		$this->set_coupon_package_cate_tbl();
		$affected_rows = $this->update($data, "cate_id={$cate_id}");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * 获取信息
	 * @param int $cate_id
	 * @return array
	 */
	public function get_cate_info($cate_id)
	{
		$cate_id = intval($cate_id);
		if( $cate_id<1 )
		{
			return array();
		}
		$this->set_coupon_package_cate_tbl();
		return $this->find("cate_id={$cate_id}");
	}
	
	/**
	 * 获取分类列表
	 * @param int $parent_id -1表示不限制
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_cate_list($parent_id=-1, $b_select_count=false, $where_str='', $order_by='sort ASC,cate_id ASC', $limit='0,20', $fields='*')
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
		$this->set_coupon_package_cate_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 获取所有分类
	 * @param int $parent_id
	 * @param int $exclude_cate_id
	 * @return array
	 */
	public function get_cate_list_all($parent_id=0, $exclude_cate_id=0)
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
		$cate_list = $this->get_cate_list($parent_id, false, "cate_id<>{$exclude_cate_id}", 'sort ASC,cate_id ASC', '0,99999999');
		foreach($cate_list as $cate_info)
		{
			$cate_id = intval($cate_info['cate_id']);
			$child_list = $this->get_cate_list_all($cate_id, $exclude_cate_id);
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
	private function add_exchange($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_exchange_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * 获取信息
	 * @param int $user_id
	 * @param string $package_sn
	 * @return array
	 */
	public function get_exchange_info($user_id, $package_sn)
	{
		$user_id = intval($user_id);
		$package_sn = trim($package_sn);
		if( $user_id<1 || strlen($package_sn)<1 )
		{
			return array();
		}
		$where_str = "user_id={$user_id} AND package_sn=:x_package_sn";
		sqlSetParam($where_str, 'x_package_sn', $package_sn);
		$this->set_coupon_exchange_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 获取信息
	 * @param int $user_id
	 * @param int $cate_id
	 * @return array
	 */
	public function get_exchange_info_by_cate_id($user_id, $cate_id)
	{
		$user_id = intval($user_id);
		$cate_id = intval($cate_id);
		if( $user_id<1 || $cate_id<1 )
		{
			return array();
		}
		$where_str = "user_id={$user_id} AND cate_id={$cate_id}";
		$this->set_coupon_exchange_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_ref_coupon($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_coupon_exchange_ref_coupon_tbl();
		return $this->insert($data, 'IGNORE');
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
		//第一位规定是9
		$code = '9';
		$length--;
		$pattern = '2935714068';
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern, mt_rand(0,9), 1);
		}
		return $code;
	}
	
	/**
	 * 生成套餐券
	 * @param int $cate_id 分类ID
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array('batch_id'=>0, 'quantity'=>0)
	 * @param array $more_info array('scope_user_divide'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'package_sn'=>'')
	 */
	public function create_package($cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'package_sn'=>'');
		
		//检查参数
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		if( $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 || empty($batch_list) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$scope_user_divide = trim($more_info['scope_user_divide']);
		$package_title = trim($more_info['package_title']);
		$package_remark = trim($more_info['package_remark']);
		
		//整理关联批次
		$batch_data_list = array();
		foreach($batch_list as $batch_info)
		{
			$batch_id_tmp = intval($batch_info['batch_id']);
			$quantity_tmp = intval($batch_info['quantity']);
			if( $batch_id_tmp<1 || $quantity_tmp<1 )
			{
				$result['result'] = -2;
				$result['message'] = '关联批次错误';
				return $result;
			}
			$batch_data_list[] = array(
				'batch_id' => $batch_id_tmp,
				'quantity' => $quantity_tmp,
			);
		}
		
		$package_id = 0;
		$package_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			$rand_str = $this->get_rand_str(6);
			if( strlen($rand_str)<1 )
			{
				//套餐券码为空
				break;
			}
			$tmp = $this->get_package_info($rand_str);
			if( !empty($tmp) )
			{
				//套餐券码已存在
				$while_count++;
				continue;
			}
			$package_data = array(
				'package_sn' => $rand_str,
				'cate_id' => $cate_id,
				'start_time' => $start_time,
				'end_time' => $end_time,
				'plan_number' => $plan_number,
				'scope_user_divide' => $scope_user_divide,
				'package_title' => $package_title,
				'package_remark' => $package_remark,
				'add_time' => time(),
			);
			$id = $this->add_package($package_data);
			if( $id<1 )
			{
				//可能有另一个请求也在生成套餐券
				$while_count++;
				continue;
			}
			$package_id = $id;
			$package_sn = $rand_str;
			break;
		}
		if( $package_id<1 || strlen($package_sn)<1 )
		{
			$result['result'] = -3;
			$result['message'] = '生成失败';
			return $result;
		}
		
		//保存关联批次
		foreach($batch_data_list as $batch_data_info)
		{
			$batch_data_info['package_id'] = $package_id;
			$this->add_ref_batch($batch_data_info);
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['package_sn'] = $package_sn;
		return $result;
	}
	
	/**
	 * 生成多个套餐券 
	 * @param int $cate_id 分类ID
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array('batch_id'=>0, 'quantity'=>0)
	 * @param int $quantity
	 * @param array $more_info array('scope_user_divide'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'quantity'=>0)
	 */
	public function create_package_multiple($cate_id, $start_time, $end_time, $plan_number, $batch_list, $quantity, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'quantity'=>0);
		
		//检查参数
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		$quantity = intval($quantity);
		if( $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 || empty($batch_list) || $quantity<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		
		//生成优惠券
		$create_quantity = 0; //实际生成数量
		for($i=1; $i<=$quantity; $i++)
		{
			$create_ret = $this->create_package($cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info);
			if( $create_ret['result']!=1 )
			{
				break;
			}
			$create_quantity++;
		}
		if( $create_quantity<1 )
		{
			$result['result'] = -2;
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
	 * 提交兑换码
	 * @param string $package_sn
	 * @param int $cate_id
	 * @param int $start_time
	 * @param int $end_time
	 * @param int $plan_number
	 * @param array $batch_list array( array('batch_id'=>0, 'quantity'=>0, 'coupon_days'=>0), ... )
	 * @param array $more_info array('scope_user_divide'=>'', 'msg_url'=>'', 'msg_content'=>'', 'package_title'=>'', 'package_remark'=>'')
	 * @return array array('result'=>0, 'message'=>'', 'package_id'=>0)
	 */
	public function submit_package($package_sn, $cate_id, $start_time, $end_time, $plan_number, $batch_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'package_id'=>0);
		
		//检查参数
		$package_sn = trim($package_sn);
		$cate_id = intval($cate_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);
		$plan_number = intval($plan_number);
		if( !is_array($batch_list) ) $batch_list = array();
		if( strlen($package_sn)<1 || $cate_id<1 || $start_time<1 || $end_time<1 || $start_time>$end_time || $plan_number<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$scope_user_divide = trim($more_info['scope_user_divide']);
		$msg_url = trim($more_info['msg_url']);
		$msg_content = trim($more_info['msg_content']);
		$package_title = trim($more_info['package_title']);
		$package_remark = trim($more_info['package_remark']);
		
		//整理关联批次
		$batch_data_list = array();
		foreach($batch_list as $batch_info)
		{
			$batch_id_tmp = intval($batch_info['batch_id']);
			$quantity_tmp = intval($batch_info['quantity']);
			$coupon_days_tmp = intval($batch_info['coupon_days']);
			if( $batch_id_tmp<1 || $quantity_tmp<1 )
			{
				$result['result'] = -2;
				$result['message'] = '关联批次错误';
				return $result;
			}
			$batch_data_list[] = array(
				'batch_id' => $batch_id_tmp,
				'quantity' => $quantity_tmp,
				'coupon_days' => $coupon_days_tmp,
			);
		}
		
		//检查兑换码是否已存在
		$tmp = $this->get_package_info($package_sn);
		if( !empty($tmp) )
		{
			$result['result'] = -3;
			$result['message'] = '兑换码已存在';
			return $result;
		}
		
		//保存
		$package_data = array(
			'package_sn' => $package_sn,
			'cate_id' => $cate_id,
			'start_time' => $start_time,
			'end_time' => $end_time,
			'plan_number' => $plan_number,
			'scope_user_divide' => $scope_user_divide,
			'msg_url' => $msg_url,
			'msg_content' => $msg_content,
			'package_title' => $package_title,
			'package_remark' => $package_remark,
			'add_time' => time(),
		);
		$package_id = $this->add_package($package_data);
		if( $package_id<1 )
		{
			$result['result'] = -4;
			$result['message'] = '保存失败';
			return $result;
		}
		
		//保存关联批次
		foreach($batch_data_list as $batch_data_info)
		{
			$batch_data_info['package_id'] = $package_id;
			$this->add_ref_batch($batch_data_info);
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['package_id'] = $package_id;
		return $result;
	}
	
	/**
	 * 检查套餐券的有效性
	 * @param array $coupon_info
	 * @param int $cur_time 当前时间
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function check_package_valid($package_info, $cur_time=0)
	{
		$result = array('result'=>0, 'message'=>'');
	
		if( !is_array($package_info) || empty($package_info) )
		{
			$result['result'] = -1;
			$result['message'] = '此券不存在';
			return $result;
		}
		$cur_time = intval($cur_time);
		if( $cur_time<1 ) $cur_time = time();
	
		$package_sn = trim($package_info['package_sn']);
		if( strlen($package_sn)<1 )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		
		$start_time = intval($package_info['start_time']);
		if( $start_time>$cur_time )
		{
			$result['result'] = -3;
			$result['message'] = '此券未到有效期';
			return $result;
		}
		
		$end_time = intval($package_info['end_time']);
		if( $end_time<$cur_time )
		{
			$result['result'] = -4;
			$result['message'] = '此券已过有效期';
			return $result;
		}
		
		$plan_number = intval($package_info['plan_number']);
		$real_number = intval($package_info['real_number']);
		if( $plan_number<=$real_number )
		{
			$result['result'] = -5;
			$result['message'] = '此券已失效';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '有效';
		return $result;
	}
	
	/**
	 * 兑换套餐券
	 * @param int $user_id
	 * @param string $package_sn
	 * @return string array('result'=>0, 'message'=>'', 'coupon_sn'=>'')
	 */
	public function exchange_package($user_id, $package_sn)
	{
		$result = array('result'=>0, 'message'=>'', 'coupon_sn'=>'');
		
		$user_id = intval($user_id);
		$package_sn = trim($package_sn);
		if( $user_id<1 || strlen($package_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//获取
		$package_info = $this->get_package_info($package_sn);
		if( empty($package_info) )
		{
			$result['result'] = -2;
			$result['message'] = '此券不存在';
			return $result;
		}
		$package_id = intval($package_info['package_id']);
		$cate_id = intval($package_info['cate_id']);
		
		//检查是否兑换过
		$exchange_info = $this->get_exchange_info($user_id, $package_sn);
		if( !empty($exchange_info) )
		{
			$result['result'] = -3;
			$result['message'] = '此券已领取过';
			return $result;
		}
		$exchange_info = $this->get_exchange_info_by_cate_id($user_id, $cate_id);
		if( !empty($exchange_info) )
		{
			$result['result'] = -3;
			$result['message'] = '此类券已领取过';
			return $result;
		}
		
		$cur_time = time();
		
		//检查新老用户
		$scope_user_divide = trim($package_info['scope_user_divide']);
		if( strlen($scope_user_divide)>0 )
		{
			//计算注册天数
			$user_obj = POCO::singleton('pai_user_class');
			$user_info = $user_obj->get_user_info($user_id);
			$reg_time = intval($user_info['add_time']);
			$reg_time_zero = strtotime(date('Y-m-d 00:00:00', $reg_time));
			$cur_time_zero = strtotime(date('Y-m-d 00:00:00', $cur_time));
			$reg_days = ($cur_time_zero - $reg_time_zero)/86400 + 1; //注册天数，自然天
			$threshold_days = 15; //阈值天数
			
			//判断新老用户
			if( $scope_user_divide=='new' ) //新用户才能兑换
			{
				if( $reg_days>$threshold_days )
				{
					$result['result'] = -4;
					$result['message'] = '优惠仅适用于新用户';
					return $result;
				}
			}
			elseif( $scope_user_divide=='old' ) //老用户才能兑换
			{
				if( $reg_days<=$threshold_days )
				{
					$result['result'] = -4;
					$result['message'] = '优惠仅适用于老用户';
					return $result;
				}
			}
			else //未知
			{
				$result['result'] = -4;
				$result['message'] = '此券配置有误';
				return $result;
			}
		}
		
		//检查有效性
		$check_ret = $this->check_package_valid($package_info, $cur_time);
		if( $check_ret['result']!=1 )
		{
			$result['result'] = -4;
			$result['message'] = $check_ret['message'];
			return $result;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//累积实际使用数量
		$ret = $this->margin_package_real_number($package_id, 1);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '此券已失效';
			return $result;
		}
		
		//保存
		$exchange_data = array(
			'user_id' => $user_id,
			'cate_id' => $cate_id,
			'package_sn' => $package_sn,
			'add_time' => $cur_time,
		);
		$exchange_id = $this->add_exchange($exchange_data);
		if( $exchange_id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -6;
			$result['message'] = '此券已领取过';
			return $result;
		}
		
		//发放优惠券
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$coupon_sn_arr = array(); //兑换的优惠券
		$ref_batch_list = $this->get_ref_batch_list($package_id, false, '', 'batch_id ASC', '');
		foreach($ref_batch_list as $ref_batch_info)
		{
			$batch_id_tmp = intval($ref_batch_info['batch_id']);
			$quantity_tmp = intval($ref_batch_info['quantity']);
			$coupon_days_tmp = intval($ref_batch_info['coupon_days']);
			
			//计算有效天数（自然天），领取之日算1天。
			if( $coupon_days_tmp>0 )
			{
				$start_time_tmp = strtotime( date('Y-m-d 00:00:00', $cur_time) );
				$end_time_tmp = strtotime( date('Y-m-d 23:59:59', $cur_time+($coupon_days_tmp-1)*24*3600) );
				$more_info = array('start_time'=>$start_time_tmp, 'end_time'=>$end_time_tmp);
			}
			else
			{
				$more_info = array();
			}
			
			for($i=1; $i<=$quantity_tmp; $i++)
			{
				$give_ret = $coupon_obj->give_coupon_by_create($user_id, $batch_id_tmp, $more_info);
				if( $give_ret['result']!=1 )
				{
					//事务回滚
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -7;
					$result['message'] = '此券已失效';
					return $result;
				}
				$data = array(
					'exchange_id' => $exchange_id,
					'user_id' => $user_id,
					'cate_id' => $cate_id,
					'package_sn' => $package_sn,
					'batch_id' => $batch_id_tmp,
					'coupon_sn' => $give_ret['coupon_sn'],
					'add_time' => $cur_time,
				);
				$id = $this->add_ref_coupon($data);
				if( $id<1 )
				{
					//事务回滚
					POCO_TRAN::rollback($this->getServerId());
					
					$result['result'] = -8;
					$result['message'] = '兑换失败';
					return $result;
				}
				$coupon_sn_arr[] = $give_ret['coupon_sn'];
			}
		}
		if( empty($coupon_sn_arr) )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -9;
			$result['message'] = '此券已失效';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		//事件触发
		$pai_trigger_obj = POCO::singleton('pai_trigger_class');
		$trigger_params = array('user_id'=>$user_id, 'package_sn'=>$package_sn, 'coupon_sn_arr'=>$coupon_sn_arr);
		$pai_trigger_obj->coupon_exchange_package_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['coupon_sn'] = $coupon_sn_arr[0];
		return $result;
	}
}
