<?php
/**
 * ��Ӧ����
 *
 * @author
 */

class pai_mall_supplier_class extends POCO_TDG
{
	/**
	 * ������
	 * @var object
	 */
	private $order_obj;

	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
		$this->order_obj = POCO::singleton('pai_mall_order_class');
	}

	/**
	 * ָ����
	 */
	private function set_mall_supplier_tbl()
	{
		$this->setTableName('mall_supplier_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_supplier_goods_tbl()
	{
		$this->setTableName('mall_supplier_goods_tbl');
	}
	/**
	 * ��Ӧ�̻�����Ϣ����Ӧ�����̼ҹ�ϵ��������
	 * @var array
	 */
	public $supplier_list = array(
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>1161270,
					'mobile'=>'18681078009',
					'password'=>'test888',
					'name'=>'һ�ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>120632,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>100002,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>118259,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>100036,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>117355,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
		array(
			'supplier_info'=>
				array(
					'supplier_id'=>130799,
					'mobile'=>'13800138082',
					'password'=>'test888',
					'name'=>'���ҷ���',
					'address'=>'����ߗ',
				),
		),
	);
	/**
	 * ��Ӧ����Ʒ������ϵ
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
	 * �Ƿ�Ӧ�̣�Ȩ���жϣ�
	 * @param  int   $supplier_id ��Ӧ��id
	 * @return boolean
	 */
	public function is_supplier($supplier_id)
	{
		// ������
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
	 * ��ȡ��Ϣ
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
	 * ��ȡ��Ӧ�̻�����Ϣ
	 * @param  int   $supplier_id ��Ӧ��id
	 * @return array
	 */
	public function get_supplier_info($supplier_id)
	{
		// ������
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
	 * ��ȡ��Ϣ
	 * @param int $order_id
	 * @return array
	 */
	public function get_supplier_list($b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		//�����ѯ����
		$sql_where = '';
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}

		$this->set_mall_supplier_tbl();
		//��ѯ
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
	 * ����goods_id��ȡ������Ϣ
	 * @param int $order_id
	 * @return array
	 */
	private function get_supplier_id_by_goods_id($goods_id)
	{
		//�����ѯ����
		$goods_id = intval($goods_id);
		if( $goods_id<0 )
		{
			return array();
		}

		$this->set_mall_supplier_goods_tbl();
		//��ѯ
		return $this->find("goods_id={$goods_id}");
	}

	/**
	 * @param int $user_id �û�id
	 * @return array('result'=>1,'message'=>'�ɹ�')
	 */
	public function add_supplier($user_id)
	{
		$user_id = intval($user_id);
		if( $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		$supplier_info = $this->get_supplier_info_by_id($user_id);
		if( $supplier_info )
		{
			$result['result'] = 1;
			$result['message'] = '���û���ע��';
			return $result;
		}

		$user_nickname =  get_user_nickname_by_user_id($user_id);
//		if( !$user_nickname )
//		{
//			$result['result'] = -2;
//			$result['message'] = '���û�������';
//			return $result;
//		}

		//���涩��
		$supplier_data = array(
			'supplier_user_id'         	=> $user_id,
			'supplier_name'		  		=> $user_nickname,
			'supplier_desc'       		=> '',
			'is_check'        			=> 1,
			'purview_level'				=> 1
		);

		$this->_add_supplier($supplier_data);

		$result['result'] = 1;
		$result['message'] = 'ע��ɹ�';
		return $result;
	}

	/**
	 * �ж���Ʒ�Ƿ����ڹ�Ӧ��
	 * @param  int  $goods_id    ��Ʒid
	 * @param  int  $supplier_id ��Ӧ��id
	 * @return boolean
	 */
	private function is_supplier_goods($supplier_id,$goods_id)
	{
		// ������
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
	 * ��ȡ��Ӧ��������Ʒid
	 * @param  int $supplier_id ��Ӧ��id
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
	 * ǩ������
	 * �����������ǩ������ҳ�ʾǩ���룬���ҵ���ɨ�뾵ͷ��
	 * @param string $code_sn ǩ����
	 * @param int $supplier_id ��Ӧ��id
	 * @param int $sign �Ƿ�ǩ��
	 * @return array array('result'=>0, 'message'=>'', 'order_info'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order($code_sn, $supplier_id, $sign=1)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		// ������
		$supplier_id = intval($supplier_id);
		$code_sn = trim($code_sn);
		if( $supplier_id<1 || strlen($code_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}

		// ��ȡǩ������Ϣ
		$code_info = $this->order_obj->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$order_id = intval($code_info['order_id']);
		$is_check = intval($code_info['is_check']);

		// ��ȡ������ϸ��Ϣ
		$order_info = $this->order_obj->get_order_full_info_by_id($order_id);
		$order_detail = $order_info['detail_list'][0];
		$goods_id = intval($order_detail['goods_id']);
		$seller_user_id = $order_info['seller_user_id'];

		// �ж������ǲ������ڵ�ǰ��Ӧ��
		$is_supplier_goods_ret = $this->is_supplier_goods($supplier_id,$goods_id);
		if( !$is_supplier_goods_ret )
		{
			$result['result'] = -3;
			$result['message'] = '���������ڸù�Ӧ��';
			$result['is_limit_error'] = 1;
			return $result;
		}

		if( $sign && $is_check==0 )
		{
			// ǩ��
			return $this->order_obj->sign_order($code_sn,$seller_user_id);
		}

		$result['result'] = 1;
		$result['message'] = $order_info;
		$result['is_limit_error'] = 0;

		return $result;
	}

	/**
	 * ��ȡ��Ӧ�����ж���
	 * @param  int $supplier_id ��Ӧ��id
	 * @return array
	 */
	public function get_order_list($type_id='-1', $status='-1', $b_select_count=false, $where, $order_by, $limit)
	{
		$type_id = intval($type_id);
		$status = intval($status);
		// ������
		$supplier_id = intval(SUPPLIER_ADMIN_USER_ID);
		if( $supplier_id<1 )
		{
			return array();
		}

		// ��ȡ��Ӧ����Ʒid
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
	 * ���
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