<?php
/**
 * 供应商类
 *
 * @author
 */

class pai_mall_supplier_class extends POCO_TDG
{
	/**
	 * 订单类
	 * @var object
	 */
	private $order_obj;

	/**
	 * 构造函数
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_obj = POCO::singleton('pai_mall_order_class');
	}

	/**
	 * 指定表
	 */
	private function set_mall_supplier_tbl()
	{
		$this->setTableName('mall_supplier_tbl');
	}

	/**
	 * 指定表
	 */
	private function set_mall_supplier_goods_tbl()
	{
		$this->setTableName('mall_supplier_goods_tbl');
	}
	/**
	 * 供应商基本信息及供应商与商家关系测试数据
	 * @var array
	 */
	public $supplier_list = array(
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>1161270,
					'mobile'=>'18681078009',
					'password'=>'test888',
					'name'=>'一家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>120632,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>100002,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>118259,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>100036,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>117355,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>130799,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'两家饭店',
					'address'=>'五羊邨',
				),
		),
	);
	/**
	 * 供应商商品关联关系
	 * @var array
	 */
	public $supplier_goods_list = array(
		array(
			'supplier_id'=>120632,
			'goods_id'=>303,
		),
		array(
			'supplier_id'=>116127,
			'goods_id'=>319,
		),
		array(
			'supplier_id'=>116127,
			'goods_id'=>318,
		),
		array(
			'supplier_id'=>116127,
			'goods_id'=>248,
		),
		array(
			'supplier_id'=>120632,
			'goods_id'=>308,
		),
		array(
			'supplier_id'=>100002,
			'goods_id'=>2117916,
		),
		array(
			'supplier_id'=>117355,
			'goods_id'=>2117117,
		),
		array(
			'supplier_id'=>130799,
			'goods_id'=>2118462,
		),
		array(
			'supplier_id'=>130799,
			'goods_id'=>2118240,
		)

	);

	/**
	 * 是否供应商（权限判断）
	 * @param  int   $supplier_id 供应商id
	 * @return boolean
	 */
	public function is_supplier($supplier_id)
	{
		// 检查参数
		$supplier_id = intval($supplier_id);
		if( $supplier_id<0 )
		{
			return false;
		}

		$supplier_list = $this->supplier_list;
		foreach($supplier_list as $supplier)
		{
			$supplier_id_tmp = $supplier['supplier_info']['supplier_id'];
			if( intval($supplier_id_tmp) == $supplier_id )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 获取信息
	 * @param int $order_id
	 * @return array
	 */
	public function get_supplier_info_by_id($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			return array();
		}
		$this->set_mall_supplier_tbl();
		return $this->find("supplier_user_id={$user_id}");
	}

	/**
	 * 获取供应商基本信息
	 * @param  int   $supplier_id 供应商id
	 * @return array
	 */
	public function get_supplier_info($supplier_id)
	{
		// 检查参数
		$supplier_id = intval($supplier_id);
		if( $supplier_id<0 )
		{
			return false;
		}

		$supplier_list = $this->supplier_list;
		$supplier = array();
		foreach($supplier_list as $supplier)
		{
			$supplier_id_tmp = $supplier['supplier_info']['supplier_id'];
			if( intval($supplier_id_tmp) == $supplier_id )
			{
				$supplier = $supplier['supplier_info'];
			}
		}
		return $supplier;
	}

	/**
	 * 获取信息
	 * @param int $order_id
	 * @return array
	 */
	public function get_supplier_list($b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		//整理查询条件
		$sql_where = '';
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		$this->set_mall_supplier_tbl();
		//查询
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	public function get_supplier_info_by_goods_id($goods_id)
	{
		$goods_id = intval($goods_id);
		if( $goods_id<0 )
		{
			return array();
		}
		$supplier_info = $this->get_supplier_id_by_goods_id($goods_id);
		$supplier_user_id = $supplier_info['supplier_user_id'];
		$supplier_user_name = get_user_nickname_by_user_id($supplier_user_id);
		$supplier_user_cellphone = POCO::singleton('pai_user_class')->get_phone_by_user_id($supplier_user_id);
		$ret = array(
			'supplier_user_id' => $supplier_user_id,
			'supplier_user_name' => $supplier_user_name,
			'supplier_user_cellphone' => $supplier_user_cellphone
		);
		return $ret;
	}

	/**
	 * 根据goods_id获取餐厅信息
	 * @param int $order_id
	 * @return array
	 */
	private function get_supplier_id_by_goods_id($goods_id)
	{
		//整理查询条件
		$goods_id = intval($goods_id);
		if( $goods_id<0 )
		{
			return array();
		}

		$this->set_mall_supplier_goods_tbl();
		//查询
		return $this->find("goods_id={$goods_id}");
	}

	/**
	 * @param int $user_id 用户id
	 * @return array('result'=>1,'message'=>'成功')
	 */
	public function add_supplier($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			return $result;
		}

		$supplier_info = $this->get_supplier_info_by_id($user_id);
		if( $supplier_info )
		{
			$result['result'] = 1;
			$result['message'] = '该用户已注册';
			return $result;
		}

		$user_nickname =  get_user_nickname_by_user_id($user_id);
//		if( !$user_nickname )
//		{
//			$result['result'] = -2;
//			$result['message'] = '该用户不存在';
//			return $result;
//		}

		//保存订单
		$supplier_data = array(
			'supplier_user_id'         	=> $user_id,
			'supplier_name'		  		=> $user_nickname,
			'supplier_desc'       		=> '',
			'is_check'        			=> 1,
			'purview_level'				=> 1
		);

		$this->_add_supplier($supplier_data);

		$result['result'] = 1;
		$result['message'] = '注册成功';
		return $result;
	}

	/**
	 * 判断商品是否属于供应商
	 * @param  int  $goods_id    商品id
	 * @param  int  $supplier_id 供应商id
	 * @return boolean
	 */
	private function is_supplier_goods($supplier_id,$goods_id)
	{
		// 检查参数
		$supplier_id = intval($supplier_id);
		$goods_id = intval($goods_id);
		if( $supplier_id<0 || $goods_id<0 )
		{
			return false;
		}

		$supplier_goods_list = $this->get_supplier_goods_ids($supplier_id);
		if( !$supplier_goods_list )
		{
			return false;
		}

		foreach( $supplier_goods_list as $goods )
		{
			if( $supplier_id == $goods['supplier_user_id'] && $goods_id == $goods['goods_id'] )
			{
				return true;
			}
		}
		return false;
	}

	/**
	 * 获取供应商所有商品id
	 * @param  int $supplier_id 供应商id
	 * @return array
	 */
	private function get_supplier_goods_ids($supplier_id)
	{
		$supplier_id = intval($supplier_id);
		if( $supplier_id<0 )
		{
			return false;
		}
		$this->set_mall_supplier_goods_tbl();
		$supplier_goods_list = $this->findAll("supplier_user_id={$supplier_id}");
		return $supplier_goods_list;
	}

	/**
	 * 签到订单
	 * 买家来找卖家签到，买家出示签到码，卖家调出扫码镜头。
	 * @param string $code_sn 签到码
	 * @param int $supplier_id 供应商id
	 * @param int $sign 是否签到
	 * @return array array('result'=>0, 'message'=>'', 'order_info'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order($code_sn, $supplier_id, $sign=1)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		// 检查参数
		$supplier_id = intval($supplier_id);
		$code_sn = trim($code_sn);
		if( $supplier_id<1 || strlen($code_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '参数错误';
			$result['is_limit_error'] = 1;
			return $result;
		}

		// 获取签到码信息
		$code_info = $this->order_obj->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = '签到码错误';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$order_id = intval($code_info['order_id']);
		$is_check = intval($code_info['is_check']);

		// 获取订单详细信息
		$order_info = $this->order_obj->get_order_full_info_by_id($order_id);
		$order_detail = $order_info['detail_list'][0];
		$goods_id = intval($order_detail['goods_id']);
		$seller_user_id = $order_info['seller_user_id'];

		// 判断卖家是不是属于当前供应商
		$is_supplier_goods_ret = $this->is_supplier_goods($supplier_id,$goods_id);
		if( !$is_supplier_goods_ret )
		{
			$result['result'] = -3;
			$result['message'] = '订单不属于该供应商';
			$result['is_limit_error'] = 1;
			return $result;
		}

		if( $sign && $is_check==0 )
		{
			// 签到
			return $this->order_obj->sign_order($code_sn,$seller_user_id);
		}

		$result['result'] = 1;
		$result['message'] = $order_info;
		$result['is_limit_error'] = 0;

		return $result;
	}

	/**
	 * 获取供应商所有订单
	 * @param  int $supplier_id 供应商id
	 * @return array
	 */
	public function get_order_list($type_id='-1', $status='-1', $b_select_count=false, $where, $order_by, $limit)
	{
		$type_id = intval($type_id);
		$status = intval($status);
		// 检查参数
		$supplier_id = intval(SUPPLIER_ADMIN_USER_ID);
		if( $supplier_id<1 )
		{
			return array();
		}

		// 获取供应商商品id
		$goods_list = $this->get_supplier_goods_ids($supplier_id);
		if( !$goods_list )
		{
			return array();
		}
		$goods_ids = array();
		foreach( $goods_list as $key => $goods )
		{

			$goods_ids[$key] = $goods['goods_id'];
		}
		$order_list = $this->order_obj->get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count, $where, $order_by, $limit);
		// var_dump($order_list);
		return $order_list;
	}

	/**
	 * 添加
	 * @param array $data
	 * @return int
	 */
	private function _add_supplier($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_mall_supplier_tbl();
		return $this->insert($data, 'IGNORE');
	}

}