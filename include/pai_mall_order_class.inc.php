<?php
/**
 * 订单类
 * 
 * @author
 */

class pai_mall_order_class extends POCO_TDG
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
	 * 待确认
	 * 买家已支付，等待卖家确认
	 * @var int
	 */
	const STATUS_WAIT_CONFIRM = 1;
	
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
	const ALLOW_BUYER_REFUND_TIME = 12;

	/**
	 * 状态名称
	 * @var array
	 */
	private $status_str_arr = array(
		0 => '待支付',
		1 => '待确认',
		2 => '待签到',
		7 => '已关闭',
		8 => '已完成',
	);

	/**
	 * 服务订单
	 * @var null|object
	 */
	private $order_detail_obj = NULL;

	/**
	 * 活动订单
	 * @var null|object
	 */
	private $order_activity_obj = NULL;

	/**
	 * 活动订单
	 * @var null|object
	 */
	private $order_payment_obj = NULL;

	/**
	 * 测试分支用户列表
	 * @var array
	 */
	private $test_users = array(116127,117452,100049);

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_detail_obj = POCO::singleton('pai_mall_order_detail_class');
		$this->order_activity_obj = POCO::singleton('pai_mall_order_activity_class');
		$this->order_payment_obj = POCO::singleton('pai_mall_order_payment_class');
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
	private function set_mall_order_detail_tbl()
	{
		$this->setTableName('mall_order_detail_tbl');
	}
	
	/**
	 * 指定表
	 */
	private function set_mall_order_process_tbl()
	{
		$this->setTableName('mall_order_process_tbl');
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
	private function set_mall_comment_buyer_tbl()
	{
		$this->setTableName('mall_comment_buyer_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_mall_comment_seller_tbl()
	{
		$this->setTableName('mall_comment_seller_tbl');
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
	 * 添加订单详细
	 * @param array $data
	 * @return int
	 */
	private function add_order_detail($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_order_detail_tbl();
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
	 * 修改订单详细信息（detail）
	 * @param array $data
	 * @param string $where_str
	 * @return boolean
	 */
	private function update_order_detail_by_where($data, $where_str)
	{
		$where_str = trim($where_str);
		if( !is_array($data) || empty($data) || strlen($where_str)<1 )
		{
			return false;
		}
		$this->set_mall_order_detail_tbl();
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
	 * 获取完整信息
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_full_info($order_sn, $login_user_id=0)
	{
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			return array();
		}
		$login_user_id = intval($login_user_id);
		$order_type = trim($order_info['order_type']);

		$order_full_list = array();
		if( $order_type=='detail' )
		{
			$order_full_list = $this->fill_order_full_list_for_detail(array($order_info), $login_user_id);
		}
		elseif( $order_type=='activity' )
		{
			$order_full_list = $this->fill_order_full_list_for_activity(array($order_info), $login_user_id);
		}
		elseif( $order_type=='payment' )
		{
			$order_full_list = $this->fill_order_full_list_for_payment(array($order_info), $login_user_id);
		}
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
	}
	
	/**
	 * 通过订单id获取完整信息
	 * @param string $order_sn
	 * @return array
	 */
	public function get_order_full_info_by_id($order_id, $login_user_id=0)
	{
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			return array();
		}
		$login_user_id = intval($login_user_id);
		$order_type = trim($order_info['order_type']);
		$order_full_list = array();
		if( $order_type=='detail' )
		{
			$order_full_list = $this->fill_order_full_list_for_detail(array($order_info), $login_user_id);
		}
		elseif( $order_type=='activity' )
		{
			$order_full_list = $this->fill_order_full_list_for_activity(array($order_info), $login_user_id);
		}
		elseif( $order_type=='payment' )
		{
			$order_full_list = $this->fill_order_full_list_for_payment(array($order_info), $login_user_id);
		}
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
	}

	/**
	 * 获取列表
	 * @param int $type_id 商品品类id
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @param string $order_type 订单类型
	 * @return array|int
	 */
	public function get_order_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$type_id = intval($type_id);
		$status = intval($status);
		$order_type = trim($order_type);
		
		//整理查询条件
		$sql_where = '';
		if( $type_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "type_id={$type_id}";
		}
		if( $status>=0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "status={$status}";
		}
		if( strlen($order_type)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "order_type='{$order_type}'";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		//查询
		$this->set_mall_order_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
        
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * 获取服务列表
	 * @param int $type_id 商品品类id
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list_for_detail($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_type = 'detail';
		return $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
	}

	/**
	 * 获取活动列表
	 * @param int $type_id 商品品类id
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_order_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_type = 'activity';
		return $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
	}

	/**
	 * 可自行添加查询条件的订单列表
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param string $order_type 订单类型
	 * @return array
	 */
	public function get_order_full_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$ret = $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields, $order_type);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
	}

	/**
	 * 可自行添加查询条件的订单列表
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param string $order_type 订单类型
	 * @return array
	 */
	public function get_order_full_list_for_detail($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*', $order_type='')
	{
		$ret = $this->get_order_list_for_detail($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list_for_detail($ret);
	}

	/**
	 * 可自行添加查询条件的订单列表
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @return array
	 */
	public function get_order_full_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$ret = $this->get_order_list_for_activity($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list_for_activity($ret);
	}

	/**
	 * 通过goods_id获取订单列表
	 * @param  array   $goods_ids      商品id列表
	 * @param  int     $status         订单状态
	 * @param  boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param  string  $order_by       排序
	 * @param  string  $limit          查询条数
	 * @param  string  $fields         查询字段
	 * @return array
	 */
	public function get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count=false, $where_str, $order_by='d.order_id', $limit='0,20', $fields='o.*')
	{
		$rst = $this->order_detail_obj->get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count, $where_str, $order_by, $limit, $fields);
	    return $rst;
	}

	/**
	 * 获取买家订单列表（加强版）
	 * @param int $user_id 买家用户ID
	 * @param int $type_id 商品品类ID
	 * @param array $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_fill_order 是否需要补充详细信息（detail,process等）
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @return array
	 */
	public function get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_buyer_comment=-1, $order_type='')
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_buyer_comment = intval($is_buyer_comment);
		$order_type = trim($order_type);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}
		//整理查询条件
		$sql_where = " buyer_user_id={$user_id} AND is_buyer_del=0";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		if( $is_buyer_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_buyer_comment={$is_buyer_comment}";
		}

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields, $order_type);
		if( $b_select_count )
		{
			return $rst;
		}
		if( $is_fill_order==1 )
		{
			$rst = $this->fill_order_full_list($rst);
		}
		return $rst;
	}

	/**
	 * 获取买家订单列表
	 * @param int $user_id 买家用户ID
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @param string $order_type 订单类型 服务 detail，活动 activity，当面付 payment
	 * @return array
	 */
	public function get_order_list_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1, $order_type='')
	{
		return $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 1, $is_buyer_comment, $order_type);
	}

	/**
	 * 获取买家服务订单列表
	 * @param int $user_id 买家用户ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @return array
	 */
	public function get_order_list_by_detail_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'detail');
		return $this->fill_order_full_list_for_detail($rst);
	}

	/**
	 * 获取买家活动订单列表
	 * @param int $user_id 买家用户ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @return array
	 */
	public function get_order_list_by_activity_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'activity');
		return $this->fill_order_full_list_for_activity($rst);
	}

	/**
	 * 获取卖家订单列表（加强版）
	 * @param int $user_id 买家用户ID
	 * @param int $type_id 商品品类ID
	 * @param array $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_fill_order 是否需要补充详细信息（detail,process等）
	 * @param int $is_buyer_comment [卖家是否已评价]
	 * @return array
	 */
	public function get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_seller_comment=-1, $order_type)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_seller_comment = intval($is_seller_comment);
		$order_type = trim($order_type);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//整理查询条件
		$sql_where = "seller_user_id={$user_id} AND is_seller_del=0";
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		if( $is_seller_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_seller_comment={$is_seller_comment}";
		}

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields, $order_type);
		if( $b_select_count )
		{
			return $rst;
		}
		if( $is_fill_order==1 )
		{
			$rst = $this->fill_order_full_list($rst);
		}
		return $rst;
	}

	/**
	 * 获取卖家订单列表
	 * @param int $user_id 卖家用户ID
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_seller_comment [卖家是否已评价]
	 * @param string $order_type 订单类型 服务 detail，活动 activity，当面付 payment
	 * @return array
	 */
	public function get_order_list_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_seller_comment=-1, $order_type='')
	{
		return $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 1, $is_seller_comment, $order_type);
	}

	/**
	 * 获取卖家服务订单列表
	 * @param int $user_id 买家用户ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @return array
	 */
	public function get_order_list_by_detail_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'detail');
		return $this->fill_order_full_list_for_detail($rst);
	}


	/**
	 * 获取卖家活动订单列表
	 * @param int $user_id 买家用户ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @param int $is_buyer_comment [买家是否已评价]
	 * @return array
	 */
	public function get_order_list_by_activity_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$rst = $this->get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count, '', $order_by, $limit, $fields, 0, $is_buyer_comment, 'activity');
		return $this->fill_order_full_list_for_activity($rst);
	}

	/**
	 * 获取活动场次订单
	 * @param int $user_id 商家ID
	 * @param int $type_id
	 * @param int $status
	 * @return mixed
	 * @throws App_Exception
	 */
	public function get_activity_list_by_order_for_seller($user_id, $status, $b_select_count, $order_by, $limit)
	{
		return $this->order_activity_obj->get_activity_list_by_order_for_seller($user_id, $status, $b_select_count, $order_by, $limit);
	}

	/**
	 * 获取活动所有场次已支付（不包含取消）数量
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_activity($activity_id)
	{
		return $this->order_activity_obj->sum_order_quantity_of_paid_by_activity($activity_id);
	}

	/**
	 * 获取场次已支付（不包含取消）数量
	 * @param $activity_id
	 * @param $stage_id
	 */
	public function sum_order_quantity_of_paid_by_stage($activity_id, $stage_id)
	{
		return $this->order_activity_obj->sum_order_quantity_of_paid_by_stage($activity_id, $stage_id);
	}

	/**
	 * 获取活动场次订单
	 * @param int $activity_id 活动ID
	 * @param int $stage_id 场次ID
	 * @param int $status 状态 0 待付款，2 待签到，7 已关闭，8 已支付
	 * @param bool $b_select_count 是否查询订单数目
	 * @param string $where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 条数
	 */
	public function get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count, $where_str='', $order_by, $limit)
	{
		return $this->order_activity_obj->get_order_list_by_activity_stage($activity_id, $stage_id, $status, $b_select_count, $where_str, $order_by, $limit);
	}

	/**
	 * 根据活动场次ID获取已支付订单列表（即报名名单）
	 * @param $activity_id
	 * @param $stage_id
	 * @param bool $b_select_count
	 * @param string $order_by
	 * @param string $limit
	 * @return array|mixed
	 */
	public function get_order_list_of_paid_by_stage($activity_id, $stage_id, $b_select_count=false, $order_by='', $limit='0,20')
	{
		return $this->order_activity_obj->get_order_list_of_paid_by_stage($activity_id, $stage_id, $b_select_count, $order_by, $limit);
	}

	/**
	 * 根据活动ID获取某个买家是否有某种状态的单
	 * @param $user_id
	 * @param $activity_id
	 * @param bool $b_select_count
	 * @param int $is_fill_order
	 * @return array|mixed
	 * @throws App_Exception
	 */
	public function get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $is_fill_order=0)
	{
		return $this->order_activity_obj->get_order_list_by_activity_id_for_buyer($user_id, $activity_id, $status, $b_select_count, $order_by, $limit, $is_fill_order);
	}

	/**
	 * 获取买家订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @param string|array $order_type array('detail', 'activity')
	 * @return array
	 */
	public function get_order_number_for_buyer($user_id, $order_type='')
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//整理参数
		$order_type_str = '';
		if( !is_array($order_type) )
		{
			$order_type = trim($order_type);
			if( strlen($order_type)>0 )
			{
				$order_type = array($order_type);
			}
			else
			{
				$order_type = array();
			}
		}
		if( !empty($order_type) )
		{
			$order_type_str = "'". implode("','", $order_type) . "'";
		}
		
		$fields = ' status,COUNT(*) as c ';
		$where_str = " buyer_user_id={$user_id} AND is_seller_del=0";
		if( strlen($order_type_str)>0 )
		{
			$where_str.= " AND order_type IN ({$order_type_str})";
		}
		$where_str.= " GROUP BY status";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//获取待评价状态
		$comment_where_str = " buyer_user_id={$user_id} AND is_seller_del=0 AND is_buyer_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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

		$count = intval($ret_tmp['wait_pay'])+intval($ret_tmp['wait_sign'])+intval($ret_tmp['wait_confirm']);

		$res = array(
			'wait_pay' => isset($ret_tmp['wait_pay']) ? $ret_tmp['wait_pay'] : 0,
			'wait_confirm' => isset($ret_tmp['wait_confirm']) ? $ret_tmp['wait_confirm'] : 0,
			'wait_sign' => isset($ret_tmp['wait_sign']) ? $ret_tmp['wait_sign'] : 0,
			'wait_comment' => intval($comment_ret),
			'all' => $count,
		);

		return $res;
	}

	/**
	 * 获取买家服务订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	public function get_order_number_by_detail_for_buyer($user_id)
	{
		$order_type = 'detail';
		return $this->get_order_number_for_buyer($user_id, $order_type);
	}

	/**
	 * 获取买家活动订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	public function get_order_number_by_activity_for_buyer($user_id)
	{
		$order_type = 'activity';
		$rst = $this->get_order_number_for_buyer($user_id, $order_type);
		unset($rst['wait_confirm']);
		return $rst;
	}

	/**
	 * 获取卖家订单待办项目数量
	 * @param int $user_id 卖家用户ID
	 * @param string|array $order_type array('detail', 'activity')
	 * @param string $where_str
	 * @return array
	 */
	public function get_order_number_for_seller($user_id, $order_type='', $where_str='')
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		
		//整理参数
		$order_type_str = '';
		if( !is_array($order_type) )
		{
			$order_type = trim($order_type);
			if( strlen($order_type)>0 )
			{
				$order_type = array($order_type);
			}
			else
			{
				$order_type = array();
			}
		}
		if( !empty($order_type) )
		{
			$order_type_str = "'". implode("','", $order_type) . "'";
		}
		
		$fields = ' status,COUNT(*) as c ';
		$sql_where = " seller_user_id={$user_id} AND is_seller_del=0";
		if( strlen($order_type_str)>0 )
		{
			$sql_where.= " AND order_type IN ({$order_type_str})";
		}
		if( strlen($where_str)>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		$sql_where.= " GROUP BY status";
		$ret = $this->get_order_list(0, -1, false, $sql_where, '', '0,99999999', $fields);

		//获取待评价状态
		$comment_where_str = " seller_user_id={$user_id} AND is_seller_del=0 AND is_seller_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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

		$count = intval($ret_tmp['wait_pay'])+intval($ret_tmp['wait_sign'])+intval($ret_tmp['wait_confirm'])+intval($comment_ret);

		$res = array(
			'wait_pay' => isset($ret_tmp['wait_pay']) ? $ret_tmp['wait_pay'] : 0,
			'wait_confirm' => isset($ret_tmp['wait_confirm']) ? $ret_tmp['wait_confirm'] : 0,
			'wait_sign' => isset($ret_tmp['wait_sign']) ? $ret_tmp['wait_sign'] : 0,
			'wait_comment' => intval($comment_ret),
			'all' => $count,
		);

		return $res;
	}

	/**
	 * 获取卖家服务订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	public function get_order_number_by_detail_for_seller($user_id)
	{
		$order_type = 'detail';
		return $this->get_order_number_for_seller($user_id, $order_type);
	}

	/**
	 * 获取卖家活动订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	public function get_order_number_by_activity_for_seller($user_id)
	{
		$order_type = 'activity';
		$rst = $this->get_order_number_for_seller($user_id, $order_type);
		unset($rst['wait_confirm']);
		return $rst;
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
		return $this->order_activity_obj->get_order_number_by_stage_for_seller($user_id, $activity_id, $stage_id);
	}

	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	private function fill_order_full_list($list, $login_user_id=0)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		$login_user_id = intval($login_user_id);
		$cur_time = time();

		$code_obj = POCO::singleton('pai_activity_code_class');

		//获取系统昵称、系统头像
		$sys_nickname = get_user_nickname_by_user_id(10002);
		$sys_icon = get_user_icon(10002, 165);

		foreach($list as $key=>$info)
		{
			$order_type = trim($info['order_type']);
			$info_tmp = array();
			if( $order_type=='detail' )
			{
				$info_tmp = $this->fill_order_full_list_for_detail(array($info));
			}
			elseif( $order_type=='activity' )
			{
				$info_tmp = $this->fill_order_full_list_for_activity(array($info));
			}
			elseif( $order_type=='payment' )
			{
				$info_tmp = $this->fill_order_full_list_for_payment(array($info));
			}
			$list[$key] = $info_tmp[0];
		}
		return $list;
	}

	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	private function fill_order_full_list_for_detail($list, $login_user_id=0)
	{
		return $this->order_detail_obj->fill_order_full_list($list, $login_user_id);
	}

	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	private function fill_order_full_list_for_activity($list, $login_user_id=0)
	{
		return $this->order_activity_obj->fill_order_full_list($list, $login_user_id);
	}
	
	/**
	 * 补充订单完整信息
	 * @param array $list
	 * @param int $login_user_id 当前登录者id
	 * @return array
	 */
	private function fill_order_full_list_for_payment($list, $login_user_id=0)
	{
		return $this->order_payment_obj->fill_order_full_list($list, $login_user_id);
	}
	
	/**
	 * 获取详细
	 * @param int $order_detail_id
	 * @return array
	 */
	public function get_detail_info($order_detail_id)
	{
		return $this->order_detail_obj->get_detail_info($order_detail_id);
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
	private function get_detail_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		return $this->order_detail_obj->get_detail_list($order_id, $b_select_count, $where_str, $order_by, $limit, $fields);
	}
	
	/**
	 * 获取列表
	 * @param int $order_id 订单ID
	 * @return array
	 */
	public function get_detail_list_all($order_id)
	{
		return $this->get_detail_list($order_id, false, '', 'order_detail_id ASC', '0,99999999');
	}

	/**
	 * 获取活动详细
	 * @param int $order_activity_id
	 * @return array
	 */
	public function get_activity_info($order_activity_id)
	{
		return $this->order_activity_obj->get_activity_info($order_activity_id);
	}

	/**
	 * 获取活动列表
	 * @param int $order_id 订单ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	private function get_activity_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		return $this->order_activity_obj->get_activity_list($order_id, $b_select_count, $where_str, $order_by, $limit, $fields);
	}

	/**
	 * 获取活动列表
	 * @param int $order_id 订单ID
	 * @return array
	 */
	public function get_activity_list_all($order_id)
	{
		return $this->get_activity_list($order_id, false, '', 'order_activity_id ASC', '0,99999999');
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
	 * @return array
	 */
	public function get_code_list_all($order_id)
	{
		return $this->get_code_list($order_id, false, '', 'code_id ASC', '0,99999999');
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
	 * 获取过期秒数
	 * @param int $status
	 * @param int $type_id
	 * @return int
	 */
	private function get_expire_seconds($status, $type_id)
	{
		$expire_seconds = 0;
		
		$status = intval($status);
		$type_id = intval($type_id);
		if( $status===self::STATUS_WAIT_PAY ) //待支付
		{
			$expire_seconds_arr = array(
				31 => 1*3600, //模特服务，1小时
				//31 => 300, //为方便测试，临时5分钟
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 1*3600; //默认1小时
				//$expire_seconds = 300; //为方便测试，临时5分钟
			}
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM ) //待确认
		{
			$expire_seconds_arr = array(
				31 => 24*3600, //模特服务，24小时
				//31 => 600, //为方便测试，临时10分钟
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 24*3600; //默认24小时
				//$expire_seconds = 600; //为方便测试，临时10分钟
			}
		}
		elseif( $status===self::STATUS_WAIT_SIGN ) //待签到
		{
			$expire_seconds_arr = array(
				31 => 48*3600, //模特服务，48小时
				//31 => 900, //为方便测试，临时15分钟
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 48*3600; //默认48小时
				//$expire_seconds = 900; //为方便测试，临时15分钟
			}
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
	 * 延长订单过期时间 ，仅用于待支付
	 * @param array $order_info
	 * @returns array('result'=>0, 'message'=>'', 'expire_time'=>0)
	 */
	private function delay_order_expire_time_by_wait_pay($order_info)
	{
		$result = array('result'=>0, 'message'=>'', 'expire_time'=>0);
		
		//检查参数
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$expire_time = intval($order_info['expire_time']);
		
		//检查订单
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -2;
			$result['message'] = '订单状态错误';
			return $result;
		}
		
		//判断是否需要延长
		$cur_time = time();
		$seconds = 15*60; //15分钟
		if( $expire_time<1 || $cur_time<($expire_time-$seconds) ) //过期时间大于15分钟时不操作
		{
			$result['result'] = 1;
			$result['message'] = '不需要延长过期时间';
			$result['expire_time'] = $expire_time;
			return $result;
		}
		
		//延长时间
		$expire_time_new = max($expire_time, $cur_time) + $seconds;
		$data = array(
			'expire_time' => $expire_time_new,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, " order_id={$order_id} AND status=" . self::STATUS_WAIT_PAY);
		if( !$ret )
		{
			$result['result'] = -3;
			$result['message'] = '延长过期时间失败';
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['expire_time'] = $expire_time_new;
		return $result;
	}

	/**
	 * 提交订单
	 * @param int $buyer_user_id 买家用户ID
	 * @param array $detail_list 商品详细列表
	 * @param array $more_info 更多信息
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 *
	 * $detail_list = array( array(
	 * 	'goods_id' => 0, //商品ID
	 * 	'prices_type_id' => '',
	 * 	'service_time' => 0, //服务时间
	 * 	'service_location_id' => '',
	 * 	'service_address' => '',
	 *  'service_people' => 0,
	 *  'prices' => 0, //单价，特殊服务必填，正常服务忽略
	 * 	'quantity' => 0, //数量
	 *  'goods_promotion_id' => 0, //商品促销ID
	 * ), ... );
	 *
	 * $more_info = array(
	 * 	'seller_user_id' => 0, //卖家用户ID，特殊服务必填，正常服务忽略
	 * 	'description' => '', //描述、备注
	 *  'is_auto_accept' => 0, //是否自动接受，下单、支付、接受不发送通知
	 *  'is_auto_sign' => 0, //是否自动签到，签到、评价不发送通知
	 *  'referer' => '', //订单来源，app weixin pc wap oa
	 * );
	 *
	 */
	public function submit_order($buyer_user_id, $detail_list, $more_info=array())
	{
		return $this->order_detail_obj->submit_order($buyer_user_id, $detail_list, $more_info);
	}

	/**
	 * 提交活动订单
	 * @param $buyer_user_id
	 * @param $extend_list
	 * @param array $more_info
	 * @return mixed
	 */
	public function submit_order_activity($buyer_user_id, $extend_list, $more_info=array())
	{
		return $this->order_activity_obj->submit_order($buyer_user_id, $extend_list, $more_info);
	}
	
	/**
	 * 订单改价（总额）
	 * @param string $order_sn
	 * @param int $user_id 卖家用户ID
	 * @param string $change_price 更改后的价格
	 * @param string $change_price_reason 改价理由
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function change_order_price($order_sn, $user_id, $change_price, $change_price_reason)
	{
		$result = array('result'=>0, 'message'=>'');

		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$change_price = $change_price*1;
		$change_price_reason = trim($change_price_reason);
		if( strlen($order_sn)<1 || $user_id<1 || $change_price<=0 )
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
		$original_amount = $order_info['original_amount'];
		if( $change_price!=$original_amount && strlen($change_price_reason)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		/*
		//获取订单详细信息
		$detail_list = $this->get_detail_list_all($order_id);
		if( !is_array($detail_list) || count($detail_list)!=1 )
		{
			$result['result'] = -4;
			$result['message'] = '获取订单详细信息错误';
			return $result;
		}
		$order_detail_id = $detail_list[0]['order_detail_id'];
		*/
		
		//检查订单
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -2;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -3;
			$result['message'] = '订单待确认';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -3;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -3;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -3;
			$result['message'] = '订单状态错误';
			return $result;
		}

		if( $change_price==$original_amount )
		{
			$is_change_price = 0;
			$change_price_reason = '';
		}
		else
		{
			$is_change_price = 1;
		}
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//先去掉上一次的优惠券
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = '不使用优惠券失败';
			return $result;
		}

		$data = array(
			'total_amount' => $change_price,
			'pending_amount' => $change_price,
			'is_change_price' => $is_change_price,
			'change_price_reason' => $change_price_reason,
			'lately_time' => time(),
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -12;
			$result['message'] = '订单主信息改价失败';
			return $result;
		}

		/*
		$detail_data = array(
			'amount' => $change_price,
			'is_change_price' => $is_change_price,
		);
		$detail_ret = $this->update_order_detail_by_where($detail_data,"order_detail_id={$order_detail_id}");
		if( !$detail_ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -13;
			$result['message'] = '订单详细改价失败';
			return $result;
		}
		*/

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->change_order_price_after($trigger_params);

		//文本日志
		$log_arr = array(
			'order_sn' => $order_sn,
			'seller_user_id'=> $user_id,
			'change_price'=>$change_price,
			'change_price_reason'=>$change_price_reason,
			'change_time'=>time(),
		);
		pai_log_class::add_log($log_arr, 'change_order_price', 'pai_mall_order_class');

		$result['result'] = 1;
		$result['message'] = '改价成功';
		return $result;
	}
	
	/**
	 * 准备重新支付
	 * @param string $order_sn
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function ready_pay_order($order_sn, $user_id)
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
			$result['result'] = -2;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -3;
			$result['message'] = '订单待确认';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -3;
			$result['message'] = '订单已关闭';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -3;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -3;
			$result['message'] = '订单状态错误';
			return $result;
		}
		
		//延长订单过期时间，以便给用户留足支付时间
		$delay_ret = $this->delay_order_expire_time_by_wait_pay($order_info);
		if( $delay_ret['result']!=1 )
		{
			$result['result'] = -4;
			$result['message'] = $delay_ret['message'];
			return $result;
		}
		
		//不使用优惠券
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $not_use_ret['message'];
			return $result;
		}
		
		$result['result'] = 1;
		$result['message'] = ''; //为空时，前端就不会弹出提示了，直接跳转。
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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->cal_pay_order_for_detail($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->cal_pay_order_for_activity($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->cal_pay_order_for_payment($order_sn, $user_id, $is_available_balance, $coupon_sn);
		}

		return $result;
	}

	/**
	 * 计算服务支付页面金额
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_detail($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_detail_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
	}

	/**
	 * 计算活动支付页面金额
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_activity($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_activity_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
	}

	/**
	 * 计算活动支付页面金额
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param $coupon_sn
	 * @return mixed
	 */
	public function cal_pay_order_for_payment($order_sn, $user_id, $is_available_balance, $coupon_sn)
	{
		return $this->order_payment_obj->cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn);
	}

	/**
	 * 获取优惠券参数信息
	 * @param string $order_sn 订单号
	 * @param int $user_id 消费者用户ID
	 * @return array
	 */
	public function get_coupon_param_info_by_order_sn($order_sn, $user_id)
	{
		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			return array();
		}
		
		//订单详情
		$order_full_info = $this->get_order_full_info($order_sn);
		if( $user_id!=$order_full_info['buyer_user_id'] )
		{
			return array();
		}
		$order_id = intval($order_full_info['order_id']);
        $mall_order_type = trim($order_full_info['order_type']);
		$mall_type_id = intval($order_full_info['type_id']);
		$seller_user_id = intval($order_full_info['seller_user_id']);
		$org_user_id = intval($order_full_info['org_user_id']);
		$order_total_amount = trim($order_full_info['total_amount']);

        $mall_goods_id = 0;
        $mall_stage_id = 0;
        if($mall_order_type=='detail')
        {
            $mall_goods_id = intval($order_full_info['detail_list'][0]['goods_id']);
        }
        elseif($mall_order_type=='activity')
        {
            $mall_goods_id = intval($order_full_info['activity_list'][0]['activity_id']);
            $mall_stage_id = intval($order_full_info['activity_list'][0]['stage_id']);
        }
		
		$param_info = array(
			'channel_module' => $this->channel_module,
			'channel_oid' => $order_id,
            'mall_order_type' => $mall_order_type, 
			'module_type' => $this->channel_module, // yuepai waipai
			'order_total_amount' => $order_total_amount, // 订单总额
			'model_user_id' => $seller_user_id, // 模特用户ID，兼容旧约拍券的配置
			'event_user_id' => $seller_user_id, // 组织者用户ID，兼容外拍的配置
			'event_id' => $mall_goods_id, // 活动ID，兼容外拍的配置
			'org_user_id' => $org_user_id, // 机构ID
			'mall_type_id' => $mall_type_id, //服务品类
			'seller_user_id' => $seller_user_id, //卖家用户ID
			'mall_goods_id' => $mall_goods_id, //商品ID
			'mall_stage_id' => $mall_stage_id, //活动场次ID
		);
		return $param_info;
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
		$order_type= trim($order_info['order_type']);

		//获取所有详细列表
		if( $order_type=='detail' )
		{
			$result = $this->submit_pay_order_for_detail($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->submit_pay_order_for_activity($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->submit_pay_order_for_payment($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
		}
		return $result;
	}

	/**
	 * 提交服务支付
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_detail($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_detail_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
	}

	/**
	 * 提交活动支付
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_activity($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_activity_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
	}

	/**
	 * 提交当面付支付
	 * @param $order_sn
	 * @param $user_id
	 * @param $available_balance
	 * @param $is_available_balance
	 * @param $third_code
	 * @param $redirect_url
	 * @param $notify_url
	 * @param $coupon_sn
	 * @param $more_info
	 * @return mixed
	 */
	public function submit_pay_order_for_payment($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info)
	{
		return $this->order_payment_obj->submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url, $coupon_sn, $more_info);
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
		$channel_param = trim($payment_info['channel_param']);

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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->pay_order_by_payment_info_for_detail($payment_info);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->pay_order_by_payment_info_for_activity($payment_info);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->pay_order_by_payment_info_for_payment($payment_info);
		}
		return $result;
	}

	/**
	 * 支付服务订单，根据支付信息
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_detail($payment_info)
	{
		return $this->order_detail_obj->pay_order_by_payment_info($payment_info);
	}

	/**
	 * 支付活动订单，根据支付信息
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_activity($payment_info)
	{
		return $this->order_activity_obj->pay_order_by_payment_info($payment_info);
	}

	/**
	 * 支付活动订单，根据支付信息
	 * @param $payment_info
	 * @return mixed
	 */
	public function pay_order_by_payment_info_for_payment($payment_info)
	{
		return $this->order_payment_obj->pay_order_by_payment_info($payment_info);
	}

	/**
	 * 卖家，接受订单
	 * @param string $order_sn
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order($order_sn, $user_id=0)
	{
		return $this->order_detail_obj->accept_order($order_sn, $user_id);
	}

	/**
	 * 系统，接受订单
	 * @param string $order_sn
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order_for_system($order_sn)
	{
		return $this->order_detail_obj->accept_order_for_system($order_sn);
	}

	/**
	 * 拒绝订单
	 * @param string $order_sn
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refuse_order($order_sn, $user_id=0)
	{
		return $this->order_detail_obj->refuse_order($order_sn, $user_id);
	}

	/**
	 * 关闭场次下所有订单
	 * @param int $activity_id
	 * @param int $stage_id
	 * @return array
	 */
	public function close_order_for_stage($activity_id, $stage_id)
	{
		return $this->order_activity_obj->close_order_for_stage($activity_id, $stage_id);
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
		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_seller_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_seller_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_seller_by_detail($order_sn,$user_id)
	{
		return $this->order_detail_obj->close_order_for_seller($order_sn,$user_id);
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_seller_by_activity($order_sn,$user_id)
	{
		return $this->order_activity_obj->close_order_for_seller($order_sn,$user_id);
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
		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_buyer_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_buyer_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_buyer_by_detail($order_sn,$user_id)
	{
		return $this->order_detail_obj->close_order_for_buyer($order_sn,$user_id);
	}

	/**
	 * @param $order_sn
	 * @param $user_id
	 * @return mixed
	 */
	public function close_order_for_buyer_by_activity($order_sn,$user_id)
	{
		return $this->order_activity_obj->close_order_for_buyer($order_sn,$user_id);
	}
	
	/**
	 * 买家申请退款
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->refund_order_for_buyer_by_detail($order_sn,$user_id);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->refund_order_for_buyer_by_activity($order_sn,$user_id);
		}

		return $result;
	}

	/**
	 * 买家申请服务退款
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer_by_detail($order_sn, $user_id)
	{
		return $this->order_detail_obj->refund_order_for_buyer($order_sn,$user_id);
	}

	/**
	 * 买家申请活动退款
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer_by_activity($order_sn, $user_id)
	{
		return $this->order_activity_obj->refund_order_for_buyer($order_sn,$user_id);
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

		//获取订单信息
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '订单为空';
			return $result;
		}
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->close_order_for_system_by_detail($order_sn,$reason);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->close_order_for_system_by_activity($order_sn,$reason);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->close_order_for_system_by_payment($order_sn, $reason);
		}

		return $result;
	}

	/**
	 * 系统关闭服务订单
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_detail($order_sn,$reason)
	{
		return $this->order_detail_obj->close_order_for_system($order_sn,$reason);
	}

	/**
	 * 系统关闭活动订单
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_activity($order_sn,$reason)
	{
		return $this->order_activity_obj->close_order_for_system($order_sn,$reason);
	}
	
	/**
	 * 系统关闭面付订单
	 * @param $order_sn
	 * @param $reason
	 * @return mixed
	 */
	public function close_order_for_system_by_payment($order_sn, $reason)
	{
		return $this->order_payment_obj->close_order_for_system($order_sn);
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

		//获取签到码信息
		$code_info = $this->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = '签到码错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$order_id = intval($code_info['order_id']);

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
		$order_type =trim($order_info['order_type']);

		if( $order_type=='activity' )
		{
			$result = $this->sign_order_for_activity($code_sn, $user_id);
		}
		elseif( $order_type=='detail' )
		{
			$result = $this->sign_order_for_detail($code_sn, $user_id);
		}

		return $result;
	}

	/**
	 * 签到订单
	 * 买家来找卖家签到，买家出示签到码，卖家调出扫码镜头。
	 * @param string $code_sn 签到码
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_detail($code_sn, $user_id)
	{
		return $this->order_detail_obj->sign_order($code_sn, $user_id);
	}

	/**
	 * 签到订单
	 * 买家来找卖家签到，买家出示签到码，卖家调出扫码镜头。
	 * @param string $code_sn 签到码
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_activity($code_sn, $user_id)
	{
		return $this->order_activity_obj->sign_order($code_sn, $user_id);
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
		$order_type =trim($order_info['order_type']);

		if( $order_type=='activity' )
		{
			$result = $this->sign_order_for_system_by_activity($order_sn, $is_expired);
		}
		elseif( $order_type=='detail' )
		{
			$result = $this->sign_order_for_system_by_detail($order_sn, $is_expired);
		}

		return $result;
	}

	/**
	 * @param $order_sn
	 * @param $is_expired
	 * @return mixed
	 */
	public function sign_order_for_system_by_activity($order_sn, $is_expired)
	{
		return $this->order_activity_obj->sign_order_for_system($order_sn, $is_expired);
	}

	/**
	 * @param $order_sn
	 * @param $is_expired
	 * @return mixed
	 */
	public function sign_order_for_system_by_detail($order_sn, $is_expired)
	{
		return $this->order_detail_obj->sign_order_for_system($order_sn, $is_expired);
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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->order_detail_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->order_activity_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->order_payment_obj->comment_order_for_buyer($order_id, $user_id, $is_anonymous);
		}
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
		$order_type = trim($order_info['order_type']);
		if( $order_type=='detail' )
		{
			$result = $this->order_detail_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='activity' )
		{
			$result = $this->order_activity_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
		elseif( $order_type=='payment' )
		{
			$result = $this->order_payment_obj->comment_order_for_seller($order_id, $user_id, $is_anonymous);
		}
		return $result;
	}
	
	/**
	 * 买家删除订单
	 * @param string $order_sn
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function del_order_for_buyer($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
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
		if( $user_id>0 && $user_id!=$order_info['buyer_user_id'] )
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//更新状态
		$data = array(
			'is_buyer_del' => 1,
			'buyer_del_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_del=0 AND status IN (". self::STATUS_CLOSED .")");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '更新状态失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '删除成功';
		return $result;
	}
	
	/**
	 * 卖家删除订单
	 * @param string $order_sn
	 * @param int $user_id 卖家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function del_order_for_seller($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//检查参数
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
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
		if( $user_id>0 && $user_id!=$order_info['seller_user_id'] )
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '订单待签到';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '订单状态错误';
			return $result;
		}
		
		$cur_time = time();
		
		//事务开始
		POCO_TRAN::begin($this->getServerId());
		
		//更新状态
		$data = array(
			'is_seller_del' => 1,
			'seller_del_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_seller_del=0 AND status IN (". self::STATUS_CLOSED .")");
		if( !$ret )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
			$result['result'] = -5;
			$result['message'] = '更新状态失败';
			return $result;
		}
		
		//事务提交
		POCO_TRAN::commmit($this->getServerId());
		
		$result['result'] = 1;
		$result['message'] = '删除成功';
		return $result;
	}


	/**
	 * 获取某个时间区间内被拒绝的订单数量
	 * @param  int   $type_id        分类id
	 * @param  int 	 $user_id		 卖家id
	 * @param  int	 $start_time 	 开始查询时间
	 * @param  int	 $end_time		 结束查询时间
	 * @return array
	 */
	public function get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time)
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS count ';

		//整理查询条件
		$sql_where = ' WHERE 1';
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $user_id>0 )
		{
			$sql_where .= " AND o.seller_user_id={$user_id} ";
		}
		if( $start_time>0 )
		{
			$sql_where .= " AND o.close_time>={$start_time} ";
		}
		if( $end_time>0 )
		{
			$sql_where .= " AND o.close_time<={$end_time} ";
		}

		$where_str = " AND o.close_by='seller' AND o.close_status=1 AND o.status=7 ";

		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}"
			. " GROUP BY d.goods_id";
		$ret = $this->query($sql);

		return $ret;
	}

	/**
	 * 获取某个时间区间内商品销售的数量
	 * @param  int   $type_id        分类id
	 * @param  int	 $start_time 	 开始查询时间
	 * @param  int	 $end_time		 结束查询时间
	 * @param  int   $limit 		 查询条数
	 * @return array
	 */
	public function get_goods_sales_ranking($type_id, $start_time, $end_time, $limit='0,9999999999')
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS sales_count ';

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
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}"
			. " GROUP BY d.goods_id"
			. " ORDER BY sales_count DESC"
			. " LIMIT {$limit}";
		$ret = $this->query($sql);

		return $ret;
	}

	/**
	 * 是否待签到订单
	 * @param int $order_id
	 * @return boolean
	 */
	public function is_wait_sign_order($order_id)
	{
		//检查参数
		$order_id = intval($order_id);
		if( strlen($order_id)<1 )
		{
			return false;
		}
		
		//获取订单信息
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			return false;
		}
		$status = intval($order_info['status']);
		
		//判断订单状态
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			return false;
		}
		return true;
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

	public function get_order_pay_num($goods_id)
	{
		$sql_where = " WHERE d.goods_id={$goods_id} AND is_pay=1 AND status!=7 ";
		$sql = "SELECT count(*) as total "
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where}";
		$rs = $this->query($sql);
		return $rs[0];
	}
}
