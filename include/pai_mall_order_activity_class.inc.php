<?php
/**
 * 活动订单类
 * 
 * @author
 */

class pai_mall_order_activity_class extends POCO_TDG
{
	/**
	 * 渠道模块
	 * @var string
	 */
	private $channel_module = 'mall_order';
	
	/**
	 * 待支付
	 * 买家已下单，等待买家支付
	 * @var int
	 */
	const STATUS_WAIT_PAY = 0;
	
	/**
	 * 待签到
	 * 卖家已确认，等待买家签到
	 * @var int
	 */
	const STATUS_WAIT_SIGN = 2;
	
	/**
	 * 已关闭
	 * @var int
	 */
	const STATUS_CLOSED = 7;
	
	/**
	 * 已完成
	 * @var int
	 */
	const STATUS_SUCCESS = 8;

	/**
     * 允许消费者申请退款时间（小时）
     * @var int
	 */
	const ALLOW_BUYER_REFUND_TIME = 24;

	/**
	 * 状态名称
	 * @var array
	 */
	private $status_str_arr = array(
		0 => '待支付',
		2 => '待签到',
		7 => '已关闭',
		8 => '已完成',
	);
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}

	/**
	 * 指定表
	 */
	private function set_mall_order_tbl()
	{
		$this->setTableName('mall_order_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_mall_order_activity_tbl()
	{
		$this->setTableName('mall_order_activity_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_mall_order_code_tbl()
	{
		$this->setTableName('mall_order_code_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_mall_order_process_tbl()
	{
		$this->setTableName('mall_order_process_tbl');
	}

	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_order($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 修改
	 * @param array $data
	 * @param int $order_id
	 * @return boolean
	 */
	private function update_order($data, $order_id)
	{
		$order_id = intval($order_id);
		if( !is_array($data) || empty($data) || $order_id<1 )
		{
			return false;
		}
		$this->set_mall_order_tbl();
		$affected_rows = $this->update($data, "order_id={$order_id}");
		return $affected_rows>0 ? true : false;
	}

	/**
	 * 修改订单主信息
	 * @param array $data
	 * @param string $where_str
	 * @return boolean
	 */
	private function update_order_by_where($data, $where_str)
	{
		$where_str = trim($where_str);
		if( !is_array($data) || empty($data) || strlen($where_str)<1 )
		{
			return false;
		}
		$this->set_mall_order_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0 ? true : false;
	}

	/**
	 * 获取信息
	 * @param int $order_id
	 * @return array
	 */
	public function get_order_info_by_id($order_id)
	{
		$order_id = intval($order_id);
		if( $order_id<1 )
		{
			return array();
		}
		$this->set_mall_order_tbl();
		return $this->find("order_id={$order_id}");
	}

	/**
	 * 获取信息
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_info($order_sn)
	{
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			return array();
		}
		$where_str = 'order_sn=:x_order_sn';
		sqlSetParam($where_str, 'x_order_sn', $order_sn);
		$this->set_mall_order_tbl();
		return $this->find($where_str);
	}
	
	/**
	 * 添加订单活动
	 * @param array $data
	 * @return int
	 */
	private function add_order_activity($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_activity_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 修改订单活动信息（activity）
	 * @param array $data
	 * @param string $where_str
	 * @return boolean
	 */
	private function update_order_activity_by_where($data, $where_str)
	{
		$where_str = trim($where_str);
		if( !is_array($data) || empty($data) || strlen($where_str)<1 )
		{
			return false;
		}
		$this->set_mall_order_activity_tbl();
		$affected_rows = $this->update($data, $where_str);
		return $affected_rows>0 ? true : false;
	}

	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_code($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_code_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 更新已签到
	 * @param int $code_id
	 * @param array $more_info array('check_time'=>0)
	 * @return boolean
	 */
	private function update_code_check($code_id, $more_info=array())
	{
		$code_id = intval($code_id);
		if( $code_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_check' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_mall_order_code_tbl();
		$affected_rows = $this->update($data, "code_id={$code_id} AND is_check=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * 释放未签到二维码资源（订单取消退款时）
	 * @param int $code_id
	 * @param array $more_info array('check_time'=>0)
	 * @return boolean
	 */
	private function release_code($code_id, $more_info=array())
	{
		$code_id = intval($code_id);
		if( $code_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_check' => 2,
		);
		$data = array_merge($more_info, $data);
		$this->set_mall_order_code_tbl();
		$affected_rows = $this->update($data, "code_id={$code_id} AND is_check=0");
		return $affected_rows>0?true:false;
	}

	/**
	 * 检查签到码是否能复用
	 * @param string $code_sn
	 * @return int
	 */
	private function check_code_recently($code_sn)
	{
		$code_sn = trim($code_sn);

		$where_str = 'code_sn=:x_code_sn';
		sqlSetParam($where_str, 'x_code_sn', $code_sn);

		$check_time = time() + 90*24*3600; //最近90天
		$where_str .= " AND (is_check=0 OR (is_check=1 AND check_time>={$check_time}))";

		return $this->get_code_list(0, true, $where_str);
	}

	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function add_process($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_process_tbl();
		return $this->insert($data, 'IGNORE');
	}

	/**
	 * 通过activity_id获取订单列表
	 * @param  array   $activity_ids   活动id列表
	 * @param  int     $status         订单状态
	 * @param  boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param  string  $order_by       排序
	 * @param  string  $limit          查询条数
	 * @param  string  $fields         查询字段
	 * @return array
	 */
	public function get_order_list_by_activity_ids($type_id, $status, $activity_ids, $b_select_count=false, $where_str, $order_by='d.order_id', $limit='0,20', $fields='o.*')
	{
		$type_id = intval($type_id);
		$status = intval($status);
		if( !is_array($activity_ids) )
		{
			return array();
		}
		$activity_ids = implode(',',$activity_ids);

		//整理查询条件
		$sql_where = ' WHERE 1';
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $status>=0 )
		{
			$sql_where .= " AND status={$status}";
		}
		$where_str .= " AND d.activity_id IN ({$activity_ids}) ";
		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		//TODO 这里有问题
		if( $b_select_count == true )
		{
            $sql = "SELECT count(*) as total,sum(total_amount) as order_amount "
					. " FROM {$this->_db_name}.mall_order_activity_tbl as d"
					. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
					. " ON d.order_id=o.order_id"
                    . " {$sql_where}";
            $rs = $this->query($sql);
            return $rs[0];
		}

		$sql = "SELECT {$fields}"
				. " FROM {$this->_db_name}.mall_order_activity_tbl as d"
				. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
				. " ON d.order_id=o.order_id"
				. " {$sql_where} order by {$order_by} desc limit {$limit}";
		$ret = $this->query($sql);

	    return $ret;
	}

	/**
	 * 过期提示
	 * @param $status
	 * @param $activity_list
	 * @param $cur_time
	 * @return string
	 */
	public function get_expire_str($status, $activity_list, $cur_time)
	{
		switch ($status)
		{
			case self::STATUS_WAIT_PAY:
				$expire_str = '下单后1小时不支付订单将自动关闭';
				break;
			case self::STATUS_WAIT_SIGN:
				if( $activity_list[0]['service_start_time']<=$cur_time )
				{
					$expire_str = '活动开始前24小时内将不能申请退款';
				}
				else
				{
					$expire_str = '活动结束后48小时不签到将自动到账商家账户';
				}
				break;
			default:
				$expire_str = '';
				break;
		}
		return $expire_str;
	}
	
	/**
	 * 获取活动
	 * @param int $order_activity_id
	 * @return array
	 */
	public function get_activity_info($order_activity_id)
	{
		$order_activity_id = intval($order_activity_id);
		if( $order_activity_id<1 )
		{
			return array();
		}
		$this->set_mall_order_activity_tbl();
		return $this->find("order_activity_id={$order_activity_id}");
	}
	
	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_activity_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);
		
		//整理查询条件
		$sql_where = '';
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//查询
		$this->set_mall_order_activity_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @return array
	 */
	public function get_activity_list_all($order_id)
	{
		return $this->get_activity_list($order_id, false, '', 'order_activity_id ASC', '0,99999999');
	}

	/**
	 * 根据订单获取活动场次
	 * @param int $user_id 商家ID
	 * @param int $status 状态（订单状态）可不传
	 * @param boolean $b_select_count 是否计算总数（默认false不计算）
	 * @param string $oder_by 排序（此处默认以最新下单，不必要传）
	 * @param string $limit 返回条数（默认0-20）
	 * @return array
	 */
	public function get_activity_list_by_order_for_seller($user_id, $status, $b_select_count=false, $order_by, $limit)
	{
		$user_id = intval($user_id);
		$status = intval($status);
		$order_by = trim($order_by);
		$limit = trim($limit);
		$type_id = 42;
		if( $user_id<1 )
		{
			return array();
		}

		//整理查询条件
		$sql_where = " WHERE order_type='activity' AND o.seller_user_id={$user_id} AND o.type_id={$type_id} ";
		if( $status>0 )
		{
			$sql_where .= " AND o.status={$status}";
		}

		$fields = ' a.activity_id,a.activity_images,a.service_start_time,a.service_end_time,a.stage_id,a.prices_type_id ';
		if( $b_select_count )
		{
			$fields = 'count(*) as count';
		}
		if( strlen($order_by)<1 )
		{
			$order_by = ' o.add_time';
		}
		if( strlen($limit)<1 )
		{
			$limit = '0,20';
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_tbl as o"
			. " LEFT JOIN {$this->_db_name}.mall_order_activity_tbl as a"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " GROUP BY a.activity_id,a.stage_id"
			. " ORDER BY {$order_by}"
			. " LIMIT {$limit}";
		/**
		 * 以后测试效率可以调换试一下
		 * SELECT a.activity_id,a.activity_name,a.stage_id,a.stage_title
		 *	FROM mall_db.mall_order_activity_tbl as a
		 *	LEFT JOIN mall_db.mall_order_tbl as o
		 *	ON a.order_id=o.order_id
		 *	WHERE o.type_id=
		 *	AND o.seller_user_id=
		 *	AND order_type='activity'
		 *	GROUP BY a.activity_id,a.stage_id
		 *	ORDER BY o.add_time
		 *	LIMIT 0,20
		 */
		$activity_list = $this->query($sql);
		if( $b_select_count )
		{
			return intval($activity_list[0]);
		}
		foreach($activity_list as $key=>$activity_info)
		{
			$attend_num = $this->get_order_list_of_paid_by_stage($activity_info['activity_id'],$activity_info['stage_id'],true);
			$activity_info['attend_num'] = $attend_num;
			$activity_list[$key] = $activity_info;
		}
		return $this->fill_activity_by_order_full_list($activity_list);
	}

	/**
	 * 获取活动场次订单
	 * @param $activity_id
	 * @param $stage_id
	 * @param $type_id
	 * @param $status
	 * @param bool $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|mixed
	 * @throws App_Exception
	 */
	public function get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20')
	{
		$activity_id = intval($activity_id);
		$stage_id = intval($stage_id);
		$limit = trim($limit);
		$status = intval($status);
		$where_str = trim($where_str);
		if( $activity_id<1 || $stage_id<1 )
		{
			return array();
		}
		//整理查询条件
		$sql_where = " WHERE a.activity_id={$activity_id} AND a.stage_id={$stage_id}";
		if( $status>=0 )
		{
			$sql_where .= " AND o.status={$status}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		$fields = ' * ';
		if( $b_select_count )
		{
			$fields = 'count(*) as count';
		}
		if( strlen($order_by)<1 )
		{
			$order_by = ' o.add_time';
		}
		if( strlen($limit)<1 )
		{
			$limit = '0,20';
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " ORDER BY {$order_by}"
			. " LIMIT {$limit}";
		$ret = $this->query($sql);
		if( $b_select_count )
		{
			return intval($ret[0]['count']);
		}
		return $this->fill_activity_by_order_full_list($ret);
	}

	/**
	 * 获取已支付订单列表（报名名单）
	 * @param $activity_id
	 * @param $stage_id
	 * @param bool $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|mixed
	 */
	public function get_order_list_of_paid_by_stage($activity_id, $stage_id, $b_select_count=false, $order_by='', $limit='0,20')
	{
		$status =-1;
		$where_str = "status IN (" . self::STATUS_WAIT_SIGN .",".self::STATUS_SUCCESS.") AND is_pay=1";
		return $this->get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count, $where_str, $order_by, $limit);
	}

	/**
	 * 获取活动所有场次已支付（不包含取消）数量
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_activity($activity_id)
	{
		$activity_id = intval($activity_id);
		if( $activity_id<1 )
		{
			return array();
		}
		$fields = ' SUM(a.quantity) AS paid_num ';
		//整理查询条件
		$sql_where = " WHERE a.activity_id={$activity_id} AND status IN (" . self::STATUS_WAIT_SIGN .",".self::STATUS_SUCCESS.") AND is_pay=1";

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " LIMIT 0,1";

		$res = $this->query($sql);
		return $res[0];
	}

	/**
	 * 获取场次已支付（不包含取消）数量
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_stage($activity_id, $stage_id)
	{
		$activity_id = intval($activity_id);
		$stage_id = intval($stage_id);
		if( $activity_id<1 || $stage_id<1 )
		{
			return array();
		}
		$fields = ' SUM(a.quantity) AS paid_num ';
		//整理查询条件
		$sql_where = " WHERE a.activity_id={$activity_id} AND a.stage_id={$stage_id} AND status IN (" . self::STATUS_WAIT_SIGN .",".self::STATUS_SUCCESS.") AND is_pay=1";

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " LIMIT 0,1";

		$res = $this->query($sql);
		return $res[0];
	}

	/**
	 * 根据活动ID获取某个买家是否有某种状态的单
	 * @param int $user_id 消费者ID
	 * @param int $activity_id 活动ID
	 * @param bool $b_select_count 计算数量
	 * @param int $is_fill_order 是否填充详细数据
	 * @return array|mixed
	 * @throws App_Exception
	 */
	public function get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $is_fill_order=0)
	{
		$user_id = intval($user_id);
		$activity_id = intval($activity_id);
		$status = intval($status);
		$limit = trim($limit);
		$order_by = trim($order_by);
		if( $activity_id<1 || $user_id<1 )
		{
			return array();
		}

		$fields = ' a.activity_id,a.activity_images,a.service_start_time,a.service_end_time,a.stage_id,a.prices_type_id,o.seller_user_id,o.buyer_user_id,SUM(a.quantity) AS attend_num ';
		if( $b_select_count )
		{
			$fields = 'count(*) AS order_num';
		}
		if( strlen($order_by)<1 )
		{
			$order_by = ' o.add_time';
		}
		if( strlen($limit)<1 )
		{
			$limit = '0,20';
		}
		//整理查询条件
		$sql_where = " WHERE a.activity_id={$activity_id} AND o.buyer_user_id={$user_id}";
		if( $status>=0 )
		{
			$sql_where .= " AND o.status={$status}";
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " ORDER BY {$order_by}"
			. " LIMIT {$limit}";
		$ret = $this->query($sql);
		if( $is_fill_order==1 )
		{
			return $this->fill_activity_by_order_full_list($ret);
		}
		return $ret[0];
	}

	/**
	 * 获取卖家场次订单待办项目数量
	 * @param int $user_id 卖家用户ID
	 * @param int $activity_id 活动ID
	 * @param int $stage_id 场次ID
	 * @return array
	 */
	public function get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$user_id = intval($user_id);
		$activity_id = intval($activity_id);
		$stage_id = intval($stage_id);
		if( $user_id<1 || $activity_id<1 || $stage_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$fields = ' status,COUNT(*) as c ';
		$sql_where = " WHERE o.seller_user_id={$user_id} AND o.is_seller_del=0 AND a.activity_id={$activity_id} AND a.stage_id={$stage_id}";

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$sql_where}"
			. " GROUP BY status";
		$ret = $this->query($sql);

		$comment_fields = 'COUNT(*) as c';
		$comment_where_str = $sql_where." AND is_seller_comment=0 AND status=8";
		$comment_sql = "SELECT {$comment_fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as a"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON o.order_id=a.order_id"
			. " {$comment_where_str}";
		$comment_ret = $this->query($comment_sql);

		$ret_tmp = array();
		foreach ( $ret as $key => $info )
		{
			switch ( $info['status'] )
			{
				case 0:
					$key = 'wait_pay';
					break;
				case 1:
					$key = 'wait_confirm';
					break;
				case 2:
					$key = 'wait_sign';
					break;
				case 7:
					$key = 'closed';
					break;
				case 8:
					$key = 'success';
					break;
				default:
					// $key = 'all';
					break;
			}
			$ret_tmp[$key] = $info['c'];
		}

		$count = intval($ret_tmp['wait_pay'])+intval($ret_tmp['wait_sign'])+intval($ret_tmp['wait_confirm'])+intval($comment_ret[0]['c']);

		$res = array(
			'wait_pay' => isset($ret_tmp['wait_pay']) ? $ret_tmp['wait_pay'] : 0,
			'wait_confirm' => isset($ret_tmp['wait_confirm']) ? $ret_tmp['wait_confirm'] : 0,
			'wait_sign' => isset($ret_tmp['wait_sign']) ? $ret_tmp['wait_sign'] : 0,
			'wait_comment' => intval($comment_ret[0]['c']),
			'all' => $count,
		);

		return $res;
	}

	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	public function fill_order_full_list($list, $login_user_id=0)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		$login_user_id = intval($login_user_id);
		$cur_time = time();

		//获取系统昵称、系统头像
		$sys_nickname = get_user_nickname_by_user_id(10002);
		$sys_icon = get_user_icon(10002, 165);

		foreach($list as $key=>$info)
		{
			//订单状态
			$status_tmp = intval($info['status']);
			if( array_key_exists($status_tmp, $this->status_str_arr) )
			{
				$status_str_tmp = $this->status_str_arr[$status_tmp];
			}
			else
			{
				$status_str_tmp ='未知状态';
			}
			$info['status_str'] = $status_str_tmp;

			$info['status_str2'] = $status_str_tmp;
			if( $status_tmp==self::STATUS_SUCCESS )
			{
				$info['status_str2'] = '已正常完成';
				if( $info['sign_by']=='sys' )
				{
					$info['status_str2'] = '已超时完成';
				}
			}
			if( $status_tmp==self::STATUS_CLOSED )
			{
				$info['status_str2'] = '已关闭';
				if( intval($info['is_pay'])==1 )
				{
					$info['status_str2'] = '已退款';
				}
			}

			//支付时间
			$pay_time_str = '--';
			if( $info['is_pay']==1 )
			{
				$pay_time_str = date('Y.m.d H:i', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;

			//获取买家名称
			$info['buyer_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_icon'] = get_user_icon($info['buyer_user_id'], 165);

			//获取卖家名称
			$info['seller_name'] = get_seller_nickname_by_user_id($info['seller_user_id']);
			$info['seller_icon'] = get_seller_user_icon($info['seller_user_id'], 165);

			//订单详细列表
			$activity_list = $this->get_activity_list_all($info['order_id']);

			//获取促销信息
			$is_activity_promotion = intval($activity_list[0]['is_activity_promotion']);
			$activity_promotion_id = intval($activity_list[0]['activity_promotion_id']);
			$activity_promotion_amount = $activity_list[0]['activity_promotion_amount'];
			$activity_promotion_info = array();
			$original_amount_str = '';
			if( $is_activity_promotion==1 )
			{
				$activity_promotion_rst = POCO::singleton('pai_promotion_class')->get_promotion_full_info($activity_promotion_id);
				$activity_promotion_info['type_name'] = $activity_promotion_rst['type_name'];
				$activity_promotion_info['start_time'] = date('Y.m.d', $activity_promotion_rst['start_time']);
				$activity_promotion_info['end_time'] = date('Y.m.d', $activity_promotion_rst['end_time']);
				$activity_promotion_info['promotion_desc'] = $activity_promotion_rst['promotion_desc'];
				$activity_promotion_used_amount = number_format(($activity_promotion_amount * intval($activity_list[0]['quantity'])), 2, '.', '');
				$activity_promotion_info['cal_used_amount'] = '￥' . $activity_promotion_used_amount;
				if( $info['is_change_price']==1 ) //有改价
				{
					$original_amount_str = '促销价￥'.$info['original_amount'];
				}
				else //没改价
				{
					$original_amount_str = $activity_list[0]['prime_prices'] * intval($activity_list[0]['quantity']);
					$original_amount_str = '原价￥' . number_format($original_amount_str, 2, '.', '');
				}
			}
			else
			{
				if( $info['is_change_price']==1 ) //有改价
				{
					$original_amount_str = $activity_list[0]['prime_prices'] * intval($activity_list[0]['quantity']);
					$original_amount_str = '原价￥' . number_format($original_amount_str, 2, '.', '');
				}
				else //没改价
				{
					$original_amount_str = '';
				}
			}
			$info['activity_promotion_info'] = $activity_promotion_info;
			$info['original_amount_str'] = $original_amount_str;

			$activity_info = $this->get_activity_prices_info($activity_list[0]);
			//将活动名称和图片提出一层到order主数组信息（目前主要是消费者版用）
			$info['activity_name'] = $activity_info['activity_name'];
			$info['activity_images'] = $activity_info['activity_images'];
			$activity_list = $this->get_activity_list_all($info['order_id']);
			foreach( $activity_list as $activity_key=>$activity_info_tmp )
			{
				$activity_info_tmp['is_official'] = $activity_info['is_official'];
				$activity_list[$activity_key] = $activity_info_tmp;
			}

			$info['activity_list'] = $activity_list;
			$info['expire_str'] = $this->get_expire_str($status_tmp, $activity_list, $cur_time);

			//订单签到码列表
			$code_list = $this->get_code_list_all($info['order_id']);
			foreach($code_list as $code_key=>$code_info)
			{
				$hash = qrcode_hash($info['order_id'], $info['order_id'], $code_info['code_sn']);
				$code_info['qr_code_url'] = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$info['order_id']}&enroll_id={$info['order_id']}&code={$code_info['code_sn']}&hash={$hash}";
				$code_info['name'] = '签到码';
				$code_list[$code_key] = $code_info;
			}
			$info['code_list'] = $code_list;

			//订单状态过程列表
			$process_list = $this->get_process_list_all($info['order_id'], 'process_id DESC');
			foreach($process_list as $process_key=>$process_info)
			{
				$process_nickname = '';
				$process_icon = '';
				if( $process_info['process_by']=='seller' )
				{
					$process_nickname = $info['seller_name'];
					$process_icon = $info['seller_icon'];
				}
				elseif( $process_info['process_by']=='buyer' )
				{
					$process_nickname = $info['buyer_name'];
					$process_icon = $info['buyer_icon'];
				}
				elseif( $process_info['process_by']=='sys' )
				{
					$process_nickname = $sys_nickname;
					$process_icon = $sys_icon;
				}

				$process_content_tmp = trim($process_info['process_content']);
				if( $login_user_id==$info['buyer_user_id'] )
				{
					$process_content_tmp = str_replace("{buyer_nickname}", "您", $process_content_tmp);
				}
				elseif( $login_user_id==$info['seller_user_id'] )
				{
					$process_content_tmp = str_replace("{seller_nickname}", "您", $process_content_tmp);
				}
				$process_content_tmp = str_replace("{seller_nickname}", $info['seller_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{buyer_nickname}", $info['buyer_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{sys_nickname}", $sys_nickname, $process_content_tmp);

				//评价星数、评价内容
				$overall_score_arr_tmp = array();
				$process_remark_tmp = '';
				$comment_info_tmp = '';
				if( $process_info['process_action']=='评价' )
				{
					$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
					if( $process_info['process_by']=='seller' )
					{
						/*
						 * 获取商家对消费者订单的商品评价
						* @param int $order_id
						* @param int $goods_id
						* @return array
						*/
						$comment_info_tmp = $mall_comment_obj->get_buyer_comment_info($info['order_id'], 0);
					}
					elseif( $process_info['process_by']=='buyer' )
					{
						/*
						 * 获取消费者对商家订单的商品评价
						* @param int $order_id
						* @param int $goods_id
						* @return array
						*/
						$comment_info_tmp = $mall_comment_obj->get_seller_comment_info($info['order_id'], 0);
					}
					if( $comment_info_tmp['overall_score']>0 )
					{
						$overall_score_arr_tmp = $mall_comment_obj->format_comment_score($comment_info_tmp['overall_score']);
					}
					$process_remark_tmp = trim($comment_info_tmp['comment']);
				}

				$process_info['process_nickname'] = $process_nickname;
				$process_info['process_icon'] = $process_icon;
				$process_info['process_time_str'] = date('Y.m.d H:i', $process_info['process_time']);
				$process_info['process_content'] = $process_content_tmp;
				$process_info['process_remark'] = $process_remark_tmp;
				$process_info['overall_score_arr'] = $overall_score_arr_tmp;
				$process_list[$process_key] = $process_info;
			}
			$info['process_list'] = $process_list;

			$list[$key] = $info;
		}
		return $list;
	}


	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	private function fill_activity_by_order_full_list($list, $login_user_id=0)
	{
		if( !is_array($list) )
		{
			return $list;
		}

		foreach($list as $key=>$info)
		{
			//订单状态
			$status_tmp = intval($info['status']);
			if( array_key_exists($status_tmp, $this->status_str_arr) )
			{
				$status_str_tmp = $this->status_str_arr[$status_tmp];
			}
			else
			{
				$status_str_tmp ='未知状态';
			}
			$info['status_str'] = $status_str_tmp;

			$info['status_str2'] = $status_str_tmp;
			if( $status_tmp==self::STATUS_SUCCESS )
			{
				$info['status_str2'] = '已正常完成';
				if( $info['sign_by']=='sys' )
				{
					$info['status_str2'] = '已超时完成';
				}
			}
			if( $status_tmp==self::STATUS_CLOSED )
			{
				$info['status_str2'] = '已关闭';
				if( intval($info['is_pay'])==1 )
				{
					$info['status_str2'] = '已退款';
				}
			}

			//支付时间
			$pay_time_str = '--';
			if( $info['is_pay']==1 )
			{
				$pay_time_str = date('Y.m.d H:i', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;

			$activity_info = $this->get_activity_prices_info($info);
			//将活动名称和图片提出一层到order主数组信息（目前主要是消费者版用）
			$info['activity_name'] = $activity_info['activity_name'];
			$info['activity_images'] = $activity_info['activity_images'];
			$info['stage_min_price'] = $activity_info['min_price'];
			$info['stage_max_price'] = $activity_info['max_price'];
			$info['stage_title'] = $activity_info['stage_title'];
			$info['service_start_time'] = $activity_info['service_start_time'];
			$info['service_end_time'] = $activity_info['service_end_time'];
			$info['stock_num_total'] = $activity_info['stock_num_total'];
			$info['prices_spec'] = $activity_info['prices_spec'];
			$info['buyer_user_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_user_cellphone'] = POCO::singleton('pai_user_class')->get_phone_by_user_id($info['buyer_user_id']);
			$info['buyer_user_icon'] = get_user_icon($info['buyer_user_id'], 165);

			$list[$key] = $info;
		}
		return $list;
	}

	/**
	 * 获取信息
	 * @param string $code_sn
	 * @return array
	 */
	public function get_code_info_recently($code_sn)
	{
		$code_sn = trim($code_sn);
		if( strlen($code_sn)<1 )
		{
			return array();
		}
		$where_str = 'code_sn=:x_code_sn';
		sqlSetParam($where_str, 'x_code_sn', $code_sn);
		$this->set_mall_order_code_tbl();
		return $this->find($where_str, 'code_id DESC');
	}

	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @return array
	 */
	public function get_code_list_all($order_id)
	{
		return $this->get_code_list($order_id, false, '', 'code_id ASC', '0,99999999');
	}

	/**
	 * 获取列表
	 * @param int $order_id
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_code_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);

		//整理查询条件
		$sql_where = '';
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//查询
		$this->set_mall_order_code_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_process_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);

		//整理查询条件
		$sql_where = '';
		if( $order_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_id={$order_id}";
		}
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//查询
		$this->set_mall_order_process_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @param string $order_by
	 * @return array
	 */
	public function get_process_list_all($order_id, $order_by='process_id ASC')
	{
		return $this->get_process_list($order_id, false, '', $order_by, '0,99999999');
	}

	/**
	 * 获取过期秒数
	 * @param int $status
	 * @param int $type_id
	 * @return int
	 */
	private function get_expire_seconds($status, $login_user_id=0)
	{
		$expire_seconds = 0;

		$status = intval($status);
		if( $status===self::STATUS_WAIT_PAY ) //待支付
		{
			$expire_seconds = 1*3600; //默认1小时
//			$expire_seconds = 300; //为方便测试，临时5分钟
		}
		elseif( $status===self::STATUS_WAIT_SIGN ) //待签到
		{
			$expire_seconds = 48*3600; //默认48小时
		}

		if( $login_user_id==131188 )
		{
			$expire_seconds=300;
		}
		return $expire_seconds;
	}

	/**
	 * 不使用优惠券
	 * @param array $order_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	private function not_use_order_coupon($order_info)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);

		//检查参数
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$is_pay = intval($order_info['is_pay']);

		//检查订单
		if( $is_pay!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '支付状态错误';
			return $result;
		}
		if( $is_use_coupon==0 )
		{
			$result['result'] = 1;
			$result['message'] = '此单没有使用券';
			return $result;
		}

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//不使用优惠券
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$not_use_ret = $coupon_obj->not_use_coupon_by_oid($this->channel_module, $order_id);
		if( $not_use_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -3;
			$result['message'] = $not_use_ret['message'];
			return $result;
		}

		//更新为0
		$data = array(
			'discount_amount' => 0,
			'is_use_coupon' => 0,
			'coupon_sn' => '',
			'pending_amount' => $total_amount,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=1 AND is_pay=0 AND total_amount={$total_amount}");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '更新不使用优惠失败';
			return $result;
		}

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['used_amount'] = $not_use_ret['used_amount'];
		return $result;
	}

	/**
	 * 提交订单
	 * @param int $buyer_user_id 买家用户ID
	 * @param array $activity_list 活动活动列表
	 * @param array $more_info 更多信息
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 *
	 * $activity_list = array( array(
	 * 	'activity_id' => 0, //活动ID
	 * 	'prices_type_id' => '',
	 *  'service_people' => 0,
	 *  'prices' => 0, //单价，特殊服务必填，正常服务忽略
	 * 	'quantity' => 0, //数量
	 *  'activity_promotion_id' => 0, //活动促销ID
	 * ), ... );
	 *
	 * $more_info = array(
	 * 	'description' => '', //描述、备注
	 *  'is_auto_sign' => 0, //是否自动签到，签到、评价不发送通知
	 *  'referer' => '', //订单来源，app weixin pc wap oa
	 * );
	 *
	 */
	public function submit_order($buyer_user_id, $activity_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'');
		
		//检查参数
		$buyer_user_id = intval($buyer_user_id);
		if( $buyer_user_id<1 || !is_array($activity_list) || count($activity_list)!=1 || !is_array($activity_list[0]) || empty($activity_list[0]) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$activity_info = $activity_list[0];
		$activity_id = intval($activity_info['activity_id']);
		$activity_stage_id = intval($activity_info['stage_id']);//场次ID，stage_id不能为小于0
		$activity_prices_type_id = intval($activity_info['prices_type_id']);//价格规格ID，活动必填（和服务有区别）
		if( $activity_id<1 || $activity_stage_id<1 || $activity_prices_type_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '活动ID错误';
			return $result;
		}
		
		//是否特殊服务，true特殊服务，false正常服务
		$activity_obj = POCO::singleton('pai_mall_goods_class');
		
		//当前时间
		$cur_time = time();
		
		//整理参数中的数据
		$activity_prime_prices = $activity_info['prices']*1; //下单时传进来的，服务价格
		$activity_quantity = intval($activity_info['quantity']);
		$activity_promotion_id = intval($activity_info['activity_promotion_id']);
		$service_cellphone = trim($activity_info['service_cellphone']);
		$description = trim($more_info['description']);
		$is_auto_sign = intval($more_info['is_auto_sign']);
		$referer = trim($more_info['referer']);
		if( strlen($service_cellphone)<1 )
		{
			$result['result'] = -3;
			$result['message'] = '电话号码不能为空';
			return $result;
		}

		//获取服务信息
		$activity_info = $activity_obj->get_goods_info($activity_id);
		if( empty($activity_info) )
		{
			$result['result'] = -4;
			$result['message'] = '活动服务为空';
			return $result;
		}
		$type_id = intval($activity_info['goods_data']['type_id']);
		$seller_id = intval($activity_info['goods_data']['seller_id']);
		$store_id = intval($activity_info['goods_data']['store_id']);
		$seller_user_id = intval($activity_info['goods_data']['user_id']);
		$activity_name = trim($activity_info['goods_data']['titles']);
		$activity_images = trim($activity_info['goods_data']['images']);
		$activity_version = trim($activity_info['goods_data']['version']);
		$activity_service_location_id = trim($activity_info['goods_data']['location_id']);
		if( $seller_user_id<1 )
		{
			$result['result'] = -5;
			$result['message'] = '商家用户ID为空';
			return $result;
		}
		if( $buyer_user_id==$seller_user_id )
		{
			$result['result'] = -6;
			$result['message'] = '亲，不能购买自己出售的服务哦';
			return $result;
		}

		//获取服务信息中的场次，价格规格信息
		$activity_service_start_time = 0;
		$activity_service_end_time = 0;
		$stage_title = '';
		$activity_prices_spec = '';
		foreach( $activity_info['prices_data_list'] as $prices_data_info_tmp )
		{
			if( $activity_stage_id==intval($prices_data_info_tmp['type_id']) )
			{
				$stage_title = trim($prices_data_info_tmp['name']);
				$activity_service_start_time = intval($prices_data_info_tmp['time_s']);
				$activity_service_end_time = intval($prices_data_info_tmp['time_e']);
				//获取规格名称
				foreach( $prices_data_info_tmp['prices_list_data'] as $prices_info_data_tmp )
				{
					if( $activity_prices_type_id==intval($prices_info_data_tmp['id']) )
					{
						$activity_prices_spec = trim($prices_info_data_tmp['name']);
					}
				}
			}
		}
		if( $activity_service_end_time<1 )
		{
			$result['result'] = -7;
			$result['message'] = '服务时间错误';
			return $result;
		}
		if( $activity_service_end_time<=$cur_time && !defined('G_YUEUS_WAIPAI_IMPORT_ORDER') )
		{
			$result['result'] = -8;
			$result['message'] = '已过了服务时间';
			return $result;
		}
		if( $activity_quantity<1 )
		{
			$result['result'] = -9;
			$result['message'] = '购买数量错误';
			return $result;
		}
		if( strlen($activity_prices_spec)<1 )
		{
			$result['result'] = -10;
			$result['message'] = '规格为空';
			return $result;
		}
		
		//获取品类名称
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_info = $type_obj->get_type_info($type_id);
		if( empty($type_info) )
		{
			$result['result'] = -11;
			$result['message'] = '品类为空';
			return $result;
		}
		$type_name = trim($type_info['name']);

		//活动服务地址，补充省市名称
		$activity_service_address = $activity_info['goods_att'][272];
		if( !empty($activity_service_location_id) )
		{
			$activity_service_address = get_poco_location_name_by_location_id($activity_service_location_id) . ' ' . $activity_service_address;
			$activity_service_address = trim($activity_service_address);
		}
		
		//获取服务价格
		$activity_prices_rst = $activity_obj->get_goods_prices($activity_id,
			array('num'=>$activity_quantity, 'type_id'=>$activity_prices_type_id, 'activity_id'=>$activity_stage_id));
		if( $activity_prices_rst['result']!=1 )
		{
			$result['result'] = -12;
			$result['message'] = '服务价格为空';
			return $result;
		}
		$activity_prime_prices = ($activity_prices_rst['data']['prices'])*1;
		if( $activity_prime_prices<=0 )
		{
			$result['result'] = -13;
			$result['message'] = '服务价格为0';
			return $result;
		}
		
		//机构用户ID
		$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
		$org_user_id = intval($org_info['org_id']);
		
		//过期时间
		$expire_time = $cur_time + $this->get_expire_seconds(self::STATUS_WAIT_PAY, $buyer_user_id);
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//服务进行促销
		$is_activity_promotion = 0;
		$activity_promotion_amount = 0;
		$activity_promotion_ref_id = 0; //促销关联订单的ID
		if( $activity_promotion_id>0 )
		{
			$use_activity_param_info = array(
				'org_user_id' => $org_user_id, //机构用户ID
				'location_id' => $activity_service_location_id, //服务所在地区ID
				'seller_user_id' => $seller_user_id, //商家用户ID
				'mall_type_id' => $type_id, //品类ID
			);
			$use_prices_info = array(
				'channel_gid' => $activity_id, //必填
				'stage_id' => $activity_stage_id,
				'prices_type_id' => $activity_prices_type_id, //必填
				'goods_prices' => $activity_prime_prices, //必填
				'quantity' => $activity_quantity, //必填
			);
			$promotion_obj = POCO::singleton('pai_promotion_class');
			$use_rst = $promotion_obj->use_promotion($buyer_user_id, 'goods', $activity_promotion_id, $this->channel_module, $use_activity_param_info, $use_prices_info);
			if( $use_rst['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -14;
				$result['message'] = trim($use_rst['message']);
				return $result;
			}
			$is_activity_promotion = 1;
			$activity_promotion_amount = $use_rst['promotion_amount']; //单个活动促销了多少金额
			$activity_promotion_ref_id = $use_rst['promotion_ref_id'];
		}
		
		//计算服务金额
		$activity_original_prices = $activity_prime_prices - $activity_promotion_amount;
		$activity_original_amount = $activity_original_prices * $activity_quantity;
		$activity_is_change_price = 0;
		$activity_prices = $activity_original_prices;
		$activity_amount = $activity_prices * $activity_quantity;
		if( $activity_prices<=0 || $activity_amount<=0 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -15;
			$result['message'] = '促销后服务金额为0';
			return $result;
		}
		
		//锁定服务库存
		$check_ret = $activity_obj->check_can_buy($activity_id,
			array('num'=>$activity_quantity, 'type_id'=>$activity_prices_type_id, 'activity_id'=>$activity_stage_id));//activity_id在商品类里为场次ID
		if( $check_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -16;
			$result['message'] = trim($check_ret['message']);
			return $result;
		}
		
		//订单原金额
		$order_prime_amount = $activity_amount;
		
		//订单进行促销
		$order_promotion_amount = 0;
		$is_order_promotion = 0;
		$order_promotion_id = 0;
		$order_promotion_ref_id = 0;
		
		//计算订单金额
		$order_original_amount = $order_prime_amount - $order_promotion_amount;
		$order_is_change_price = 0;
		$order_total_amount = $order_original_amount;
		if( $order_original_amount<=0 || $order_total_amount<=0 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -17;
			$result['message'] = '促销后订单金额为0';
			return $result;
		}
		
		//保存订单
		$order_data = array(
			'order_type' => 'activity',
			'type_id' => $type_id,
			'type_name' => $type_name,
			'seller_id' => $seller_id,
			'store_id' => $store_id,
			'buyer_user_id' => $buyer_user_id,
			'seller_user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'prime_amount' => $order_prime_amount,
			'order_promotion_amount' => $order_promotion_amount,
			'is_order_promotion' => $is_order_promotion,
			'order_promotion_id' => $order_promotion_id,
			'original_amount' => $order_original_amount,
			'total_amount' => $order_total_amount,
			'discount_amount' => 0,
			'pending_amount' => $order_total_amount,
			'is_change_price' => $order_is_change_price,
			'description' => $description,
			'is_auto_accept' => 0,
			'is_auto_sign' => $is_auto_sign,
			'is_special' => 0, //正常服务
			'referer' => $referer,
			'status' => self::STATUS_WAIT_PAY,
			'expire_time' => $expire_time,
			'lately_time'  => $cur_time,
			'add_time' => $cur_time,
		);
		$order_id = $this->add_order($order_data);
		if( $order_id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -18;
			$result['message'] = '订单保存失败';
			return $result;
		}
		$order_sn = rand(10, 99) . $order_id . rand(0, 9);
		$rst = $this->update_order(array('order_sn'=>$order_sn), $order_id);
		if( !$rst )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -19;
			$result['message'] = '订单号生成失败';
			return $result;
		}
		
		//订单促销
		if( $order_promotion_ref_id>0 )
		{
			//把订单ID，更新到促销系统
		}
		
		//新增活动
		$activity_data = array(
			'order_id' => $order_id,
			'type_id' => $type_id,
			'activity_id' => $activity_id,
			'activity_name' => $activity_name,
			'activity_images' => $activity_images,
			'stage_id' => $activity_stage_id,
			'stage_title' => $stage_title,
			'prices_type_id' => $activity_prices_type_id,
			'prices_spec' => $activity_prices_spec,
			'activity_version' => $activity_version,
			'service_start_time' => $activity_service_start_time,
			'service_end_time' => $activity_service_end_time,
			'service_location_id' => $activity_service_location_id,
			'service_address' => $activity_service_address,
			'service_cellphone' => $service_cellphone,
			'prime_prices' => $activity_prime_prices, //活动原价（促销前的活动价格）
			'activity_promotion_amount' => $activity_promotion_amount, //活动促销金额（减少了多少金额）
			'is_activity_promotion' => $is_activity_promotion,
			'activity_promotion_id' => $activity_promotion_id,
			'original_prices' => $activity_original_prices, //促销后的活动价格（活动改价前的价格）
			'prices' => $activity_prices, //活动实价（活动改价后的活动价格）
			'quantity' => $activity_quantity,
			'original_amount' => $activity_original_amount, //合计金额，价格*数量（活动改价前的金额）
			'amount' => $activity_amount, //合计金额，实价*数量（活动改价后的金额）
			'is_change_price' => $activity_is_change_price, //活动是否已改价，0否，1是
		);
		$order_activity_id = $this->add_order_activity($activity_data);
		if( $order_activity_id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -19;
			$result['message'] = '服务保存失败';
			return $result;
		}
		
		//服务促销
		if( $activity_promotion_ref_id>0 )
		{
			$promotion_obj = POCO::singleton('pai_promotion_class');
			$rst = $promotion_obj->update_ref_order(array('channel_oid'=>$order_id, 'channel_did'=>$order_activity_id), $activity_promotion_ref_id);
			if( !$rst )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -20;
				$result['message'] = '更新促销系统失败';
				return $result;
			}
		}

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		//新增过程
		$process = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '下单',
			'process_result' => '待支付',
			'process_content' => '{buyer_nickname} 下单了活动: ' . $activity_name,
			'process_time' => $cur_time,
		);

		$this->add_process($process);
		
		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->submit_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['order_id'] = $order_id;
		$result['order_sn'] = $order_sn;
		return $result;
	}

	/**
	 * 计算支付页的金额，以便显示
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param string $coupon_sn
	 * @return array
	 */
	public function cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn='')
	{
		$result = array('result'=>0, 'message'=>'', 'response_data'=>array());

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$is_available_balance = intval($is_available_balance);
		$coupon_sn = trim($coupon_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']);
		$total_amount = $order_info['total_amount']*1;
		$cur_amount = $total_amount;
		$status = intval($order_info['status']);

		$activity_list = $this->get_activity_list_all($order_id);
		if( empty($activity_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
			return $result;
		}
		$is_activity_promotion = intval($activity_list[0]['is_activity_promotion']);
		$activity_promotion_id = intval($activity_list[0]['activity_promotion_id']);

		//检查订单
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -4;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -5;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -5;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -5;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -5;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$response_data = array(
			'total_amount' => number_format($total_amount, 2, '.', ''),
			'is_allow_coupon' => 1, //是否使用优惠券
			'coupon_sn' => '', //优惠券码
			'batch_name' => '', //优惠券名称
			'coupon_amount' => 0, //优惠券金额
			'is_available_balance' => $is_available_balance, //是否使用钱包
			'available_balance' => 0, //钱包剩余金额
			'use_balance' => 0, //钱包使用金额
			'is_use_third_party_payment' => 1, //是否使用第三方支付方式
			'pending_amount' => 0, //还需支付价格
		);

		//是否允许使用优惠券
		if( $is_order_promotion==1 )
		{
			$order_promotion_info = POCO::singleton('pai_promotion_class')->get_promotion_full_info($order_promotion_id);
			if( empty($order_promotion_info) || $order_promotion_info['is_allow_coupon']!=1 )
			{
				$response_data['is_allow_coupon'] = 0;
			}
		}
		if( $is_activity_promotion==1 )
		{
			$activity_promotion_info = POCO::singleton('pai_promotion_class')->get_promotion_full_info($activity_promotion_id);
			if( empty($activity_promotion_info) || $activity_promotion_info['is_allow_coupon']!=1 )
			{
				$response_data['is_allow_coupon'] = 0;
			}
		}

		//处理优惠券
		if( strlen($coupon_sn)>0 && $response_data['is_allow_coupon']==1 )
		{
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_detail_info = $coupon_obj->get_coupon_detail_by_sn($coupon_sn, $buyer_user_id);
			$coupon_used_amount = $coupon_obj->cal_used_amount($coupon_detail_info, array( 'order_total_amount'=>$total_amount ));
			$response_data['coupon_sn'] = $coupon_sn;
			$response_data['batch_name'] = trim($coupon_detail_info['batch_name']);
			$response_data['coupon_amount'] = $coupon_used_amount;
			if( $response_data['coupon_amount']>0 )
			{
				$cur_amount = bcsub($cur_amount, $response_data['coupon_amount'], 2);
			}
		}

		//获取钱包信息
		$payment_obj = POCO::singleton('pai_payment_class');
		$account_info = $payment_obj->get_user_account_info($buyer_user_id);

		$response_data['available_balance'] = $account_info['available_balance'];
		if( $is_available_balance==1 )
		{
			if( $response_data['available_balance']<$cur_amount )
			{
				$response_data['is_use_third_party_payment'] = 1;
				$response_data['use_balance'] = $response_data['available_balance'];
			}
			else
			{
				$response_data['is_use_third_party_payment'] = 0;
				$response_data['use_balance'] = $cur_amount;
			}
		}
		$response_data['pending_amount'] = number_format(bcsub($cur_amount, $response_data['use_balance'], 2), 2, '.', '');

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['response_data'] = $response_data;
		return $result;
	}

	/**
	 * 提交支付
	 * @param string $order_sn
	 * @param int $user_id 买家用户ID
	 * @param double $available_balance 页面当前余额
	 * @param int $is_available_balance 是否使用余额，0否 1是
	 * @param string $third_code 支付方式 alipay_purse tenpay_wxapp，当用户使用余额全额支付时可为空
	 * @param string $redirect_url 支付成功后跳转的url 当用户使用余额全额支付时可为空
	 * @param string $notify_url
	 * @param string $coupon_sn
	 * @param array $more_info array('page_total_amount'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
	 * result 1调取第三方支付，2余额支付成功
	 */
	public function submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='', $coupon_sn='', $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$available_balance = number_format($available_balance*1, 2, '.', '')*1;
		$is_available_balance = intval($is_available_balance);
		$third_code = trim($third_code);
		$redirect_url = trim($redirect_url);
		$notify_url = trim($notify_url);
		$coupon_sn = trim($coupon_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$page_total_amount = $more_info['page_total_amount']*1;

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$type_id = intval($order_info['type_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']); //订单促销ID
		$total_amount = $order_info['total_amount']*1;
		$pending_amount = $total_amount;
		$status = intval($order_info['status']);
		$is_auto_sign = intval($order_info['is_auto_sign']);

		//获取所有详细列表
		$activity_list = $this->get_activity_list_all($order_id);
		if( empty($activity_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
			return $result;
		}
		$activity_id = intval($activity_list[0]['activity_id']);
		$stage_id = intval($activity_list[0]['stage_id']);
		$activity_name = trim($activity_list[0]['activity_name']);
		$is_activity_promotion = intval($activity_list[0]['is_activity_promotion']);
		$activity_promotion_id = intval($activity_list[0]['activity_promotion_id']); //商品促销ID

		//检查订单
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}
		if( $page_total_amount>0 && $page_total_amount!=$total_amount )
		{
			$result['result'] = -4;
			$result['message'] = '订单金额错误，可能已改价';
			return $result;
		}

		//检查促销了的单是否可以使用优惠券
		if( strlen($coupon_sn)>0 && ($is_order_promotion==1 || $is_activity_promotion==1) )
		{
			$promotion_obj = POCO::singleton('pai_promotion_class');
			if( $is_order_promotion==1 )
			{
				$order_promotion_info = $promotion_obj->get_promotion_info($order_promotion_id);
				if( empty($order_promotion_info) || $order_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '订单促销不允许使用优惠券';
					return $result;
				}
			}
			if( $is_activity_promotion==1 )
			{
				$activity_promotion_info = $promotion_obj->get_promotion_info($activity_promotion_id);
				if( empty($activity_promotion_info) || $activity_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '商品促销不允许使用优惠券';
					return $result;
				}
			}
		}

		$cur_time = time();

		//先去掉上一次的优惠券
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = '不使用优惠券失败';
			return $result;
		}

		$discount_amount = 0;
		$is_use_coupon = 0;
		if( strlen($coupon_sn)>0 )
		{
			//事务开始
			POCO_TRAN::begin($this->getServerId());

			$param_info = array(
				'module_type' => $this->channel_module, //模块类型 waipai yuepai topic task_request mall_order
                'mall_order_type' => 'activity', 
				'order_total_amount' => $total_amount, //订单总金额
				'model_user_id' => $seller_user_id, //模特用户ID，兼容旧约拍券的配置
				'event_user_id' => $seller_user_id, //组织者用户ID，兼容外拍的配置
                'event_id' => $activity_id, // 活动ID，兼容外拍的配置
				'org_user_id' => $org_user_id, //机构ID,
				'mall_type_id' => $type_id, //商品品类ID
				'seller_user_id' => $seller_user_id, //卖家用户ID
				'mall_goods_id' => $activity_id, //商品ID
                'mall_stage_id' => $stage_id, //活动场次ID
			);
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_ret = $coupon_obj->use_coupon($buyer_user_id, 1, $coupon_sn, $this->channel_module, $order_id, $param_info);
			if( $coupon_ret['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = $coupon_ret['message'];
				return $result;
			}
			$discount_amount = $coupon_ret['used_amount'];
			$is_use_coupon = 1;

			$pending_amount = $pending_amount - $discount_amount;
			if( $pending_amount<=0 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '优惠金额有误';
				return $result;	//优惠券金额大于订单金额，即使成功使用也退回。
			}

			$data = array(
				'discount_amount' => $discount_amount,
				'is_use_coupon' => $is_use_coupon,
				'coupon_sn' => $coupon_sn,
				'pending_amount' => $pending_amount,
			);
			$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=0 AND is_pay=0 AND total_amount={$total_amount}");
			if( !$ret )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '更新优惠金额失败';
				return $result;
			}

			//事务提交
			POCO_TRAN::commmit($this->getServerId());
		}

		$payment_obj = POCO::singleton('pai_payment_class');

		//用余额支付
		if( $is_available_balance )
		{
			$account_info = $payment_obj->get_user_account_info($buyer_user_id);
			if( $account_info['available_balance']<$pending_amount )
			{
				//余额不足，置标，必须使用第三方
				$amount = $pending_amount - $account_info['available_balance'];
				$redirect_third = 1;
			}
			else
			{
				//事务开始
				POCO_TRAN::begin($this->getServerId());

				//冻结用户余额，完成支付，更新订单状态
				$more_info = array(
					'org_user_id' => $org_user_id,
					'is_balance' => 1,
					'is_third' => 0,
					'recharge_id' => 0,
					'subject' => $activity_name,
					'remark' => '',
				);
				$submit_ret = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
				if( $submit_ret['error']!==0 )
				{
					//事务回滚
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = $submit_ret['message'];
					return $result;
				}

				//支付订单
				$pay_ret = $this->pay_order($order_info);
				if( !$pay_ret )
				{
					//事务回滚
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -7;
					$result['message'] = '支付失败';
					return $result;
				}

				//事务提交
				POCO_TRAN::commmit($this->getServerId());

				//事件触发
				$trigger_params = array(
					'order_sn' => $order_sn,
				);
				POCO::singleton('pai_mall_trigger_activity_class')->pay_order_after($trigger_params);

				//支付后，自动签到
				if( $is_auto_sign==1 )
				{
					$this->sign_order_for_system($order_sn);
				}

				$result['result'] = 2;
				$result['message'] = '余额支付成功';
				return $result;
			}
		}
		else
		{
			//直接用第三方支付
			$amount = $pending_amount;
			$redirect_third = 1;
		}

		if( $redirect_third )
		{
			$openid = '';
			if( $third_code=='tenpay_wxpub' )
			{
				$bind_weixin_obj = POCO::singleton('pai_bind_weixin_class');
				$bind_info = $bind_weixin_obj->get_bind_info_by_user_id( $buyer_user_id );
				$openid = $bind_info['open_id'];
				if( empty($openid) )
				{
					$result['result'] = -8;
					$result['message'] = '没有绑定微信号';
					return $result;
				}
			}
			$more_info = array(
				'channel_return' => $redirect_url,
				'channel_notify' => $notify_url,
				'openid' => $openid,
			);
			$recharge_ret = $payment_obj->submit_recharge($this->channel_module, $buyer_user_id, $amount, $third_code, $order_id, $order_id, 0, $more_info);
			if( $recharge_ret['error']!==0 )
			{
				$result['result'] = -9;
				$result['message'] = $recharge_ret['message'];//跳转到第三方支付产生错误  详细信息见recharge_ret';
				return $result;
			}

			$result['result'] = 1;
			$result['message'] = '需跳转到第三方支付。';
			$result['payment_no'] = trim($recharge_ret['payment_no']);
			$result['request_data'] = trim($recharge_ret['request_data']);
			return $result;
		}

		$result['result'] = -10;
		$result['message'] = '未知错误';
		return $result;
	}

	/**
	 * 支付订单
	 * @param array $order_info
	 * @return bool
	 */
	private function pay_order($order_info)
	{
		if( !is_array($order_info) || empty($order_info) )
		{
			return false;
		}
		$order_id = intval($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$activity_list = $this->get_activity_list($order_id);
		if( !is_array($activity_list) || count($activity_list)!=1 || !is_array($activity_list[0]) || empty($activity_list[0]) )
		{
			return false;
		}

		$cur_time = time();

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//更新支付状态
		$expire_time = $activity_list[0]['service_end_time'] + $this->get_expire_seconds(self::STATUS_WAIT_SIGN, $buyer_user_id);
		$data = array(
			'status' => self::STATUS_WAIT_SIGN,
			'is_pay' => 1,
			'pay_time' => $cur_time,
			'expire_time' => $expire_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_pay=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			return false;
		}

		//生成签到码
		$activity_list = $this->get_activity_list_all($order_id);
		foreach($activity_list as $activity_info)
		{
			$code_sn = $this->generate_code_sn($buyer_user_id, $seller_user_id, $order_id, $activity_info['order_activity_id']);
			if( strlen($code_sn)<1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());
				
				return false;
			}
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '支付',
			'process_result' => '待签到',
			'process_content' => '{buyer_nickname} 已支付订单',
			'process_time' => $cur_time,
		);
		$process_id = $this->add_process($data);
		if( $process_id<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		return true;
	}

	/**
	 * 支付订单，根据支付信息
	 * @param array $payment_info
	 * @return array array('result'=>0, 'message'=>'') result 1,支付成功，else 支付失败
	 */
	public function pay_order_by_payment_info($payment_info)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		if( !is_array($payment_info) || empty($payment_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		$payment_status = intval($payment_info['status']);
		if( $payment_info['channel_module']!='recharge' || $payment_status!=8 )
		{
			$result['result'] = -2;
			$result['message'] = '支付错误';
			return $result;
		}
		$payment_no = trim($payment_info['payment_no']);
		$third_total_fee = $payment_info['third_total_fee']*1; //实收金额
		$channel_param = trim($payment_info['channel_param']);
		
		if($third_total_fee<=0 )
		{
			$result['result'] = -3;
			$result['message'] = '金额错误';
			return $result;
		}
		if( strlen($channel_param)<1 )
		{
			$result['result'] = -4;
			$result['message'] = '支付错误';
			return $result;
		}
		$channel_param_arr = unserialize($channel_param);
		if( empty($channel_param_arr) )
		{
			$result['result'] = -5;
			$result['message'] = '支付错误';
			return $result;
		}
		$order_id = intval($channel_param_arr['enroll_id_str']);
		
		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -6;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = $order_info['pending_amount']*1;
		$is_auto_accept = intval($order_info['is_auto_accept']);
		
		//获取所有活动列表
		$activity_list = $this->get_activity_list_all($order_id);
		if( empty($activity_list) )
		{
			$result['result'] = -6;
			$result['message'] = '订单活动为空';
			return $result;
		}
		$activity_name = trim($activity_list[0]['activity_name']);
		
		//已支付
		if( $order_info['is_pay']==1 )
		{
			if( $payment_no==$order_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '成功';
				return $result;
			}
			
			$result['result'] = -7;
			$result['message'] = '重复支付';
			return $result;
		}
		
		//全部用第三方为0，第三方和余额一起使用为1
		if( $pending_amount>$third_total_fee )
		{
			$is_balance = 1;
		}
		else
		{
			$is_balance = 0;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		$more_info = array(
			'org_user_id' => $org_user_id,
			'is_balance' => $is_balance,
			'is_third' => 1,
			'recharge_id' => $payment_info['channel_rid'],
			'subject' => $activity_name,
			'remark' => '',
		);
		$payment_obj = POCO::singleton('pai_payment_class');
		$submit_ret = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
		if( $submit_ret['error']!==0 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			pai_log_class::add_log(array(func_get_args(), $submit_ret), 'pay_order_by_payment_info', 'order_activity_tmp');
			
			$result['result'] = -9;
			$result['message'] = $submit_ret['message'];
			return $result;
		}
		
		$pay_ret = $this->pay_order($order_info);
		if( !$pay_ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -10;
			$result['message'] = '支付失败';
			return $result;
		}
		
		$this->update_order(array('payment_no'=>$payment_no), $order_id);
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->pay_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '支付成功';
		return $result;
	}

	/**
	 * 取消活动场次（关闭场次下所有订单，前提是该场次中的订单不能有已完成的订单）
	 * @param $activity_id
	 * @param $stage_id
	 * @return array
	 */
	public function close_order_for_stage($activity_id, $stage_id)
	{
		$result = array('result'=>0, 'message'=>'');
		//检查参数
		$activity_id = intval($activity_id);
		$stage_id = intval($stage_id);
		if( $activity_id<1 || $stage_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$success_count = $this->get_order_list_by_activity_stage($activity_id, $stage_id, self::STATUS_SUCCESS, true);
		if( $success_count['count']>=1 )
		{
			$result['result'] = -2;
			$result['message'] = '该活动已经有完成订单';
			return $result;
		}
		$order_list = $this->get_order_list_by_activity_stage($activity_id, $stage_id, -1, false, 'status IN(0,2)', '', '0,9999999');
		if( empty($order_list) )
		{
			$result['result'] = -3;
			$result['message'] = '该活动没有订单';
			return $result;
		}
		foreach( $order_list as $order_info )
		{
			$this->close_order_for_system($order_info['order_sn']);
		}

		$result['result'] = 1;
		$result['message'] = '活动订单已全部关闭';
		return $result;
	}

	/**
	 * 卖家关闭订单
	 * 若已支付，则退款
	 * @param string $order_sn
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_seller($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		//关闭订单
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_seller($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_seller($order_sn, $user_id);
		}

		$result['result'] = -5;
		$result['message'] = '未知错误';
		return $result;
	}

	/**
	 * 买家关闭订单
	 * 若已支付，则退款
	 * @param string $order_sn
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		//关闭订单
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_buyer($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_buyer($order_sn, $user_id);
		}

		$result['result'] = -5;
		$result['message'] = '未知错误';
		return $result;
	}

	/**
	 * 买家申请退款
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer($order_sn, $user_id)
	{
		return $this->close_wait_sign_order_for_buyer($order_sn, $user_id);
	}

	/**
	 * 系统关闭订单
	 * 若已支付，则退款
	 * @param string $order_sn
	 * @param string $reason 关闭原因
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_system($order_sn,$reason='')
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$status = intval($order_info['status']);

		//检查订单
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		//关闭订单
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_system($order_sn);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_system($order_sn,$reason);
		}

		$result['result'] = -5;
		$result['message'] = '未知错误';
		return $result;
	}

	/**
	 * 买家，关闭待支付订单
	 * @param string $order_sn 订单号
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		//获取买家昵称
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//检查订单
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//关闭订单
		$more_info = array(
			'close_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '{buyer_nickname} 已关闭订单',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->close_wait_pay_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 卖家，关闭待支付订单
	 * @param string $order_sn 订单号
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//关闭订单
		$more_info = array(
			'close_by' => 'seller',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '卖家',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '{seller_nickname} 已关闭订单',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->close_wait_pay_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 系统，关闭待支付订单
	 * @param string $order_sn 订单号
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//检查订单
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//关闭订单
		$more_info = array(
			'close_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => '系统',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '{sys_nickname} 订单超时未支付已关闭',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 买家，关闭待签到订单
	 * @param string $order_sn 订单号
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		//获取买家昵称
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//获得所有详细列表
		$activity_list = $this->get_activity_list_all($order_id);
		if ( empty($activity_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
			return $result;
		}

		//检查订单
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//是否允许退款
		$allow_time = self::ALLOW_BUYER_REFUND_TIME;
		$service_time = $activity_list[0]['service_start_time']*1;
		$service_time_prev = $service_time - $allow_time*3600; //活动开始前24小时
		if( $service_time_prev<$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '活动开始前24小时内将不能申请退款';
			return $result;
		}

		//关闭订单
		$more_info = array(
			'close_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -6;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '{buyer_nickname} 已在有效时间内申请退款，账户即将收到款项',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->close_wait_sign_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 卖家，关闭待签到订单
	 * @param string $order_sn 订单号
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//关闭订单
		$more_info = array(
			'close_by' => 'seller',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '卖家',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '{seller_nickname} 关闭了订单',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 系统，关闭待签到订单
	 * @param string $order_sn 订单号
	 * @param string $reason 关闭原因
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_system($order_sn,$reason)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$reason = trim($reason);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//检查订单
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//关闭订单
		$more_info = array(
			'close_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$close_ret = $this->_close_order($order_info, $more_info);
		if( $close_ret['result']!=1 )
		{
			$result['result'] = -6;
			$result['message'] = $close_ret['message'];
			return $result;
		}

		if( strlen($reason)<1 )
		{
			$reason = '{sys_nickname} 系统关闭了订单';
		}
		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => '系统',
			'process_action' => '关闭',
			'process_result' => '已关闭',
			'process_content' => '系统关闭，原因：'.$reason,
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->close_wait_sign_order_for_system_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 关闭订单
	 * @param array $order_info
	 * @param array $more_info array( 'close_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function _close_order($order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info['order_type']);
		$total_amount = $order_info['total_amount']*1;
		$is_pay = intval($order_info['is_pay']);
		$status = intval($order_info['status']);
		$is_special = intval($order_info['is_special']);

		if( !is_array($more_info) ) $more_info = array();
		$close_by = trim($more_info['close_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();

		//检查订单
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -3;
			$result['message'] = '订单状态错误';
			return $result;
		}

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//更新状态
		$data = array(
			'status' => self::STATUS_CLOSED,
			'close_time' => $cur_time,
			'close_by' => $close_by,
			'close_status' => $status,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status={$status}");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '更新状态失败';
			return $result;
		}

		//获得所有详细列表
		$activity_list = $this->get_activity_list_all($order_id);
		if( empty($activity_list) )
		{
			$result['result'] = -5;
			$result['message'] = '订单详情为空';
			return $result;
		}

		//解锁库存
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		foreach($activity_list as $detail_info)
		{
			$goods_id_tmp = $detail_info['activity_id'];
			$prices_type_id_tmp = $detail_info['stage_id'];
			$goods_quantity_tmp = $detail_info['quantity'];
			if( !$is_special ) //正常服务
			{
				$change_ret = $goods_obj->change_goods_stock($goods_id_tmp, $goods_quantity_tmp, $prices_type_id_tmp); //通过接口修改商品库存
//				pai_log_class::add_log(array('goods_id'=>$goods_id_tmp,'prices_type_id'=>$prices_type_id_tmp,'quantity'=>$goods_quantity_tmp), 'change_goods_stock', 'order');
				if( $change_ret!=1 )
				{
					//事务回滚
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = "库存解锁失败";
					return $result;
				}
			}
		}

		//退还促销数量
		$promotion_rst = POCO::singleton('pai_promotion_class')->refund_promotion_by_oid($this->channel_module, $order_id);
		if( $promotion_rst['result']!==1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -7;
			$result['message'] = $promotion_rst['message'];
			return $result;
		}

		//若已支付，则退款、退券
		if( $is_pay==1 )
		{
			$payment_obj = POCO::singleton('pai_payment_class');
			$cancel_ret = $payment_obj->cancel_event_v2($this->channel_module, $order_id);
			if( $cancel_ret['error']!==0 )
			{
				//pai_log_class::add_log(array(func_get_args(), $cancel_ret), '_close_order', 'order_activity_tmp');
				
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -8;
				$result['message'] = '取消交易失败';
				return $result;
			}

			//退优惠券
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$refund_ret = $coupon_obj->refund_coupon_by_oid($this->channel_module, $order_id);
			if( $refund_ret['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -9;
				$result['message'] = '退还优惠券失败';
				return $result;
			}

			//TODO 释放签到码（更改签到码状态为2）
		}
		else
		{
			//若未付款，不使用优惠券
			$not_use_ret = $this->not_use_order_coupon($order_info);
			if( $not_use_ret['result']!=1 )
			{
				//事务回滚
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -10;
				$result['message'] = '不使用优惠券失败';
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
	 * 签到订单
	 * 买家来找卖家签到，买家出示签到码，卖家调出扫码镜头。
	 * @param string $code_sn 签到码
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order($code_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//检查参数
		$code_sn = trim($code_sn);
		$user_id = intval($user_id);
		if( strlen($code_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		//获取买家昵称
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//获取签到码信息
		$code_info = $this->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = '签到码错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$order_id = intval($code_info['order_id']);
		$is_check = intval($code_info['is_check']);

		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);
		$order_type = trim($order_info['order_type']);
		$order_activity_list = $this->get_activity_list_all($order_id);
		$activity_id = $order_activity_list[0]['activity_id'];
		$stage_id = $order_activity_list[0]['stage_id'];

		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '该签到码不正确，请重新尝试';
			$result['is_limit_error'] = 1;
			return $result;
		}
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = '该签到码之前已经签到过';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = '签到码状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		$cur_time = time();

		//签到订单
		$more_info = array(
			'sign_by' => 'buyer',
			'cur_time' => $cur_time,
		);
		$sign_ret = $this->_sign_order($code_info, $order_info, $more_info);
		if( $sign_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $sign_ret['message'];
			$result['order_sn'] = $sign_ret['order_sn'];
			$result['is_limit_error'] = $sign_ret['is_limit_error'];
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '签到',
			'process_result' => '已完成',
			'process_content' => '{buyer_nickname} 已成功签到',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->sign_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['order_sn'] = $order_sn;
		$result['order_type'] = $order_type;
		$result['activity_id'] = $activity_id;
		$result['stage_id'] = $stage_id;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * 系统签到订单
	 * @param string $order_sn
	 * @param boolean $is_expired 是否因为过期，所以系统签到
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_system($order_sn, $is_expired=false)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//检查参数
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			$result['is_limit_error'] = 1;
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_id = trim($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//获取签到码
		$code_list = $this->get_code_list_all($order_id);
		$code_info = $code_list[0];
		if( !is_array($code_list) || empty($code_list) || !is_array($code_info) || empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = '签到码错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$is_check = intval($code_info['is_check']);

		//检查订单
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = '签到码已签到';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = '签到码状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		$cur_time = time();

		//签到订单
		$more_info = array(
			'sign_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$sign_ret = $this->_sign_order($code_info, $order_info, $more_info);
		if( $sign_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $sign_ret['message'];
			$result['order_sn'] = $sign_ret['order_sn'];
			$result['is_limit_error'] = $sign_ret['is_limit_error'];
			return $result;
		}

		//新增过程
		if( $is_expired )
		{
			$process_content = '{sys_nickname} 订单超时未签到，款项已自动到账商家账户';
		}
		else
		{
			$process_content = '{sys_nickname} 已成功签到';
		}
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => '系统',
			'process_action' => '签到',
			'process_result' => '已完成',
			'process_content' => $process_content,
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->sign_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * 签到订单
	 * @param array $code_info
	 * @param array $order_info
	 * @param array $more_info array( 'sign_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	private function _sign_order($code_info, $order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//检查参数
		if( !is_array($code_info) || empty($code_info) || !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$is_check = intval($code_info['is_check']);
		$order_id = intval($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		if( !is_array($more_info) ) $more_info = array();
		$sign_by = trim($more_info['sign_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();

		//检查订单
		if( $is_check!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '签到码状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '订单状态错误';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//更新签到码状态
		$more_info = array(
			'check_time' => $cur_time,
		);
		$ret = $this->update_code_check($code_id, $more_info);
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '更新签到状态失败';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//更新订单状态
		$data = array(
			'status' => self::STATUS_SUCCESS,
			'sign_time' => $cur_time,
			'sign_by' => $sign_by,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status IN (" . self::STATUS_WAIT_SIGN .")");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = '更新订单状态失败';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		pai_log_class::add_log(func_get_args(), 'sign_order', 'order_test');

		//结算订单
		$end_ret = $this->end_order($order_id);
		if( $end_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -6;
			$result['message'] = '结算订单失败';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * 结算订单
	 * @param int $order_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function end_order($order_id)
	{
		$result = array('result'=>0, 'message'=>'');

		pai_log_class::add_log(func_get_args(), 'end_order', 'order_test');

		$order_id = intval($order_id);
		if( $order_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = bcsub($total_amount, $discount_amount, 2);

		pai_log_class::add_log(func_get_args(), 'end_order', 'order_test');

		//获取所有详细列表
		$activity_list = $this->get_activity_list_all($order_id);
		if( empty($activity_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
			return $result;
		}
		$activity_name = trim($activity_list[0]['activity_name']);

		//处理收入
		$org_amount = $org_user_id>0?$pending_amount:0;
		$in_list[] = array(
			'discount_amount' => $discount_amount,
			'user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'apply_id' => 0,
			'amount' => $total_amount,
			'org_amount' => $org_amount,
			'subject' => $activity_name,
			'remark' => '',
		);

		//处理优惠券
		$coupon_cash_list = array();
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$ref_order_list = $coupon_obj->get_ref_order_list_by_oid($this->channel_module, $order_id);
		if( !is_array($ref_order_list) ) $ref_order_list = array();
		foreach( $ref_order_list as $ref_order_info )
		{
			$used_amount_tmp = $ref_order_info['used_amount'];
			$org_amount_tmp = $org_user_id>0?$used_amount_tmp:0;
			$coupon_cash_list[] = array(
				'id' => $ref_order_info['id'],
				'user_id' => $seller_user_id,
				'org_user_id' => $org_user_id,
				'amount' => $used_amount_tmp,
				'org_amount' => $org_amount_tmp,
				'subject' => $activity_name,
				'remark' => '',
			);
		}

		$refund_list = array();
		$coupon_refund_list = array();
		$payment_obj = POCO::singleton('pai_payment_class');
		$end_ret = $payment_obj->end_event_v2($this->channel_module, $order_id, $refund_list, $in_list, $coupon_refund_list, $coupon_cash_list);
		if( $end_ret['error']!==0 )
		{
			$result['result'] = -3;
			$result['message'] = '完成订单失败';
			return $result;
		}

		//处理促销
		$promotion_obj = POCO::singleton('pai_promotion_class');
		$ref_order_list = $promotion_obj->get_ref_order_list_by_oid($this->channel_module, $order_id);
		if( !is_array($ref_order_list) ) $ref_order_list = array();
		foreach( $ref_order_list as $ref_order_info )
		{
			$more_info = array('seller_user_id'=>$seller_user_id, 'org_user_id'=>$org_user_id, 'need_amount'=>0, 'org_amount'=>0, 'subject'=>$activity_name);
			$cash_rst = $promotion_obj->cash_promotion($ref_order_info['id'], 0, $more_info);
			if( $cash_rst['result']!=1 )
			{
				$result['result'] = -4;
				$result['message'] = '兑现促销失败';
				return $result;
			}
			$settle_rst = $promotion_obj->settle_promotion($ref_order_info['id']);
			if( $settle_rst['result']!=1 )
			{
				$result['result'] = -5;
				$result['message'] = '结算促销失败';
				return $result;
			}
		}

		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}

	/**
	 * 买家评价订单
	 * @param int $order_id
	 * @param int $user_id 买家用户ID
	 * @param int $is_anonymous 是否匿名
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_buyer($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//更新状态
		$data = array(
			'is_buyer_comment' => 1,
			'buyer_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '更新状态失败';
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '评价',
			'process_result' => '已评价',
			'process_content' => '{buyer_nickname} 已评价了 {seller_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->comment_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}

	/**
	 * 卖家评价订单
	 * @param int $order_id
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_seller($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '订单待支付';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}

		$cur_time = time();

		//更新状态
		$data = array(
			'is_seller_comment' => 1,
			'seller_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_seller_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '更新状态失败';
			return $result;
		}

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '卖家',
			'process_action' => '评价',
			'process_result' => '已评价',
			'process_content' => '{seller_nickname} 已评价了 {buyer_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_activity_class')->comment_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '成功';
		return $result;
	}

	/**
	 * 生成签到码
	 * @param int $buyer_user_id
	 * @param int $seller_user_id
	 * @param int $order_id
	 * @param int $order_detail_id
	 * @return string
	 */
	private function generate_code_sn($buyer_user_id, $seller_user_id, $order_id, $order_detail_id)
	{
		//检查参数
		$buyer_user_id = intval($buyer_user_id);
		$seller_user_id = intval($seller_user_id);
		$order_id = intval($order_id);
		$order_detail_id = intval($order_detail_id);
		if( $buyer_user_id<1 || $seller_user_id<1 || $order_id<1 || $order_detail_id<1 )
		{
			return '';
		}

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		$code_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			//获取随机字符串
			$rand_str = $this->get_rand_str(6);
			if( strlen($rand_str)<1 )
			{
				//签到码为空
				break;
			}

			//检查是否能复用
			$c = $this->check_code_recently($rand_str);
			if( $c>0 )
			{
				$while_count++;
				continue;
			}

			//保存
			$data = array(
				'buyer_user_id' => $buyer_user_id,
				'seller_user_id' => $seller_user_id,
				'order_id' => $order_id,
				'order_detail_id' => $order_detail_id,
				'code_sn' => $rand_str,
				'add_time' => time(),
			);
			$id = $this->add_code($data);
			if( $id<1 )
			{
				$while_count++;
				continue;
			}

			//再次检查是否能复用
			$c = $this->check_code_recently($rand_str);
			if( $c>1 )
			{
				//可能另外一个订单也在生成
				$while_count++;
				continue;
			}

			$code_sn = $rand_str;
			break;
		}
		if( strlen($code_sn)<1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			return '';
		}

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		return $code_sn;
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

		//第1位，仅用1、3、5、7、9，其它保留给外拍系统
		$pattern_str = '31597';
		$pattern_len = strlen($pattern_str);
		$code .= substr($pattern_str, rand(0, $pattern_len-1), 1);

		//后几位
		$length--;
		$pattern_str = '8029753416';
		$pattern_len = strlen($pattern_str);
		for($i=0; $i<$length; $i++)
		{
			$code .= substr($pattern_str, rand(0, $pattern_len-1), 1);
		}

		return $code;
	}

	/**
	 * 获取某个时间区间内活动销售的数量
	 * @param  int   $type_id        分类id
	 * @param  int	 $start_time 	 开始查询时间
	 * @param  int	 $end_time		 结束查询时间
	 * @param  int   $limit 		 查询条数
	 * @return array
	 */
	public function get_activity_sales_ranking($type_id, $start_time, $end_time, $limit='0,9999999999')
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.activity_id,COUNT(d.activity_id) AS sales_count ';

		//整理查询条件
		$sql_where = ' WHERE 1';
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $start_time>0 )
		{
			$sql_where .= " AND o.add_time>={$start_time} ";
		}
		if( $end_time>0 )
		{
			$sql_where .= " AND o.add_time<={$end_time} ";
		}

		$where_str = " AND o.status=8 ";

		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_activity_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}"
			. " GROUP BY d.activity_id"
			. " ORDER BY sales_count DESC"
			. " LIMIT {$limit}";
		$ret = $this->query($sql);

		return $ret;
	}

	private function get_activity_prices_info($order_activity_info)
	{
		$result = array('stage_title'=>0,'service_start_time'=>0,'service_end_time'=>0,'stock_num_total'=>0,'prices_spec'=>'');
		if( empty($order_activity_info) )
		{
			return array();
		}
		$activity_obj = POCO::singleton('pai_mall_goods_class');
		$activity_info = $activity_obj->get_goods_info($order_activity_info['activity_id']);
		foreach( $activity_info['prices_data_list'] as $prices_data_info_tmp )
		{
			if( $order_activity_info['stage_id']==intval($prices_data_info_tmp['type_id']) )
			{
				$result['stage_title'] = trim($prices_data_info_tmp['name']);
				$result['service_start_time'] = intval($prices_data_info_tmp['time_s']);
				$result['service_end_time'] = intval($prices_data_info_tmp['time_e']);
				$result['stock_num_total'] = intval($prices_data_info_tmp['stock_num_total']);
				//获取规格名称
				foreach( $prices_data_info_tmp['prices_list_data'] as $prices_info_data_tmp )
				{
					if( $order_activity_info['prices_type_id']==intval($prices_info_data_tmp['id']) )
					{
						$result['prices_spec'] = trim($prices_info_data_tmp['name']);
					}
				}
			}
		}
		$stage_scope = $activity_obj->get_goods_id_screenings_price_max_and_min($order_activity_info['activity_id'],$order_activity_info['stage_id']);
		$result['activity_name'] = $activity_info['goods_data']['titles'];
		$result['activity_images'] = $activity_info['goods_data']['images'];
		$result['min_price'] = $stage_scope['min_price'];
		$result['max_price'] = $stage_scope['max_price'];
		$result['is_official'] = $activity_info['goods_data']['is_official'];
		return $result;
	}
}
