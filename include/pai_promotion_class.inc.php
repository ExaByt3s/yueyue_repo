<?php
/**
 * ������
 * @author bryan
 * @copyright 2015-10-10
 */

class pai_promotion_class extends POCO_TDG
{
	/**
	 * ������Ч�Ĵ����б��������÷�Χ��������SKU��չ��
	 * @var array
	 */
	private $cache_promotion_list_valid_arr = null;

	/**
	 * ������Ч�����б��ʱ��
	 * @var int
	 */
	private $cache_promotion_list_valid_time = 0;

	/**
	 * �������÷�Χ��SKU
	 * @var array
	 */
	private $cache_scope_sku_prices_type_id_arr = array();

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_promotion_db');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_tbl()
	{
		$this->setTableName('promotion_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_cate_tbl()
	{
		$this->setTableName('promotion_cate_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_type_tbl()
	{
		$this->setTableName('promotion_type_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_scope_tbl()
	{
		$this->setTableName('promotion_scope_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_scope_sku_tbl()
	{
		$this->setTableName('promotion_scope_sku_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_ref_order_tbl()
	{
		$this->setTableName('promotion_ref_order_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_quantity_sku_tbl()
	{
		$this->setTableName('promotion_quantity_sku_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_promotion_quantity_user_tbl()
	{
		$this->setTableName('promotion_quantity_user_tbl');
	}

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_cate($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_promotion_cate_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $cate_id
	 * @return boolean
	 */
	public function update_cate($data, $cate_id)
	{
		//������
		$cate_id = intval($cate_id);
		if( !is_array($data) || empty($data) || $cate_id<1 )
		{
			return false;
		}
		//����
		$this->set_promotion_cate_tbl();
		$affected_rows = $this->update($data, "cate_id={$cate_id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * ��ȡ������Ϣ
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
		$this->set_promotion_cate_tbl();
		return $this->find("cate_id={$cate_id}");
	}

	/**
	 * ��ȡ���������б�
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return int|array
	 */
	public function get_cate_list($parent_id=-1, $b_select_count=false, $where_str='', $order_by='sort ASC,cate_id ASC', $limit='0,20', $fields='*')
	{
		//������
		$parent_id = intval($parent_id);

		//�����ѯ����
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
		$this->set_promotion_cate_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ���д�������
	 * @param int $parent_id
	 * @param int $exclude_cate_id
	 * @return array
	 */
	public function get_cate_list_all($parent_id=0, $exclude_cate_id=0)
	{
		//������
		$parent_id = intval($parent_id);
		$exclude_cate_id = intval($exclude_cate_id);
		if( $parent_id<0 )
		{
			return array();
		}
		
		//��ȡ����
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
	 * ��ȡ������ʽ�б�
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return int|array
	 */
	public function get_type_list($b_select_count=false, $where_str='', $order_by='type_id ASC', $limit='0,20', $fields='*')
	{
		$this->set_promotion_type_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		return $this->findAll($where_str, $limit, $order_by, $fields);
	}

	/**
	 * ����
	 * @param array $data
	 * @return boolean
	 */
	public function add_scope($data)
	{
		$data['promotion_id'] = intval($data['promotion_id']);
		$data['scope_type'] = trim($data['scope_type']);
		$data['scope_scene'] = trim($data['scope_scene']);
		$data['scope_code'] = trim($data['scope_code']);
		$data['scope_value'] = trim($data['scope_value']);
		if( $data['promotion_id']<1 || !in_array($data['scope_type'], array('white', 'black')) || strlen($data['scope_scene'])<1 ||  strlen($data['scope_code'])<1 )
		{
			return false;
		}
		$this->set_promotion_scope_tbl();
		$this->insert($data, 'IGNORE');
		return true;
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $scope_id
	 * @return boolean
	 */
	public function update_scope($data, $scope_id)
	{
		//������
		$scope_id = intval($scope_id);
		if( !is_array($data) || empty($data) || $scope_id<1 )
		{
			return false;
		}
		//����
		$this->set_promotion_scope_tbl();
		$affected_rows = $this->update($data, "scope_id={$scope_id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * ɾ��
	 * @param int $scope_id
	 * @return boolean
	 */
	public function del_scope($scope_id)
	{
		$scope_id = intval($scope_id);
		if($scope_id < 1)
		{
			return false;
		}
		$where_str = "scope_id={$scope_id}";
		$this->set_promotion_scope_tbl();
		$this->delete($where_str);
		return true;
	}

	/**
	 * ��ȡ
	 * @param int $scope_id
	 * @return array
	 */
	public function get_scope_info($scope_id)
	{
		$scope_id = intval($scope_id);
		if( $scope_id<1 )
		{
			return array();
		}
		$where_str = "scope_id='{$scope_id}'";
		$this->set_promotion_scope_tbl();
		return $this->find($where_str);
	}

	/**
	 * ��ȡ�б�
	 * @param int $promotion_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_scope_list($promotion_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$promotion_id = intval($promotion_id);

		//�����ѯ����
		$sql_where = '';

		if( $promotion_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "promotion_id={$promotion_id}";
		}

		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//��ѯ
		$this->set_promotion_scope_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ���÷�Χ�б����ݳ���
	 * @param int $promotion_id
	 * @param string $scope_scene
	 * @return array
	 */
	public function get_scope_list_by_scene($promotion_id, $scope_scene)
	{
		$promotion_id = intval($promotion_id);
		$scope_scene = trim($scope_scene);
		if( $promotion_id<1 || strlen($scope_scene)<1 )
		{
			return array();
		}
		$where_str = "FIND_IN_SET('". $scope_scene. "', scope_scene)";
		return $this->get_scope_list($promotion_id, false, $where_str, 'scope_id ASC', '0,99999999');
	}

	/**
	 * ����
	 * @param int $promotion_id
	 * @param string $scope_type
	 * @param string $scope_code
	 * @param string $scope_value
	 * @return boolean
	 */
	public function add_scope_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id)
	{
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
		{
			return false;
		}
		$data = array(
			'promotion_id' => $promotion_id,
			'channel_module' => $channel_module,
			'channel_gid' => $channel_gid,
			'prices_type_id' => $prices_type_id,
		);
		$this->set_promotion_scope_sku_tbl();
		$this->insert($data, 'REPLACE');
		return true;
	}

	/**
	 * ɾ��
	 * @param $promotion_id
	 * @param $channel_module
	 * @param $channel_gid
	 * @param $prices_type_id
	 * @return bool
	 * @throws App_Exception
	 */
	public function del_scope_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id)
	{
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
		{
			return false;
		}
		$where_str = "promotion_id={$promotion_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_prices_type_id', $prices_type_id);
		$this->set_promotion_scope_sku_tbl();
		$this->delete($where_str);
		return true;
	}

	/**
	 * ɾ��
	 * @param $promotion_id
	 * @return bool
	 * @throws App_Exception
	 */
	public function del_scope_sku_list($promotion_id)
	{
		$promotion_id = intval($promotion_id);
		if( $promotion_id<1 )
		{
			return false;
		}
		$where_str = "promotion_id={$promotion_id}";
		$this->set_promotion_scope_sku_tbl();
		$this->delete($where_str);
		return true;
	}

	/**
	 * ��ȡ�б�
	 * @param int $promotion_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_scope_sku_list($promotion_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$promotion_id = intval($promotion_id);

		//�����ѯ����
		$sql_where = '';
		if( $promotion_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "promotion_id={$promotion_id}";
		}
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//��ѯ
		$this->set_promotion_scope_sku_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ���
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	public function add_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $plan_quantity<1 )
		{
			return false;
		}

		//���
		$data = array(
			'promotion_id' => $promotion_id,
			'channel_module' => $channel_module,
			'channel_gid' => $channel_gid,
			'prices_type_id' => $prices_type_id,
			'plan_quantity' => $plan_quantity,
		);
		$this->set_promotion_quantity_sku_tbl();
		$this->insert($data, 'IGNORE');
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}

	/**
	 * ����
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	public function update_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $plan_quantity<1 )
		{
			return false;
		}

		//���
		$data = array(
			'plan_quantity' => $plan_quantity,
		);
		$where_str = "promotion_id={$promotion_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_prices_type_id', $prices_type_id);
		$this->set_promotion_quantity_sku_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}

	/**
	 * @param $promotion_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 */
	public function get_quantity_sku_list($promotion_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$promotion_id = intval($promotion_id);

		//�����ѯ����
		$sql_where = '';
		if( $promotion_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "promotion_id={$promotion_id}";
		}
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//��ѯ
		$this->set_promotion_quantity_sku_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡSKU������Ϣ
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @return array
	 */
	public function get_quantity_sku_info($promotion_id, $channel_module, $channel_gid, $prices_type_id)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 )
		{
			return array();
		}

		//��ȡ
		$where_str = "promotion_id={$promotion_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_prices_type_id', $prices_type_id);
		$this->set_promotion_quantity_sku_tbl();
		return $this->find($where_str);
	}

	/**
	 * ��ʼ��SKU����
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	private function init_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $plan_quantity<1 )
		{
			return false;
		}
		$quantity_info = $this->get_quantity_sku_info($promotion_id, $channel_module, $channel_gid, $prices_type_id);
		if( !empty($quantity_info) )
		{
			return true;
		}
		return $this->add_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $plan_quantity);
	}

	/**
	 * ����
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $quantity
	 * @return boolean
	 */
	private function increase_real_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity = intval($quantity);

		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $quantity<1 )
		{
			return false;
		}
		$where_str = "promotion_id={$promotion_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id AND (plan_quantity-real_quantity)>={$quantity}";
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_prices_type_id', $prices_type_id);
		$this->set_promotion_quantity_sku_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity+{$quantity} WHERE {$where_str}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}

	/**
	 * ����
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $quantity
	 * @return boolean
	 */
	private function decrease_real_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity = intval($quantity);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $quantity<1 )
		{
			return false;
		}
		$where_str = "promotion_id={$promotion_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id AND real_quantity>={$quantity}";
//		pai_log_class::add_log($where_str, 'decrease_real_quantity_sku', 'activity');
		sqlSetParam($where_str, 'x_channel_module', $channel_module);
		sqlSetParam($where_str, 'x_prices_type_id', $prices_type_id);
		$this->set_promotion_quantity_sku_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity-{$quantity} WHERE {$where_str}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}

	/**
	 * ���
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	public function add_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$quantity_type = intval($quantity_type);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity_type = intval($quantity_type);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity_type<1 || $plan_quantity<1 )
		{
			return false;
		}

		//���
		$data = array();
		$data['promotion_id'] = $promotion_id;
		$data['buyer_user_id'] = $buyer_user_id;
		$data['plan_quantity'] = $plan_quantity;
		if( $quantity_type==1 ) //��x��
		{
			//����Ҫ���������
		}
		elseif( $quantity_type==2 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 )
			{
				return false;
			}
			$data['channel_module'] = $channel_module;
			$data['channel_gid'] = $channel_gid;
		}
		elseif( $quantity_type==3 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 || $prices_type_id<1 )
			{
				return false;
			}
			$data['channel_module'] = $channel_module;
			$data['channel_gid'] = $channel_gid;
			$data['prices_type_id'] = $prices_type_id;
		}
		$this->set_promotion_quantity_user_tbl();
		$this->insert($data, 'IGNORE');
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}

	/**
	 * ���
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	public function update_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$quantity_type = intval($quantity_type);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity_type = intval($quantity_type);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity_type<1 || $plan_quantity<1 )
		{
			return false;
		}

		//���
		$data = array();
		$data['plan_quantity'] = $plan_quantity;
		$where_str = '';
		if( $quantity_type==1 ) //��x��
		{
			//����Ҫ���������
			$where_str = "promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id}";
		}
		elseif( $quantity_type==2 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 )
			{
				return false;
			}
			$where_str = "promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid}";
			sqlSetParam($where_str, 'x_channel_module', $channel_module);
		}
		elseif( $quantity_type==3 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 || $prices_type_id<1 )
			{
				return false;
			}
			$where_str = "promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id} AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
			sqlSetParam($where_str, 'x_channel_module', $channel_module);
			sqlSetParam($where_str, 'x_prices_type_id' , $prices_type_id);
		}
		$this->set_promotion_quantity_user_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0?true:false;
	}

	/**
	 * @param $promotion_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 */
	public function get_quantity_user_list($promotion_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$promotion_id = intval($promotion_id);

		//�����ѯ����
		$sql_where = '';
		if( $promotion_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "promotion_id={$promotion_id}";
		}
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//��ѯ
		$this->set_promotion_quantity_user_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ�û��޹�������Ϣ
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @return array
	 */
	public function get_quantity_user_info($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$quantity_type = intval($quantity_type);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity_type<1 )
		{
			return array();
		}

		$sql_where = ' 1';
		if( $quantity_type==1 ) //��x��
		{
			//����Ҫ���������
		}
		elseif( $quantity_type==2 ) //ÿ���������޹���x��
		{
			if( strlen($channel_module)<1 || $channel_gid<1 )
			{
				return array();
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid}";
			sqlSetParam($sql_where, "x_channel_module", $channel_module);
		}
		elseif( $quantity_type==3 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
			{
				return array();
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
			sqlSetParam($sql_where, "x_channel_module", $channel_module);
			sqlSetParam($sql_where, "x_prices_type_id", $prices_type_id);
		}
		//��ȡ
		$where_str = " AND promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id}";
		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}
		$this->set_promotion_quantity_user_tbl();
		return $this->find($sql_where);
	}


	/**
	 * ��ʼ���û��޹�����
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $plan_quantity
	 * @return boolean
	 */
	private function init_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $plan_quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity_type = intval($quantity_type);
		$plan_quantity = intval($plan_quantity);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity_type<1 || $plan_quantity<1 )
		{
			return false;
		}

		$quantity_info = $this->get_quantity_user_info($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id);
		if( !empty($quantity_info) )
		{
			return true;
		}
		return $this->add_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $plan_quantity);
	}

	/**
	 * ����
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @param int $quantity
	 * @return boolean
	 */
	private function increase_real_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$quantity_type = intval($quantity_type);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity = intval($quantity);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity_type<1 || $quantity<1 )
		{
			return false;
		}
		
		$sql_where = ' 1';
		if( $quantity_type==1 ) //��x��
		{
			//����Ҫ���������
		}
		elseif( $quantity_type==2 ) //ÿ���������޹���x��
		{
			if( strlen($channel_module)<1 || $channel_gid<1 )
			{
				return false;
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid}";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		elseif( $quantity_type==3 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
			{
				return false;
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
			sqlSetParam($sql_where, 'x_prices_type_id', $prices_type_id);
		}
		else
		{
			return false;
		}
		$where_str = " AND promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id} AND (plan_quantity-real_quantity)>={$quantity}";
		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}
		$this->set_promotion_quantity_user_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity+{$quantity} WHERE {$sql_where}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ����
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity
	 * @return boolean
	 */
	private function decrease_real_quantity_user($promotion_id, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id, $quantity)
	{
		//������
		$promotion_id = intval($promotion_id);
		$buyer_user_id = intval($buyer_user_id);
		$quantity_type = intval($quantity_type);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		$quantity = intval($quantity);
		if( $promotion_id<1 || $buyer_user_id<1 || $quantity<1 )
		{
			return false;
		}

		$sql_where = ' 1';
		if( $quantity_type==1 ) //��x��
		{
			//����Ҫ���������
		}
		elseif( $quantity_type==2 ) //ÿ���������޹���x��
		{
			if( strlen($channel_module)<1 || $channel_gid<1 )
			{
				return array();
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid}";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
		}
		elseif( $quantity_type==3 )
		{
			if( strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
			{
				return array();
			}
			$sql_where .= " AND channel_module=:x_channel_module AND channel_gid={$channel_gid} AND prices_type_id=:x_prices_type_id";
			sqlSetParam($sql_where, 'x_channel_module', $channel_module);
			sqlSetParam($sql_where, 'x_prices_type_id', $prices_type_id);
		}

		$where_str = " AND promotion_id={$promotion_id} AND buyer_user_id={$buyer_user_id} AND real_quantity>={$quantity}";
		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}
		$this->set_promotion_quantity_user_tbl();
		$this->query("UPDATE {$this->_db_name}.{$this->_tbl_name} SET real_quantity=real_quantity-{$quantity} WHERE {$sql_where}");
		$affected_rows = $this->get_affected_rows();
		return $affected_rows>0?true:false;
	}

	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_ref_order($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_promotion_ref_order_tbl();
		return $this->insert($data);
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $id
	 * @return boolean
	 */
	public function update_ref_order($data, $id)
	{
		//������
		$id = intval($id);
		if( !is_array($data) || empty($data) || $id<1 )
		{
			return false;
		}
		//����
		$this->set_promotion_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * ��ȡ��Ϣ
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
		$this->set_promotion_ref_order_tbl();
		return $this->find("id={$id}");
	}

	/**
	 * �������˻�
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
		$this->set_promotion_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����Ѷ���
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
		$this->set_promotion_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND used_amount>={$cash_amount} AND is_refund=0 AND is_cash=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * �����ѽ���
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
		$this->set_promotion_ref_order_tbl();
		$affected_rows = $this->update($data, "id={$id} AND is_refund=0 AND is_cash=1 AND is_settle=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ�б�
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_ref_order_list($b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		//��ѯ
		$this->set_promotion_ref_order_tbl();
		if( $b_select_count )
		{
			return $this->findCount($where_str);
		}
		$list = $this->findAll($where_str, $limit, $order_by, $fields);
		return $list;
	}

	/**
	 * ��ȡ�б�
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
	 * ���
	 * @param array $data
	 * @return int
	 */
	public function add_promotion($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_promotion_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * �޸�
	 * @param array $data
	 * @param int $promotion_id
	 * @return boolean
	 */
	public function update_promotion($data, $promotion_id)
	{
		//������
		$promotion_id = intval($promotion_id);
		if( !is_array($data) || empty($data) || $promotion_id<1 )
		{
			return false;
		}
		//����
		$this->set_promotion_tbl();
		$affected_rows = $this->update($data, "promotion_id={$promotion_id}");
		return $affected_rows>0?true:false;
	}

	/**
	 * ��ȡ������Ϣ
	 * @param int $promotion_id ����id
	 * @return array
	 */
	public function get_promotion_info($promotion_id)
	{
		$promotion_id = intval($promotion_id);
		if( $promotion_id<1 )
		{
			return array();
		}
		$this->set_promotion_tbl();
		return $this->find("promotion_id={$promotion_id}");
	}

	/**
	 * ��ȡ������Ϣ
	 * @param int $promotion_id
	 * @return array
	 */
	public function get_promotion_full_info($promotion_id)
	{
		$promotion_info = $this->get_promotion_info($promotion_id);
		if( empty($promotion_info) )
		{
			return array();
		}
		$promotion_full_list = $this->fill_promotion_full_list(array($promotion_info));
		$promotion_full_info = $promotion_full_list[0];
		if( !is_array($promotion_full_info) ) $promotion_full_info = array();
		return $promotion_full_info;
	}

	/**
	 * ���䶩��������Ϣ
	 * @param array $promotion_list
	 * @return array
	 */
	private function fill_promotion_full_list($promotion_list)
	{
		if( !is_array($promotion_list) )
		{
			return $promotion_list;
		}

		//��ȡ������ʽ�б�
		$type_list_arr = array();
		if( !empty($promotion_list) )
		{
			$type_list_tmp = $this->get_type_list(false, '', 'type_id ASC', '0,99999999999');
			foreach($type_list_tmp as $type_info_tmp)
			{
				$type_id_tmp = intval($type_info_tmp['type_id']);
				$type_list_arr[$type_id_tmp] = $type_info_tmp;
			}
		}

		//��������
		foreach($promotion_list as $promotion_key=>$promotion_info)
		{
			//������ʽ��Ϣ
			$type_id_tmp = intval($promotion_info['type_id']);
			$type_info_tmp = $type_list_arr[$type_id_tmp];
			if( !is_array($type_info_tmp) ) $type_info_tmp = array();
			$promotion_info['type_name'] = trim($type_info_tmp['type_name']);
			$promotion_info['type_unit'] = trim($type_info_tmp['type_unit']);

			$promotion_list[$promotion_key] = $promotion_info;
		}

		return $promotion_list;
	}

	/**
	 * ��ȡ�����б�
	 * @param int $cate_id
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return int|array
	 */
	public function get_promotion_list($cate_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$cate_id = intval($cate_id);

		//�����ѯ����
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

		//��ѯ
		$this->set_promotion_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ��Ч�ڴ����б�
	 * @return array
	 */
	private function get_promotion_list_by_valid($type_target='')
	{
		$type_target = trim($type_target);
		if( is_null($this->cache_promotion_list_valid_arr) )
		{
			//��ǰʱ��
			$cur_time = time();

			//��ȡ�����б�
			$where_str = "check_status=1 AND start_time<={$cur_time} AND {$cur_time}<=end_time";

			$promotion_list = $this->get_promotion_list(0, false, $where_str, 'promotion_id ASC', '0,99999999999');

			//�������ID
			$promotion_id_arr = array();
			foreach($promotion_list as $promotion_info)
			{
				$promotion_id_arr[] = intval($promotion_info['promotion_id']);
			}
			$promotion_id_str = implode(',', $promotion_id_arr);

			//��ȡ���÷�Χ�б�
			$scope_list_all = array();
			if( strlen($promotion_id_str)>0 )
			{
				$scope_list_all = $this->get_scope_list(0, false, "promotion_id IN ($promotion_id_str)", 'promotion_id ASC, scope_id ASC', '0,99999999');
			}
			foreach($promotion_list as $promotion_key=>$promotion_info)
			{
				//���÷�Χ�б�
				$scope_list_tmp = array();
				foreach($scope_list_all as $scope_key=>$scope_info)
				{
					if( $scope_info['promotion_id']==$promotion_info['promotion_id'] )
					{
						$scope_list_tmp[] = $scope_info;
						unset($scope_list_all[$scope_key]);
					}
				}
				$promotion_info['scope_list'] = $scope_list_tmp;
				$promotion_list[$promotion_key] = $promotion_info;
			}

			$promotion_full_list = $this->fill_promotion_full_list($promotion_list, $cur_time);

			$this->cache_promotion_list_valid_arr = $promotion_full_list;
			$this->cache_promotion_list_valid_time = $cur_time;
		}

		//ȫ������
		if( $type_target=='' )
		{
			return $this->cache_promotion_list_valid_arr;
		}
		//����type_target��ֵ������Ӧ����
		$list = array();
		foreach( $this->cache_promotion_list_valid_arr as $value )
		{
			if( $type_target==$value['type_target'] )
			{
				$list[] = $value;
			}
		}
		return $list;
	}

	/**
	 * ��ȡ��Ч�ڴ�����Ϣ
	 * @param int $promotion_id
	 * @return array
	 */
	private function get_promotion_info_by_valid($promotion_id)
	{
		$promotion_id = intval($promotion_id);
		if( $promotion_id<1 )
		{
			return array();
		}
		$info = array();
		$promotion_list = $this->get_promotion_list_by_valid();
		foreach($promotion_list as $promotion_info)
		{
			if( $promotion_info['promotion_id']==$promotion_id )
			{
				$info = $promotion_info;
				break;
			}
		}
		return $info;
	}

	/**
	 * ��ȡ���÷�Χ��Ϣ
	 * @param array $scope_list
	 * @param string $scope_type
	 * @param string $scope_code
	 * @param string $scope_scene
	 * @return array
	 */
	private function get_scope_info_from_cache($scope_list, $scope_type, $scope_code, $scope_scene)
	{
		$scope_type = trim($scope_type);
		$scope_code = trim($scope_code);
		$scope_scene = trim($scope_scene);
		if( !is_array($scope_list) || empty($scope_list) || strlen($scope_type)<1 || strlen($scope_code)<1 || strlen($scope_scene)<1 )
		{
			return array();
		}
		$scope_type = strtolower($scope_type);
		$scope_code = strtolower($scope_code);
		$scope_scene = strtolower($scope_scene);

		//����
		$scope_info = array();
		foreach($scope_list as $val)
		{
			$scope_type_tmp = strtolower(trim($val['scope_type']));
			if( $scope_type!=$scope_type_tmp ) continue;

			$scope_code_tmp = strtolower(trim($val['scope_code']));
			if( $scope_code!=$scope_code_tmp ) continue;

			//��鳡��
			$scope_scene_str = strtolower(trim($val['scope_scene']));
			$scope_scene_arr = explode(',', $scope_scene_str);
			if( !in_array($scope_scene, $scope_scene_arr, true) ) continue;

			$scope_info = $val;
			break;
		}
		return $scope_info;
	}

	/**
	 * �ֿ����÷�Χ���õ��ڰ��б�
	 * @param array $scope_list
	 * @param string $scope_scene
	 * @return array
	 */
	private function separate_scope_list_by_scene($scope_list, $scope_scene)
	{
		$scope_scene = trim($scope_scene);
		if( !is_array($scope_list) || empty($scope_list) || strlen($scope_scene)<1 )
		{
			return array();
		}

		//����ڰ�����
		$white_list = array();
		$black_list = array();
		foreach($scope_list as $val)
		{
			//��鳡��
			$scope_scene_str = trim($val['scope_scene']);
			$scope_scene_arr = explode(',', $scope_scene_str);
			if( !in_array($scope_scene, $scope_scene_arr, true) ) continue;

			//�ֿ�����
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
			return array();
		}
		return array(
			'white_list' => $white_list,
			'black_list' => $black_list,
		);
	}

	/**
	 * ������÷�Χ�е�SKU
	 * @param int $promotion_id
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param string $prices_type_id (����ǻ����Ϊ"����ID#���ID")
	 * @return bool
	 */
	private function check_scope_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id)
	{
		//������
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
		$channel_gid = intval($channel_gid);
		$prices_type_id = trim($prices_type_id);
		if( $promotion_id<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<1 )
		{
			return false;
		}

		//��ȡSKU����
		$cache_key = "p_{$promotion_id}_m_{$channel_module}_g_{$channel_gid}";
		if( array_key_exists($cache_key, $this->cache_scope_sku_prices_type_id_arr) )
		{
			$prices_type_id_arr = $this->cache_scope_sku_prices_type_id_arr[$cache_key];
		}
		else
		{
			$where_str = "channel_module=:x_channel_module AND channel_gid=:x_channel_gid";
			sqlSetParam($where_str, 'x_channel_module', $channel_module);
			sqlSetParam($where_str, 'x_channel_gid', $channel_gid);
			$sku_list = $this->get_scope_sku_list($promotion_id, false, $where_str, '', '0,99999999');
			if( !is_array($sku_list) ) $sku_list = array();

			$prices_type_id_arr = array();
			foreach($sku_list as $sku_info)
			{
				$prices_type_id_arr[] = trim($sku_info['prices_type_id']);
			}
			$this->cache_scope_sku_prices_type_id_arr[$cache_key] = $prices_type_id_arr;
		}

		return in_array($prices_type_id, $prices_type_id_arr, true);
	}

	/**
	 * ��ȡ���÷�Χ����
	 * @return array
	 */
	public function get_scope_code_arr()
	{
		return array(
			'all' => 'ȫ��',
			'channel_module' => 'ģ������',
			'sku' => 'ģ��#��ƷID#��Ʒ�۸�ID',
			'goods_prices' => '��Ʒ�۸�',
			'order_total_amount' => '�������',
			'org_user_id' => '�����û�ID',
			'location_id' => '����ID������ϵͳ��',
			'seller_user_id' => '�̼��û�ID���̳�ϵͳ������ϵͳ��',
			'mall_type_id' => '����Ʒ��ID���̳�ϵͳ��',
		);
	}

	/**
	 * ����Ƿ����ڷ�Χ��
	 * @param array $scope_info
	 * @param array $param_info
	 * @param int $cur_time ��ǰʱ��
	 * @return boolean û������������󣬷���false
	 * @tutorial
	 *
	 * $scope_code $scope_value������
	 * channel_module yuepai#waipai#topic ģ������
	 * order_amount 5.00 �������
	 * model_user_id 1#2 ģ���û�ID
	 * org_user_id 1#2 �����û�ID
	 * event_id 1#2 �ID
	 *
	 */
	private function check_scope_code($login_user_id, $scope_info, $param_info)
	{
		$result = false;

		//������
		if( !is_array($scope_info) || empty($scope_info) )
		{
			return $result;
		}
		if( !is_array($param_info) ) $param_info = array();

		//����Χ
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

		//�жϷ�Χ
		switch($scope_code)
		{
			case 'all': // ����
				$result = true;
				break;
			case 'channel_module': //����ģ�� mall_order waipai
				$channel_module = trim($param_info['channel_module']);
				if( in_array($channel_module, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'sku': // ��Ʒid��ȡ��չ��
				$promotion_id = trim($scope_info['promotion_id']);
				$channel_module = trim($param_info['channel_module']);
				$channel_gid = trim($param_info['channel_gid']);
				$prices_type_id = trim($param_info['prices_type_id']);
				$rst = $this->check_scope_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id);
				if( $rst )
				{
					$result = true;
				}
				break;
			case 'goods_prices': //�۸�����
				$goods_prices = $param_info['goods_prices']*1;
				if( $goods_prices>0 && $goods_prices>=($scope_value*1) )
				{
					$result = true;
				}
				break;
			case 'order_total_amount': //��������
				$order_total_amount = $param_info['order_total_amount']*1;
				if( $order_total_amount>0 && $order_total_amount>=($scope_value*1) )
				{
					$result = true;
				}
				break;
			case 'org_user_id': // ����
				$org_user_id = trim($param_info['org_user_id']);
				if( in_array($org_user_id, $scope_value_arr, true) )
				{
					$result = true;
				}
				break;
			case 'location_id': // ����
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
			case 'seller_user_id':
				$seller_user_id = trim($param_info['seller_user_id']);
				if( in_array($seller_user_id, $scope_value_arr, true) )
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
			default:
				break;
		}
		return $result;
	}

	/**
	 * ������÷�Χ
	 * @param int $login_user_id ��ǰ��¼�û�id
	 * @param array $promotion_info ������Ϣ������scope_list
	 * @param array $param_info
	 * array(
	 * 	'channel_module' => '', //����
	 * 	'scope_scene' => '', //����
	 *  'order_total_amount' => 0, //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 *  ......
	 *  'channel_gid' => 0, //����
	 *  'prices_type_id' => 0, //����
	 *  'goods_prices' => 0, //����
	 *  'quantity' => 0, //����
	 * )
	 * @return array
	 */
	private function check_promotion_scope($login_user_id, $promotion_info, $param_info)
	{
		$result = array('result'=>0, 'message'=>'', 'message_white_false'=>'', 'message_black_true'=>'');

		$login_user_id = intval($login_user_id);
		if( !is_array($promotion_info) || empty($promotion_info) || !is_array($param_info) || empty($param_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$scope_list = $promotion_info['scope_list'];
		$scope_scene = trim($param_info['scope_scene']);
		if( !is_array($scope_list) || empty($scope_scene) || strlen($scope_scene)<1 )
		{
			$result['result'] = -2;
			$result['message'] = '��������';
			return $result;
		}

		//�ֿ����÷�Χ
		$separate_arr = $this->separate_scope_list_by_scene($scope_list, $scope_scene);
		if( !is_array($separate_arr) || empty($separate_arr) )
		{
			$result['result'] = -3;
			$result['message'] = 'û�����÷�Χ';
			return $result;
		}
		$white_list = $separate_arr['white_list'];
		$black_list = $separate_arr['black_list'];
		if( !is_array($white_list) || !is_array($black_list) || (empty($white_list) && empty($black_list)) )
		{
			$result['result'] = -4;
			$result['message'] = '���÷�Χ����';
			return $result;
		}

		//��������
		$ret_white = true;
		$white_info_false = array();
		foreach ($white_list as $k => $white_info)
		{
			$ret = $this->check_scope_code($login_user_id, $white_info, $param_info);
			if (!$ret) {
				//���������ķ�Χ�ڣ�������
				$ret_white = false;
				$white_info_false = $white_info;
				break;
			}
		}

		//��������
		$ret_black = false;
		$black_info_true = array();
		foreach ($black_list as $black_info)
		{
			$ret = $this->check_scope_code($login_user_id, $black_info, $param_info);
			if( $ret )
			{
				//�������ķ�Χ�ڣ�������
				$ret_black = true;
				$black_info_true = $black_info;
				break;
			}
		}

		if( $ret_white==true && $ret_black==false)
		{
			$result['result'] = 1;
			$result['message'] = '����';
		}
		else
		{
			$result['result'] = -5;
			$result['message'] = '������';
			$result['message_white_false'] = http_build_query($white_info_false);
			$result['message_black_true'] = http_build_query($black_info_true);
		}
		return $result;
	}

	/**
	 * ����ʹ�ý��
	 * ���ƣ��Żݽ��ҪС�ڶ������
	 * @param array $promotion_info
	 * @param array $cal_info
	 * array(
	 * 	'order_total_amount' => 0, //4��������õ�
	 * 	'goods_prices' => 0, //1���۴�����2��ʱ�ۿۡ�3��ʱ�ͼۣ��õ�
	 * )
	 * @param boolean $b_deal_face_max ��������޶�
	 * @return double ����2λС��
	 */
	private function cal_promotion_amount($promotion_info, $cal_info, $b_deal_face_max=true)
	{
		//������
		$type_id = intval($promotion_info['type_id']);
		$face_value = number_format($promotion_info['face_value']*1, 2, '.', '')*1;
		$face_max = number_format($promotion_info['face_max']*1, 2, '.', '')*1;
		$order_total_amount = number_format($cal_info['order_total_amount']*1, 2, '.', '')*1;
		$goods_prices = number_format($cal_info['goods_prices']*1, 2, '.', '')*1;

		if( $type_id<1 || $face_value<=0 )
		{
			return 0;
		}

		//����
		$promotion_amount = 0;
		if( $type_id==1 ) //��Ʒ�����۴���
		{
//			pai_log_class::add_log(array('func1'=>func_get_args(),'goods_prices'=>$goods_prices,'face_value'=>$face_value), 'cal_promotion_amount', 'promotion');
			if( $goods_prices>0 && $face_value>0 && $face_value<$goods_prices ) //���ƣ��ֿ۽��ҪС����Ʒ����
			{
				$promotion_amount = $face_value;
			}
		}
		elseif( $type_id==2 ) //��Ʒ����ʱ�ۿ�
		{
			if( $goods_prices>0 && $face_value>0 && $face_value<100 && $face_max>0 ) //���ƣ��ֿ۱���Ҫ��������Ҫ���޶�
			{
				$promotion_amount = $goods_prices - round($goods_prices*$face_value/100, 2);
				if( $b_deal_face_max ) $promotion_amount = min($promotion_amount, $face_max);
				if( $promotion_amount>=$goods_prices ) $promotion_amount = 0;
			}
		}
		elseif( $type_id==3 ) //��Ʒ����ʱ�ͼ�
		{
			if( $goods_prices>0 && $face_value>0 && $face_value<$goods_prices && $face_max>0 ) //���ƣ��ֿ۽��ҪС����Ʒ���ۣ�����Ҫ���޶�
			{
				$promotion_amount = $goods_prices - $face_value;
				if( $b_deal_face_max ) $promotion_amount = min($promotion_amount, $face_max);
				if( $promotion_amount>=$goods_prices ) $promotion_amount = 0;
			}
		}
		elseif( $type_id==4 ) //�����������
		{
			if( $face_value>0 && $order_total_amount>0 && $face_value<$order_total_amount ) //���ƣ��ֿ۽��ҪС�ڶ������
			{
				$promotion_amount = $face_value;
			}
		}

		if( $promotion_amount<=0 )
		{
			return 0;
		}
		return number_format($promotion_amount, 2, '.', '')*1;
	}

	/**
	 * ����SKU����
	 * @param array $promotion_info
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @return array
	 */
	private function cal_quantity_sku_info($promotion_info, $channel_module, $channel_gid, $prices_type_id)
	{
		$quantity_info = $this->get_quantity_sku_info($promotion_info['promotion_id'], $channel_module, $channel_gid, $prices_type_id);
		if( !empty($quantity_info) )
		{
			$plan_quantity_sku = $quantity_info['plan_quantity']*1;
			$real_quantity_sku = $quantity_info['real_quantity']*1;
		}
		else
		{
			$plan_quantity_sku = $promotion_info['default_quantity_sku']*1;
			$real_quantity_sku = 0;
		}
		$remain_quantity_sku = $plan_quantity_sku - $real_quantity_sku;
		if( $remain_quantity_sku<0 ) $remain_quantity_sku = 0;
		return array(
			'plan_quantity' => $plan_quantity_sku,
			'real_quantity' => $real_quantity_sku,
			'remain_quantity' => $remain_quantity_sku,
		);
	}

	/**
	 * �����û��޹�����
	 * @param int $promotion_id
	 * @param int $buyer_user_id
	 * @param int $quantity_type //�û��޹�����
	 * @param string $channel_module
	 * @param int $channel_gid
	 * @param int $prices_type_id
	 * @return array
	 */
	private function cal_quantity_user_info($promotion_info, $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id)
	{
		$buyer_user_id = intval($buyer_user_id);
		$quantity_info = $this->get_quantity_user_info($promotion_info['promotion_id'], $buyer_user_id, $quantity_type, $channel_module, $channel_gid, $prices_type_id);

		if( !empty($quantity_info) )
		{
			$plan_quantity_user = $quantity_info['plan_quantity']*1;
			$real_quantity_user = $quantity_info['real_quantity']*1;
		}
		else
		{
			$plan_quantity_user = $promotion_info['default_quantity_user']*1;
			$real_quantity_user = 0;
		}
		$remain_quantity_user = $plan_quantity_user - $real_quantity_user;
		if( $remain_quantity_user<0 ) $remain_quantity_user = 0;

		return array(
			'plan_quantity' => $plan_quantity_user,
			'real_quantity' => $real_quantity_user,
			'remain_quantity' => $remain_quantity_user,
		);
	}

	/**
	 * ��ȡ�����б�����۸�
	 * @param int $login_user_id
	 * @param array $show_param_info
	 * @param string $type_target (order/goods/null)
	 * array(
	 * 	'channel_module' => '', //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 *  'channel_gid' => 0, //����
	 *  ......
	 * )
	 * @param array $prices_list
	 * array(array(
	 * 	'prices_type_id' => 0, //����
	 * 	'goods_prices' => 0, //����
	 * ))
	 * @return array
	 */
	public function get_promotion_info_for_show_multiple($login_user_id, $type_target, $show_param_info, $prices_list, $b_remain_quantity=false)
	{
		//������
		$login_user_id = intval($login_user_id);
		$type_target = trim($type_target);
		$channel_module = trim($show_param_info['channel_module']);
		if( strlen($type_target)<1 || strlen($channel_module)<1 || !is_array($prices_list) || empty($prices_list) )
		{
			return array();
		}

		//��ȡ��ʡ�Ĵ�����Ϣ
		$info = array();
		$cal_save_amount = 0;
		foreach( $prices_list as $key=>$prices_info )
		{
			$promotion_list = $this->get_promotion_list_for_show_single($login_user_id, $type_target, $show_param_info, $prices_info, $b_remain_quantity);
			foreach( $promotion_list as $promotion_info )
			{
				if( $promotion_info['cal_save_amount']>$cal_save_amount )
				{
					$info['most_saving_promotion'] = $promotion_info;
				}
				$cal_save_amount = $promotion_info['cal_save_amount']*1;
				$info['promotion_list'][$key] = $promotion_info;
			}
		}

		return $info;
	}

	/**
	 * ��ȡ�����б������۸�
	 * @param int $login_user_id
	 * @param string $type_target (order/goods/null)
	 * @param array $show_param_info
	 * array(
	 * 	'channel_module' => '', //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 * 	'channel_gid' => 0, //����
	 *  ......
	 * )
	 * @param array $prices_info
	 * array(
	 * 	'prices_type_id' => 0, //����
	 * 	'stage_id' => 0,
	 * 	'goods_prices' => 0, //����
	 * )
	 * @param bool $b_remain_quantity
	 * @return array
	 */
	public function get_promotion_list_for_show_single($login_user_id, $type_target, $show_param_info, $prices_info, $b_remain_quantity=false)
	{
		//������
		$login_user_id = intval($login_user_id);
		$type_target = trim($type_target);
		$channel_module = trim($show_param_info['channel_module']);
		$channel_gid = intval($show_param_info['channel_gid']);
		$stage_id = intval($prices_info['stage_id']);
		$prices_type_id = trim($prices_info['prices_type_id']);
		$goods_prices = $prices_info['goods_prices']*1;
		if( $stage_id>0 )
		{
			$prices_type_id = $stage_id.'_'.$prices_type_id;
		}
		if( strlen($type_target)<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $goods_prices<=0 )
		{
			return array();
		}
		$quantity = 1;
		//��ȡ�б�
		$single_param_info = $show_param_info;
		$single_param_info['scope_scene'] = 'show';
		$single_param_info['order_total_amount'] = $goods_prices*$quantity;

		$single_prices_info = $prices_info;
		$single_prices_info['quantity'] = $quantity;
		$single_prices_info['channel_gid'] = $channel_gid;

		return $this->get_promotion_list_for_single($login_user_id, $type_target, $single_param_info, $single_prices_info, $b_remain_quantity);
	}

	/**
	 * ��ȡ�����б������۸�
	 * @param int $login_user_id
	 * @param string $type_target (order/goods/null)
	 * @param array $order_param_info
	 * array(
	 * 	'channel_module' => '', //����
	 *  'order_total_amount' => 0, //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 *  ......
	 * )
	 * @param array $prices_info
	 * array(
	 * 	'channel_gid' => 0, //����
	 *  'stage_id'=>0
	 * 	'prices_type_id' => 0, //����
	 * 	'goods_prices' => 0, //����
	 * )
	 * @param int $quantity
	 * @param bool $b_remain_quantity
	 * @return array
	 */
	public function get_promotion_list_for_use_single($login_user_id, $type_target, $order_param_info, $prices_info, $quantity, $b_remain_quantity=false)
	{
		//������
		$login_user_id = intval($login_user_id);
		$type_target = trim($type_target);
		$channel_module = trim($order_param_info['channel_module']);
		$order_total_amount = $order_param_info['order_total_amount']*1;
		$channel_gid = intval($prices_info['channel_gid']);
		$prices_type_id = trim($prices_info['prices_type_id']);
		$goods_prices = $prices_info['goods_prices']*1;
		$quantity = intval($quantity);
		if( strlen($type_target)<1 || strlen($channel_module)<1 || $order_total_amount<=0 || $channel_gid<1 || strlen($prices_type_id)<0 || $goods_prices<=0 || $quantity<1 )
		{
			return array();
		}
		//��ȡ�б�
		$single_param_info = $order_param_info;
		$single_param_info['scope_scene'] = 'use';

		$single_prices_info = $prices_info;
		$single_prices_info['quantity'] = $quantity;

		return $this->get_promotion_list_for_single($login_user_id, $type_target, $single_param_info, $single_prices_info, $b_remain_quantity);
	}

	/**
	 * ��ȡ�����б������۸�
	 * @param int $login_user_id
	 * @param string $type_target (order/goods/null)
	 * @param array $single_param_info
	 * array(
	 * 	'channel_module' => '', //����
	 * 	'scope_scene' => '', //����
	 *  'order_total_amount' => 0, //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 *  ......
	 * )
	 * @param array $prices_info
	 * array(
	 * 	'channel_gid' => 0, //����
	 *  'stage_id'
	 * 	'prices_type_id' => 0, //����
	 * 	'goods_prices' => 0, //����
	 *  'quantity' => 0, //����
	 * )
	 * @param bool $b_remain_quantity
	 * @return array
	 */
	private function get_promotion_list_for_single($login_user_id, $type_target, $single_param_info, $single_prices_info, $b_remain_quantity=false)
	{
		//������
		$login_user_id = intval($login_user_id);
		$type_target = trim($type_target);
		$channel_module = trim($single_param_info['channel_module']);
		$order_total_amount = $single_param_info['order_total_amount']*1;
		$channel_gid = intval($single_prices_info['channel_gid']);
		$stage_id = intval($single_prices_info['stage_id']);
		$prices_type_id = trim($single_prices_info['prices_type_id']);
		$goods_prices = $single_prices_info['goods_prices']*1;
		$quantity = intval($single_prices_info['quantity']);
		if( strlen($type_target)<1 || strlen($channel_module)<1 || $channel_gid<1 || strlen($prices_type_id)<0 || $goods_prices<=0 || $quantity<1 )
		{
			return array();
		}

		//��ȡ��Ч�ڴ����б�
		$promotion_list = $this->get_promotion_list_by_valid($type_target);
		if( !is_array($promotion_list) || empty($promotion_list) )
		{
			return array();
		}
		if( $stage_id>1 )
		{
			$prices_type_id = $stage_id.'_'.$prices_type_id;
		}

		//�������
		$param_info = $single_param_info;
		$param_info['channel_gid'] = $channel_gid;
		$param_info['prices_type_id'] = $prices_type_id;
		$param_info['goods_prices'] = $goods_prices;
		$param_info['quantity'] = $quantity;
		//����Ƿ�����
		$list = array();
		foreach($promotion_list as $promotion_info)
		{
			//��װ��prices_type_id
			$promotion_info['prices_type_id'] = $prices_type_id;

			//�ж��Ƿ�����������޶�(face_max)
			$cal_info = array(
				'order_total_amount' => $order_total_amount, //4��������õ�
				'goods_prices' => $goods_prices, //1���۴�����2��ʱ�ۿۡ�3��ʱ�ͼۣ��õ�
			);
			$cal_promotion_amount = $this->cal_promotion_amount($promotion_info, $cal_info, false);
			if( $cal_promotion_amount<=0 || $cal_promotion_amount>$promotion_info['face_max'] )
			{
				continue;
			}

			$rst = $this->check_promotion_scope($login_user_id, $promotion_info, $param_info);
			if( $rst['result']!=1 )
			{
				continue;
			}

			//�Ƿ�����������ҪΪ����������Ʒ������µ���
			if( $b_remain_quantity )
			{
				//�����û��޹�ʣ������
				$cal_quantity_sku_info = $this->cal_quantity_user_info($promotion_info, $login_user_id, $promotion_info['quantity_user_type'], $channel_module, $channel_gid, $prices_type_id);
				$promotion_info['plan_quantity_user'] = intval($cal_quantity_sku_info['plan_quantity']);
				$promotion_info['real_quantity_user'] = intval($cal_quantity_sku_info['real_quantity']);
				$promotion_info['remain_quantity_user'] = intval($cal_quantity_sku_info['remain_quantity']);
				//����������
				$cal_quantity_sku_info = $this->cal_quantity_sku_info($promotion_info, $channel_module, $channel_gid, $prices_type_id);
				$promotion_info['plan_quantity_sku'] = intval($cal_quantity_sku_info['plan_quantity']);
				$promotion_info['real_quantity_sku'] = intval($cal_quantity_sku_info['real_quantity']);
				$promotion_info['remain_quantity_sku'] = intval($cal_quantity_sku_info['remain_quantity']);
				//���������Թ����������������ȷ��ҳ�ж��û����������Ƿ���ڴ��������޹�������
				$promotion_info['remain_quantity'] = min($promotion_info['remain_quantity_user'], $promotion_info['remain_quantity_sku']);
			}

			//�����ʡ�Ľ��
			$cal_info = array(
				'order_total_amount' => PHP_INT_MAX, //4��������õ�
				'goods_prices' => $goods_prices, //1���۴�����2��ʱ�ۿۡ�3��ʱ�ͼۣ��õ�
			);
			$cal_save_amount = $this->cal_promotion_amount($promotion_info, $cal_info);
			$promotion_info['cal_save_amount'] = $cal_save_amount;

			//����ֿ۽��
			$cal_info = array(
				'order_total_amount' => $order_total_amount, //4��������õ�
				'goods_prices' => $goods_prices, //1���۴�����2��ʱ�ۿۡ�3��ʱ�ͼۣ��õ�
			);
			$cal_promotion_amount = $this->cal_promotion_amount($promotion_info, $cal_info);
			$promotion_info['cal_used_amount'] = $cal_promotion_amount * $quantity;

			//���������
			if( $promotion_info['type_id']==4 ) //4�����
			{
				$scope_info_tmp = $this->get_scope_info_from_cache($promotion_info['scope_list'], 'white', 'order_total_amount', 'use');
				$scope_value_tmp = $scope_info_tmp['scope_value']*1;
				$face_value_tmp = $promotion_info['face_value']*1;
				$promotion_info['cal_goods_prices'] = "��{$scope_value_tmp}��{$face_value_tmp}";
			}
			else
			{
				$promotion_info['cal_goods_prices'] = '��' . ($goods_prices - $promotion_info['cal_save_amount']);
			}

			unset($promotion_info['scope_list']);
			$promotion_info['start_time_str'] = date('Y.m.d', $promotion_info['start_time']);
			$promotion_info['end_time_str'] = date('Y.m.d', $promotion_info['end_time']);
			$list[] = $promotion_info;
		}

		return $list;
	}

	/**
	 * ��������Ч�ԡ����÷�Χ
	 * ����ʹ��ǰ���Դ������м��
	 * @param int $buyer_user_id
	 * @param string $type_target (order/goods/null)
	 * @param int $promotion_id
	 * @param int $channel_module
	 * @param int $channel_oid $channel_oid
	 * @param array $use_param_info
	 * array(
	 *  'order_total_amount' => 0, //����
	 * 	'org_user_id' => 0,
	 * 	'location_id' => 0,
	 *  'seller_user_id' => 0,
	 * 	'mall_type_id' => 0,
	 *  ......
	 * )
	 * @param array $use_prices_info
	 * array(
	 * 	'channel_gid' => 0, //����
	 * 	'prices_type_id' => 0, //����
	 *  'stage_id' => 0, //���������ID
	 * 	'goods_prices' => 0, //����
	 *  'quantity' => 0, //����
	 * )
	 * @return array array('result'=>0, 'message'=>'', 'promotion_ref_id'=>0, 'promotion_amount'=>0)
	 *
	 */
	public function use_promotion($buyer_user_id, $type_target, $promotion_id, $channel_module, $use_param_info, $use_prices_info)
	{
		$result = array('result'=>0, 'message'=>'', 'promotion_ref_id'=>0, 'promotion_amount'=>0);

		//������
		$buyer_user_id = intval($buyer_user_id);
		$type_target = trim($type_target);
		$promotion_id = intval($promotion_id);
		$channel_module = trim($channel_module);
//		$channel_oid = intval($channel_oid);
		$order_total_amount = number_format($use_param_info['order_total_amount']*1, 2, '.', '')*1;
		$channel_gid = intval($use_prices_info['channel_gid']);
		$stage_id = intval($use_prices_info['stage_id']);
		$prices_type_id = trim($use_prices_info['prices_type_id']);
		$goods_prices = $use_prices_info['goods_prices']*1;
		$quantity = intval($use_prices_info['quantity']);

		//channel_oid��δ���� || $channel_oid<1
		if( $stage_id>1 )
		{
			$prices_type_id = $stage_id.'_'.$prices_type_id;
		}

		if( $buyer_user_id==116127 )
		{
			pai_log_class::add_log(func_get_args(), 'use_promotion', 'promotion_test');
		}

		if( $buyer_user_id<1 || strlen($type_target)<1 || $promotion_id<1 || strlen($channel_module)<1
			|| $channel_gid<1 || strlen($prices_type_id)<0 || $goods_prices<=0 || $quantity<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ��Ч������Ϣ
		$promotion_info = $this->get_promotion_info_by_valid($promotion_id);
		if( empty($promotion_info) )
		{
			$result['result'] = -2;
			$result['message'] = '�˴�����ʧЧ';
			return $result;
		}
		if( $promotion_info['type_target']!=$type_target )
		{
			$result['result'] = -3;
			$result['message'] = '�����������';
			return $result;
		}

		//������÷�Χ
		$param_info = $use_param_info;
		$param_info['channel_module'] = $channel_module;
		$param_info['scope_scene'] = 'use';
		$param_info['channel_gid'] = $channel_gid;
		$param_info['prices_type_id'] = $prices_type_id;
		$param_info['goods_prices'] = $goods_prices;
		$param_info['quantity'] = $quantity;
		$check_rst = $this->check_promotion_scope($buyer_user_id, $promotion_info, $param_info);
		if( $check_rst['result']!=1 )
		{
			$result['result'] = -4;
			$result['message'] = $check_rst['message'];
			return $result;
		}

		//����û��޹�
		$cal_quantity_user_info = $this->cal_quantity_user_info($promotion_info, $buyer_user_id, $promotion_info['quantity_user_type'], $channel_module, $channel_gid, $prices_type_id);
		if( $cal_quantity_user_info['remain_quantity']<$quantity )
		{
			$result['result'] = -5;
			$result['message'] = '�����޹�����';
			return $result;
		}

		//���SKU���
		$cal_quantity_sku_info = $this->cal_quantity_sku_info($promotion_info, $channel_module, $channel_gid, $prices_type_id);
		if( $cal_quantity_sku_info['remain_quantity']<$quantity )
		{
			$result['result'] = -6;
			$result['message'] = '������治��';
			return $result;
		}

		//����ʹ�ý��
		$cal_info = array(
			'order_total_amount' => $order_total_amount, //4��������õ�
			'goods_prices' => $goods_prices, //1���۴�����2��ʱ�ۿۡ�3��ʱ�ͼۣ��õ�
		);
		$promotion_amount = $this->cal_promotion_amount($promotion_info, $cal_info);
		if( $promotion_amount<=0 )
		{
			$result['result'] = -7;
			$result['message'] = '�˴���������';
			return $result;
		}
		$used_amount = $promotion_amount * $quantity;

		$cur_time = time();	//��ǰʱ��

		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//��ʼ���޹�����
		$rst = $this->init_quantity_user($promotion_id, $buyer_user_id, $promotion_info['quantity_user_type'], $channel_module, $channel_gid, $prices_type_id, $promotion_info['default_quantity_user']);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -8;
			$result['message'] = '��ʼ���޹�����ʧ��';
			return $result;
		}
		
		//����ʵ������
		$rst = $this->increase_real_quantity_user($promotion_id, $buyer_user_id, $promotion_info['quantity_user_type'], $channel_module, $channel_gid, $prices_type_id, $quantity, $quantity);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -9;
			$result['message'] = '�����޹�����';
			return $result;
		}

		//��ʼ��SKU����
		$rst = $this->init_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $promotion_info['default_quantity_sku']);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -10;
			$result['message'] = '��ʼ���������ʧ��';
			return $result;
		}

		//����ʵ������
		$rst = $this->increase_real_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $quantity);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -11;
			$result['message'] = '������治��';
			return $result;
		}

		//�����������
		$ref_data = array(
			'promotion_id' => $promotion_id,
			'cate_id' => $promotion_info['cate_id'], //��������id
			'type_id' => $promotion_info['type_id'],
			'type_target' => $promotion_info['type_target'],
			'channel_module' => $channel_module,
			'channel_oid' => 0,
			'channel_did' => 0, //��ϸ
			'channel_gid' => $channel_gid,
			'prices_type_id' => $prices_type_id,
			'quantity' => $quantity,
			'quantity_user_type' => $promotion_info['quantity_user_type'],
			'buyer_user_id' => $buyer_user_id,
			'is_need_cash' => $promotion_info['is_need_cash'],
			'need_cash_rate' => $promotion_info['need_cash_rate'],
			'need_cash_max' => $promotion_info['need_cash_max'],
			'face_value' => $promotion_info['face_value'],
			'face_max' => $promotion_info['face_max'],
			'promotion_amount' => $promotion_amount,
			'used_amount' => $used_amount, //ʹ��ʱ�ֿ۵Ľ��
			'add_time' => $cur_time,
		);
		$id = $this->add_ref_order($ref_data);
		if( $id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -12;
			$result['message'] = 'ʹ�ô���ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['promotion_ref_id'] = $id;
		$result['promotion_amount'] = number_format($promotion_amount, 2, '.', '');
		return $result;
	}

	/**
	 * �˻�����
	 * @param string $channel_module ����ģ�飬mall_orderԼ�ģ�waipai����
	 * @param int $channel_oid ��������ID
	 * @return array
	 */
	public function refund_promotion_by_oid($channel_module, $channel_oid)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$channel_module = trim($channel_module);
		$channel_oid = intval($channel_oid);
		if( strlen($channel_module)<1 || $channel_oid<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ����������Ϣ
		$ref_order_list = $this->get_ref_order_list_by_oid($channel_module, $channel_oid);
		if( empty($ref_order_list) )
		{
			$result['result'] = 1;
			$result['message'] = '�˵�û��ʹ�ô���';
			return $result;
		}

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//ѭ������
		foreach ($ref_order_list as $ref_order_info)
		{
			$refund_ret = $this->refund_promotion($ref_order_info);
			if( $refund_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -3;
				$result['message'] = $refund_ret['message'];
				return $result;
			}
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * �˻�����
	 * @param array $ref_order_info
	 * @return array
	 */
	private function refund_promotion($ref_order_info)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		if( !is_array($ref_order_info) || empty($ref_order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -2;
			$result['message'] = '�޷��˻�';
			return $result;
		}
		$id = intval($ref_order_info['id']);
		$promotion_id = intval($ref_order_info['promotion_id']);
		$channel_module = trim($ref_order_info['channel_module']);
		$channel_gid = intval($ref_order_info['channel_gid']);
		$prices_type_id = trim($ref_order_info['prices_type_id']);
		$quantity = intval($ref_order_info['quantity']);
		$buyer_user_id =intval($ref_order_info['buyer_user_id']);
		$quantity_user_type = intval($ref_order_info['quantity_user_type']);

		$cur_time = time(); //��ǰʱ��

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//�˻��û��޹�����
		$rst = $this->decrease_real_quantity_user($promotion_id, $buyer_user_id, $quantity_user_type, $channel_module, $channel_gid, $prices_type_id, $quantity);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -3;
			$result['message'] = '�˻��޹�����ʧ��';
			return $result;
		}

		//�˻����
		$rst = $this->decrease_real_quantity_sku($promotion_id, $channel_module, $channel_gid, $prices_type_id, $quantity);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '�˻��������ʧ��';
			return $result;
		}

		//�������˻�
		$rst = $this->update_ref_order_refund($id, array('refund_time'=>$cur_time));
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = '�˻�ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * ���ִ���
	 * @param int $id
	 * @param double $cash_amount
	 * @param array $more_info array('seller_user_id'=>0, 'org_user_id'=>0, 'need_amount'=>0.00, 'org_amount'=>0.00, 'subject'=>'')
	 * @return array
	 */
	public function cash_promotion($id, $cash_amount, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$id = intval($id);
		$cash_amount = number_format($cash_amount*1, 2, '.', '')*1;
		$seller_user_id = intval($more_info['seller_user_id']);
		$org_user_id = intval($more_info['org_user_id']);
		$need_amount = number_format($more_info['need_amount']*1, 2, '.', '')*1;
		$org_amount = number_format($more_info['org_amount']*1, 2, '.', '')*1;
		$seller_amount = bcsub($cash_amount, $org_amount, 2);	//�����߶��ֽ����ֽ�� ��ȥ �������ֽ��
		$subject = trim($more_info['subject']);
		if( $id<1 || $cash_amount<0 || $need_amount<0 || $org_amount<0 || $seller_amount<0 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_cash']==1 )
		{
			$result['result'] = -3;
			$result['message'] = '�����޷�����';
			return $result;
		}
		
		$cur_time = time();
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����Ѷ���
		$data = array(
			'subject' => $subject,
			'seller_user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'need_amount' => $need_amount,
			'seller_amount' => $seller_amount,
			'org_amount' => $org_amount,
			'cash_time' => $cur_time,
		);
		$ret = $this->update_ref_order_cash($id, $cash_amount, $data);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * �������
	 * @param int $id
	 * @return array
	 */
	public function settle_promotion($id)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$id = intval($id);
		if( $id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ����������Ϣ
		$ref_order_info = $this->get_ref_order_info($id);
		if( empty($ref_order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '������';
			return $result;
		}
		if( $ref_order_info['is_refund']==1 || $ref_order_info['is_settle']==1 || $ref_order_info['is_cash']==0 )
		{
			$result['result'] = -3;
			$result['message'] = '�����޷�����';
			return $result;
		}
		
		$cur_time = time();
	
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//�����ѽ���
		$ret = $this->update_ref_order_settle($id, array('settle_time'=>$cur_time));
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -4;
			$result['message'] = 'ʧ��';
			return $result;
		}
		
		//�����ύ
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
}

?>