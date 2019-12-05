<?php
/**
 * 通用订单类
 * 
 * @author
 */

class pai_mall_order_test_class extends POCO_TDG
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
	 * 声明服务订单对象
	 * @var null|object
	 */
	private $order_detail_obj = NULL;

	/**
	 * 声明活动订单对象
	 * @var null|object
	 */
	private $order_activity_obj = NULL;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_detail_obj = POCO::singleton('pai_mall_order_detail_class');
		$this->order_activity_obj = POCO::singleton('pai_mall_order_activity_class');
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
		$order_full_list = $this->fill_order_full_list(array($order_info), $login_user_id);
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
		$order_full_list = $this->fill_order_full_list(array($order_info), $login_user_id);
		$order_full_info = $order_full_list[0];
		if( !is_array($order_full_info) ) $order_full_info = array();
		return $order_full_info;
	}

	//TODO
	public function get_order_list_for_activity($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{

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
	 * @return array|int
	 */
	public function get_order_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$type_id = intval($type_id);
		$status = intval($status);
		
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
	 * 可自行添加查询条件的订单列表
	 * @param int $type_id 商品品类ID
	 * @param int $status 订单状态：-1全部，0待支付，1待确认，2待签到，7已关闭，8已完成
	 * @param boolean $b_select_count 订单数量，与查询条件、订单状态一起使用
	 * @param string @where_str 查询语句
	 * @param string $order_by 排序
	 * @param string $limit 一次查询条数
	 * @param string $fields 查询字段
	 * @return array
	 */
	public function get_order_full_list($type_id, $status, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$ret = $this->get_order_list($type_id, $status, $b_select_count, $where_str, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
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
	    return $this->fill_order_full_list($rst);
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
	 * @return array
	 */
	public function get_order_list_for_buyer($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_buyer_comment=-1)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//整理查询条件
		$where_str = "buyer_user_id={$user_id} AND is_buyer_del=0";
		$sql_where = '';
		if( $is_buyer_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_buyer_comment={$is_buyer_comment}";
		}
		if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
		$sql_where .= $where_str;

		$ret = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
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
	public function get_order_list_for_buyer_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_buyer_comment=-1)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_buyer_comment = intval($is_buyer_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//整理查询条件
		$sql_where = " buyer_user_id={$user_id} AND is_buyer_del=0 ";
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

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
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
	 * @return array
	 */
	public function get_order_list_for_seller($user_id, $type_id, $status, $b_select_count=false, $order_by='', $limit='0,20', $fields='*', $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$is_seller_comment = intval($is_seller_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//整理查询条件
		$where_str = "seller_user_id={$user_id} AND is_seller_del=0";
		$sql_where = '';
		if( $is_seller_comment>-1 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "is_seller_comment={$is_seller_comment}";
		}
		if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
		$sql_where .= $where_str;

		$ret = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
		if( $b_select_count )
		{
			return $ret;
		}
		return $this->fill_order_full_list($ret);
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
	public function get_order_list_for_seller_by_where($user_id, $type_id, $status, $b_select_count=false, $where_str, $order_by='', $limit='0,20', $fields='*', $is_fill_order=0, $is_seller_comment=-1)
	{
		$user_id = intval($user_id);
		$is_fill_order = intval($is_fill_order);
		$is_seller_comment = intval($is_seller_comment);
		if( $user_id<1 )
		{
			return $b_select_count ? 0 : array();
		}

		//整理查询条件
		$sql_where = "seller_user_id={$user_id} AND is_seller_del=0 ";
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

		$rst = $this->get_order_list($type_id, $status, $b_select_count, $sql_where, $order_by, $limit, $fields);
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
	 * 获取买家订单待办项目数量
	 * @param int $user_id 买家用户ID
	 * @return array
	 */
	public function get_order_number_for_buyer($user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$fields = ' status,COUNT(*) as c ';
		$where_str = " buyer_user_id={$user_id} AND is_seller_del=0 GROUP BY status ";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//获取待评价状态
		$comment_where_str = " buyer_user_id={$user_id} AND is_seller_del=0 AND is_buyer_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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
	 * 获取卖家订单待办项目数量
	 * @param int $user_id 卖家用户ID
	 * @return array
	 */
	public function get_order_number_for_seller($user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}
		$fields = ' status,COUNT(*) as c ';
		$where_str = " seller_user_id={$user_id} AND is_seller_del=0 GROUP BY status ";
		$ret = $this->get_order_list(0, -1, false, $where_str, '', '0,99999999', $fields);

		//获取待评价状态
		$comment_where_str = " seller_user_id={$user_id} AND is_seller_del=0 AND is_seller_comment=0 AND status=8 ";
		$comment_ret = $this->get_order_list(0, -1, true, $comment_where_str, '', '0,99999999', '*');

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
				$pay_time_str = date('Y-m-d H:i:s', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;
			
			//获取买家名称
			$info['buyer_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_icon'] = get_user_icon($info['buyer_user_id'], 165);
			
			//获取卖家名称
			$info['seller_name'] = get_seller_nickname_by_user_id($info['seller_user_id']);
			$info['seller_icon'] = get_seller_user_icon($info['seller_user_id'], 165);
			
			//订单详细列表
			if( $info['order_type']=='detail' )
			{
				$detail_list = $this->get_detail_list_all($info['order_id']);
				if( empty($detail_list) ) $detail_list = array();
				$info['detail'] = $detail_list;
				$info['expire_str'] = $this->order_detail_obj->get_expire_str($status_tmp, $detail_list, $cur_time);
			}
			elseif( $info['order_type']=='activity' )
			{
				$activity_list = $this->get_activity_list_all($info['order_id']);
				if( empty($activity_list) ) $activity_list = array();
				$info['activity'] = $activity_list;
				$info['expire_str'] = $this->order_activity_obj->get_expire_str($status_tmp, $activity_list, $cur_time);
			}

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
	 * 获取详细
	 * @param int $order_detail_id
	 * @return array
	 */
	public function get_detail_info($order_detail_id)
	{
		return $this->order_detail_obj->get_detail_info($order_detail_id);
	}

	/**
	 * 获取服务列表
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
	 * 获取服务列表
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
	 * 提交服务订单
	 * @param int $buyer_user_id 买家用户ID
	 * @param string $order_type 订单类型 detail,activity
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
	public function submit_order($buyer_user_id, $extend_list, $more_info=array())
	{
		return $this->order_detail_obj->submit_order($buyer_user_id, $extend_list, $more_info);
	}

	/**
	 * 提交服务订单
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

		//事务提交
		POCO_TRAN::commmit($this->getServerId());

		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->change_order_price_after($trigger_params);

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

		//检查订单
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -4;
			$result['message'] = '非法操作';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -5;
			$result['message'] = '订单待确认';
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

		//获取所有详细列表
		$extend_list = array();
		$is_goods_promotion = 0;
		$goods_promotion_id = 0;
		if( $order_info['order_type']=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_goods_promotion']);
			$goods_promotion_id = intval($extend_list[0]['goods_promotion_id']);
		}
		elseif( $order_info['order_type']=='activity' )
		{
			$extend_list = $this->get_activity_list($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_activity_promotion']);
			$goods_promotion_id = intval($extend_list[0]['activity_promotion_id']);
		}
		if( empty($extend_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
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
		if( $is_goods_promotion==1 )
		{
			$goods_promotion_info = POCO::singleton('pai_promotion_class')->get_promotion_full_info($goods_promotion_id);
			if( empty($goods_promotion_info) || $goods_promotion_info['is_allow_coupon']!=1 )
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
				$cur_amount = $cur_amount - $response_data['coupon_amount'];
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
		$response_data['pending_amount'] = number_format(($cur_amount-$response_data['use_balance']), 2, '.', '');

		$result['result'] = 1;
		$result['message'] = '成功';
		$result['response_data'] = $response_data;
		return $result;
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
		$order_info = $this->get_order_info($order_sn);
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			return array();
		}
		$order_id = intval($order_info['order_id']);
		$order_type = trim($order_info);
		$mall_type_id = intval($order_info['type_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$order_total_amount = trim($order_info['total_amount']);
		
		$param_info = array(
			'channel_module' => $this->channel_module,
			'channel_oid' => $order_id,
			'module_type' => $this->channel_module, // yuepai waipai
			'order_total_amount' => $order_total_amount, // 订单总额
			'model_user_id' => $seller_user_id, // 模特用户ID，兼容旧约拍券的配置
			'event_user_id' => $seller_user_id, // 组织者用户ID，兼容外拍的配置
			'org_user_id' => $org_user_id, // 机构ID
			'mall_type_id' => $mall_type_id, //服务品类
			'seller_user_id' => $seller_user_id, //卖家用户ID
		);
		if( $order_type=='detail' )
		{
			$detail_info = $this->get_detail_info($order_id);
			$param_info['mall_goods_id'] = $detail_info['goods_id'];//商品ID
		}
		elseif( $order_type=='activity' )//TODO 优惠券activity_id or goods_id
		{
			$activity_info = $this->get_activity_info($order_id);
			$param_info['mall_activity_id'] = $activity_info['activity_id'];//活动ID
		}

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
		$is_auto_accept = intval($order_info['is_auto_accept']);

		//检查订单
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -3;
			$result['message'] = '非法操作';
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

		//获取所有详细列表
		$extend_list = array();
		$is_goods_promotion = 0;
		$goods_promotion_id = 0;
		$goods_id = 0;
		$goods_name = 0;
		if( $order_info['order_type']=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_goods_promotion']);
			$goods_promotion_id = intval($extend_list[0]['goods_promotion_id']);
			$goods_id = intval($extend_list[0]['goods_id']);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		elseif( $order_info['order_type']=='activity' )
		{
			$extend_list = $this->get_activity_list($order_id);
			$is_goods_promotion = intval($extend_list[0]['is_activity_promotion']);
			$goods_promotion_id = intval($extend_list[0]['activity_promotion_id']);
			$goods_id = intval($extend_list[0]['goods_id']);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		if( empty($extend_list) )
		{
			$result['result'] = -2;
			$result['message'] = '订单详情为空';
			return $result;
		}

		//检查促销了的单是否可以使用优惠券
		if( strlen($coupon_sn)>0 && ($is_order_promotion==1 || $is_goods_promotion==1) )
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
			if( $is_goods_promotion==1 )
			{
				$goods_promotion_info = $promotion_obj->get_promotion_info($goods_promotion_id);
				if( empty($goods_promotion_info) || $goods_promotion_info['is_allow_coupon']!=1 )
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
				'order_total_amount' => $total_amount, //订单总金额
				'model_user_id' => $seller_user_id, //模特用户ID，兼容旧约拍券的配置
				'event_user_id' => $seller_user_id, //组织者用户ID，兼容外拍的配置
				'org_user_id' => $org_user_id, //机构ID,
				'mall_type_id' => $type_id, //商品品类ID
				'seller_user_id' => $seller_user_id, //卖家用户ID
				'mall_goods_id' => $goods_id, //商品ID
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
					'subject' => $goods_name,
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
				POCO::singleton('pai_mall_trigger_class')->pay_order_after($trigger_params);

				//支付后，自动接受
				if( $is_auto_accept==1 )
				{
					$this->accept_order_for_system($order_sn);
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
		$order_type = trim($order_info['order_type']);
		$type_id = intval($order_info['type_id']);

		$cur_time = time();

		//事务开始
		POCO_TRAN::begin($this->getServerId());

		//更新支付状态（活动订单没有待确认步骤/状态）
		$status_tmp = 0;
		if( $order_type=='detail' )
		{
			$status_tmp = self::STATUS_WAIT_CONFIRM;
		}
		elseif( $order_type=='activity' )
		{
			$status_tmp = self::STATUS_WAIT_SIGN;
		}
		$expire_time = $cur_time + $this->get_expire_seconds($status_tmp, $type_id);

		$data = array(
			'status' => $status_tmp,
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

		//新增过程
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '买家',
			'process_action' => '支付',
			'process_result' => '待确认',
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
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = $order_info['pending_amount']*1;
		$is_auto_accept = intval($order_info['is_auto_accept']);
		
		//获取所有详细列表
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$goods_name = $extend_list[0]['goods_name'];
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
			$goods_name = $extend_list[0]['activity_name'];
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -7;
			$result['message'] = '订单详情为空';
			return $result;
		}

		//已支付
		if( $order_info['is_pay']==1 )
		{
			if( $payment_no==$order_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '成功';
				return $result;
			}
			
			$result['result'] = -8;
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
			'subject' => $goods_name,
			'remark' => '',
		);
		$payment_obj = POCO::singleton('pai_payment_class');
		$submit_ret = $payment_obj->submit_trade_out_v2($this->channel_module, $order_id, $order_id, $buyer_user_id, $total_amount, $discount_amount, $more_info);
		if( $submit_ret['error']!==0 )
		{
			//事务回滚
			POCO_TRAN::rollback($this->getServerId());
			
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
		POCO::singleton('pai_mall_trigger_class')->pay_order_after($trigger_params);
		
		//支付后，自动接受
		if( $is_auto_accept==1 )
		{
			$this->accept_order_for_system($order_sn);
		}
		
		$result['result'] = 1;
		$result['message'] = '支付成功';
		return $result;
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
		return $this->order_detail_obj->close_wait_confirm_order_for_seller($order_sn, $user_id);
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
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
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
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->order_detail_obj->close_wait_confirm_order_for_seller($order_sn, $user_id);
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
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
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
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->close_wait_confirm_order_for_buyer($order_sn, $user_id);
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
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
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
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->close_wait_confirm_order_for_system($order_sn);
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
		POCO::singleton('pai_mall_trigger_class')->close_wait_pay_order_for_buyer_after($trigger_params);
		
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
		POCO::singleton('pai_mall_trigger_class')->close_wait_pay_order_for_seller_after($trigger_params);
		
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
	 * 买家，关闭待确认订单
	 * @param string $order_sn 订单号
	 * @param int $user_id 买家用户ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_buyer($order_sn, $user_id)
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
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
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
			'process_content' => '{buyer_nickname} 已取消订单',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//事件触发
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_class')->close_wait_confirm_order_for_buyer_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '关闭成功';
		return $result;
	}

	/**
	 * 系统，关闭待确认订单
	 * @param string $order_sn 订单号
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_system($order_sn)
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
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '订单已完成';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
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
			'process_content' => '{sys_nickname} 订单超时未处理已关闭',
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
		$order_type = trim($order_info['order_type']);

		//获取所有详细列表
		//TODO 活动服务时间再确认
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -6;
			$result['message'] = '订单详情为空';
			return $result;
		}
		$service_time = $extend_list[0]['service_time']*1;
		
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
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
		$service_time_prev = $service_time - $allow_time*3600; //前12小时
		if( $service_time_prev<$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '距离服务开始不足12小时将不能申请退款';
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
		POCO::singleton('pai_mall_trigger_class')->close_wait_sign_order_for_buyer_after($trigger_params);
		
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
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
			$result['result'] = -5;
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
		POCO::singleton('pai_mall_trigger_class')->close_wait_sign_order_for_system_after($trigger_params);
		
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
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
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
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -5;
			$result['message'] = '订单详情为空';
			return $result;
		}
		
		//解锁库存
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		foreach($extend_list as $detail_info)
		{
			$goods_id_tmp = $detail_info['goods_id'];
			$goods_quantity_tmp = $detail_info['quantity'];
			$prices_type_id_tmp = $detail_info['prices_type_id'];
			if( $order_type=='activity' ) $stage_id = $detail_info['stage_id'];
			if( !$is_special ) //正常服务
			{
				$change_ret = $goods_obj->change_goods_stock($goods_id_tmp, $goods_quantity_tmp, $prices_type_id_tmp); //通过接口修改商品库存
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
		//TODO 退还促销可能还需要传递order_type
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
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
		POCO::singleton('pai_mall_trigger_class')->sign_order_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '成功';
		$result['order_sn'] = $order_sn;
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
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '订单待确认';
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
		POCO::singleton('pai_mall_trigger_class')->sign_order_after($trigger_params);
		
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
		$order_type = trim($order_info['order_type']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = bcsub($total_amount, $discount_amount, 2);

		//获得所有详细列表
		$extend_list = array();
		$goods_name = '';
		if( $order_type=='detail' )
		{
			$extend_list = $this->get_detail_list_all($order_id);
			$goods_name = trim($extend_list[0]['goods_name']);
		}
		elseif( $order_type=='activity' )
		{
			$extend_list = $this->get_activity_list_all($order_id);
			$goods_name = trim($extend_list[0]['activity_name']);
		}
		if( empty($extend_list) || strlen($goods_name)<1 )
		{
			$result['result'] = -6;
			$result['message'] = '订单详情为空';
			return $result;
		}

		//处理收入
		$org_amount = $org_user_id>0?$pending_amount:0;
		$in_list[] = array(
			'discount_amount' => $discount_amount,
			'user_id' => $seller_user_id,
			'org_user_id' => $org_user_id,
			'apply_id' => 0,
			'amount' => $total_amount,
			'org_amount' => $org_amount,
			'subject' => $goods_name,
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
				'subject' => $goods_name,
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
			$more_info = array('seller_user_id'=>$seller_user_id, 'org_user_id'=>$org_user_id, 'need_amount'=>0, 'org_amount'=>0, 'subject'=>$goods_name);
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
		POCO::singleton('pai_mall_trigger_class')->comment_order_for_buyer_after($trigger_params);
		
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
		POCO::singleton('pai_mall_trigger_class')->comment_order_for_seller_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '成功';
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
	 * 获取某个时间区间内被拒绝的订单数量（detail）
	 * @param  int   $type_id        分类id
	 * @param  int 	 $user_id		 卖家id
	 * @param  int	 $start_time 	 开始查询时间
	 * @param  int	 $end_time		 结束查询时间
	 * @return array
	 */
	public function get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time)
	{
		return $this->order_detail_obj->get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time);
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

		return $this->order_detail_obj->get_goods_sales_ranking($type_id, $start_time, $end_time, $limit);
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
}
