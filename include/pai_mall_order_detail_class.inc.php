<?php
/**
 * ���񶩵���
 * 
 * @author
 */

class pai_mall_order_detail_class extends POCO_TDG
{
	/**
	 * ����ģ��
	 * @var string
	 */
	private $channel_module = 'mall_order';
	
	/**
	 * ��֧��
	 * ������µ����ȴ����֧��
	 * @var int
	 */
	const STATUS_WAIT_PAY = 0;
	
	/**
	 * ��ȷ��
	 * �����֧�����ȴ�����ȷ��
	 * @var int
	 */
	const STATUS_WAIT_CONFIRM = 1;
	
	/**
	 * ��ǩ��
	 * ������ȷ�ϣ��ȴ����ǩ��
	 * @var int
	 */
	const STATUS_WAIT_SIGN = 2;
	
	/**
	 * �ѹر�
	 * @var int
	 */
	const STATUS_CLOSED = 7;
	
	/**
	 * �����
	 * @var int
	 */
	const STATUS_SUCCESS = 8;

	/**
     * ���������������˿�ʱ�䣨Сʱ��
     * @var int
	 */
	const ALLOW_BUYER_REFUND_TIME = 12;

	/**
	 * ״̬����
	 * @var array
	 */
	private $status_str_arr = array(
		0 => '��֧��',
		1 => '��ȷ��',
		2 => '��ǩ��',
		7 => '�ѹر�',
		8 => '�����',
	);
	
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId('101');
		$this->setDBName('mall_db');
	}

	/**
	 * ָ����
	 */
	private function set_mall_order_tbl()
	{
		$this->setTableName('mall_order_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_order_detail_tbl()
	{
		$this->setTableName('mall_order_detail_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_order_code_tbl()
	{
		$this->setTableName('mall_order_code_tbl');
	}

	/**
	 * ָ����
	 */
	private function set_mall_order_process_tbl()
	{
		$this->setTableName('mall_order_process_tbl');
	}

	/**
	 * ���
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
	 * �޸�
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
	 * �޸Ķ�������Ϣ
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
	 * ��Ӷ�����ϸ
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
	 * �޸Ķ�����ϸ��Ϣ��detail��
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
	 * ���
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
	 * ���ǩ�����Ƿ��ܸ���
	 * @param string $code_sn
	 * @return int
	 */
	private function check_code_recently($code_sn)
	{
		$code_sn = trim($code_sn);

		$where_str = 'code_sn=:x_code_sn';
		sqlSetParam($where_str, 'x_code_sn', $code_sn);

		$check_time = time() + 90*24*3600; //���90��
		$where_str .= " AND (is_check=0 OR (is_check=1 AND check_time>={$check_time}))";

		return $this->get_code_list(0, true, $where_str);
	}

	/**
	 * ������ǩ��
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
	 * ���
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
	 * ��ȡ��Ϣ
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
	 * ��ȡ��Ϣ
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
	 * ������ʾ
	 * @param $status
	 * @param $detail_list
	 * @param $cur_time
	 * @return string
	 */
	public function get_expire_str($status, $detail_list, $cur_time)
	{
		switch ($status)
		{
			case self::STATUS_WAIT_PAY:
				$expire_str = '�µ���1Сʱ��֧���������Զ��ر�';
				break;
			case self::STATUS_WAIT_CONFIRM:
				$expire_str = '֧����24Сʱ��ȷ�϶������Զ��ر�';
				break;
			case self::STATUS_WAIT_SIGN:
				if( $detail_list[0]['service_time']>=$cur_time )
				{
					$expire_str = '�������ʼ����12Сʱ�����������˿�';
				}
				else
				{
					$expire_str = '����ʼ��48Сʱ��ǩ�����Զ������̼��˻�';
				}
				break;
			default:
				$expire_str = '';
				break;
		}
		return $expire_str;
	}

	/**
	 * ͨ��goods_id��ȡ�����б�
	 * @param  array   $goods_ids      ��Ʒid�б�
	 * @param  int     $status         ����״̬
	 * @param  boolean $b_select_count �������������ѯ����������״̬һ��ʹ��
	 * @param  string  $order_by       ����
	 * @param  string  $limit          ��ѯ����
	 * @param  string  $fields         ��ѯ�ֶ�
	 * @return array
	 */
	public function get_order_list_by_goods_ids($type_id, $status, $goods_ids, $b_select_count, $where_str, $order_by, $limit, $fields)
	{
		$type_id = intval($type_id);
		$status = intval($status);
		if( !is_array($goods_ids) )
		{
			return array();
		}
		$goods_ids = implode(',',$goods_ids);

		//�����ѯ����
		$sql_where = " WHERE order_type='detail'";
		if( $type_id>0 )
		{
			$sql_where .= " AND o.type_id={$type_id}";
		}
		if( $status>=0 )
		{
			$sql_where .= " AND status={$status}";
		}
		$where_str .= " AND d.goods_id IN ({$goods_ids}) ";
		if( strlen($where_str)>0 )
		{
			$sql_where .= $where_str;
		}

		if( $b_select_count == true )
		{
			$sql = "SELECT count(*) as total,sum(total_amount) as order_amount "
				. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
				. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
				. " ON d.order_id=o.order_id"
				. " {$sql_where}";
			$rs = $this->query($sql);
			return $rs[0];
		}

		$sql = "SELECT {$fields}"
			. " FROM {$this->_db_name}.mall_order_detail_tbl as d"
			. " LEFT JOIN {$this->_db_name}.mall_order_tbl as o"
			. " ON d.order_id=o.order_id"
			. " {$sql_where} order by {$order_by} desc limit {$limit}";
		$rst = $this->query($sql);

		$rst = $this->fill_order_full_list($rst);
		return $rst;
	}

	/**
	 * ���䶩��������Ϣ
	 * @param array $list
	 * @param int $login_user_id ��ǰ��¼��id
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

		$code_obj = POCO::singleton('pai_activity_code_class');

		//��ȡϵͳ�ǳơ�ϵͳͷ��
		$sys_nickname = get_user_nickname_by_user_id(10002);
		$sys_icon = get_user_icon(10002, 165);

		foreach($list as $key=>$info)
		{
			//����״̬
			$status_tmp = intval($info['status']);
			if( array_key_exists($status_tmp, $this->status_str_arr) )
			{
				$status_str_tmp = $this->status_str_arr[$status_tmp];
			}
			else
			{
				$status_str_tmp ='δ֪״̬';
			}
			$info['status_str'] = $status_str_tmp;

			$info['status_str2'] = $status_str_tmp;
			if( $status_tmp==self::STATUS_SUCCESS )
			{
				$info['status_str2'] = '���������';
				if( $info['sign_by']=='sys' )
				{
					$info['status_str2'] = '�ѳ�ʱ���';
				}
			}
			if( $status_tmp==self::STATUS_CLOSED )
			{
				$info['status_str2'] = '�ѹر�';
				if( intval($info['is_pay'])==1 )
				{
					$info['status_str2'] = '���˿�';
				}
			}

			//֧��ʱ��
			$pay_time_str = '--';
			if( $info['is_pay']==1 )
			{
				$pay_time_str = date('Y.m.d H:i', $info['pay_time']);
			}
			$info['pay_time_str'] = $pay_time_str;

			//��ȡ�������
			$info['buyer_name'] = get_user_nickname_by_user_id($info['buyer_user_id']);
			$info['buyer_icon'] = get_user_icon($info['buyer_user_id'], 165);

			//��ȡ��������
			$info['seller_name'] = get_seller_nickname_by_user_id($info['seller_user_id']);
			$info['seller_icon'] = get_seller_user_icon($info['seller_user_id'], 165);

			//������ϸ�б�
			$detail_list = $this->get_detail_list_all($info['order_id']);

			//������ԭʼ�۸�
			$info['original_subtotal'] = number_format($detail_list[0]['prime_prices'] * intval($detail_list[0]['quantity']), 2, '.', '');

			//��ȡ������Ϣ
			$is_goods_promotion = intval($detail_list[0]['is_goods_promotion']);
			$goods_promotion_id = intval($detail_list[0]['goods_promotion_id']);
			$goods_promotion_amount = $detail_list[0]['goods_promotion_amount'];
			$goods_promotion_info = array();
			$original_amount_str = '';
			if( $is_goods_promotion==1 )
			{
				$goods_promotion_rst = POCO::singleton('pai_promotion_class')->get_promotion_full_info($goods_promotion_id);
				$goods_promotion_info['type_name'] = $goods_promotion_rst['type_name'];
				$goods_promotion_info['start_time'] = date('Y.m.d', $goods_promotion_rst['start_time']);
				$goods_promotion_info['end_time'] = date('Y.m.d', $goods_promotion_rst['end_time']);
				$goods_promotion_info['promotion_desc'] = $goods_promotion_rst['promotion_desc'];
				$goods_promotion_used_amount = number_format(($goods_promotion_amount * intval($detail_list[0]['quantity'])), 2, '.', '');
				$goods_promotion_info['cal_used_amount'] = '��' . $goods_promotion_used_amount;
				if( $info['is_change_price']==1 ) //�иļ�
				{
					$original_amount_str = '�����ۣ�'.$info['original_amount'];
				}
				else //û�ļ�
				{
					$original_amount_str = $detail_list[0]['prime_prices'] * intval($detail_list[0]['quantity']);
					$original_amount_str = 'ԭ�ۣ�' . number_format($original_amount_str, 2, '.', '');
				}
			}
			else
			{
				if( $info['is_change_price']==1 ) //�иļ�
				{
					$original_amount_str = $detail_list[0]['prime_prices'] * intval($detail_list[0]['quantity']);
					$original_amount_str = 'ԭ�ۣ�' . number_format($original_amount_str, 2, '.', '');
				}
				else //û�ļ�
				{
					$original_amount_str = '';
				}
			}
			$info['goods_promotion_info'] = $goods_promotion_info;
			$info['original_amount_str'] = $original_amount_str;

			//����Ʒ���ƺ�ͼƬ���һ�㵽order��������Ϣ��Ŀǰ��Ҫ�������߰��ã�
			$info['goods_name'] = $detail_list[0]['goods_name'];
			$info['goods_images'] = $detail_list[0]['goods_images'];

			foreach($detail_list as $detail_key=>$detail_info)
			{
				$detail_info['service_time_str'] = '--';
				if( $detail_info['service_time']>0 )
				{
					$detail_info['service_time_str'] = date('Y.m.d H:i', $detail_info['service_time']);
				}
				$detail_list[$detail_key] = $detail_info;
			}
			$info['detail_list'] = $detail_list;

			//����ǩ�����б�
			$code_list = $this->get_code_list_all($info['order_id']);
			foreach($code_list as $code_key=>$code_info)
			{
				$hash = qrcode_hash($info['order_id'], $info['order_id'], $code_info['code_sn']);
				$code_info['qr_code_url'] = "http://yp.yueus.com/mobile/action/check_qrcode.php?event_id={$info['order_id']}&enroll_id={$info['order_id']}&code={$code_info['code_sn']}&hash={$hash}";
				$code_info['name'] = 'ǩ����';
				$code_list[$code_key] = $code_info;
			}
			$info['code_list'] = $code_list;

			//����״̬�����б�
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
					$process_content_tmp = str_replace("{buyer_nickname}", "��", $process_content_tmp);
				}
				elseif( $login_user_id==$info['seller_user_id'] )
				{
					$process_content_tmp = str_replace("{seller_nickname}", "��", $process_content_tmp);
				}
				$process_content_tmp = str_replace("{seller_nickname}", $info['seller_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{buyer_nickname}", $info['buyer_name'], $process_content_tmp);
				$process_content_tmp = str_replace("{sys_nickname}", $sys_nickname, $process_content_tmp);

				//������������������
				$overall_score_arr_tmp = array();
				$process_remark_tmp = '';
				if( $process_info['process_action']=='����' )
				{
					$mall_comment_obj = POCO::singleton('pai_mall_comment_class');
					if( $process_info['process_by']=='seller' )
					{
						/*
						 * ��ȡ�̼Ҷ������߶�������Ʒ����
						* @param int $order_id
						* @param int $goods_id
						* @return array
						*/
						$comment_info_tmp = $mall_comment_obj->get_buyer_comment_info($info['order_id'], 0);
					}
					elseif( $process_info['process_by']=='buyer' )
					{
						/*
						 * ��ȡ�����߶��̼Ҷ�������Ʒ����
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

			//������ʾ
			$expire_str = '';
			switch ($status_tmp)
			{
				case self::STATUS_WAIT_PAY:
					$expire_str = '�µ���1Сʱ��֧���������Զ��ر�';
					break;
				case self::STATUS_WAIT_CONFIRM:
					$expire_str = '֧����24Сʱ��ȷ�϶������Զ��ر�';
					break;
				case self::STATUS_WAIT_SIGN:
					if( $detail_list[0]['service_time']>=$cur_time )
					{
						$expire_str = '�������ʼ����12Сʱ�����������˿�';
					}
					else
					{
						$expire_str = '����ʼ��48Сʱ��ǩ�����Զ������̼��˻�';
					}
					break;
				default:
					$expire_str = '';
					break;
			}
			$info['expire_str'] = $expire_str;

			$list[$key] = $info;
		}
		return $list;
	}

	/**
	 * ��ȡ��ϸ
	 * @param int $order_detail_id
	 * @return array
	 */
	public function get_detail_info($order_detail_id)
	{
		$order_detail_id = intval($order_detail_id);
		if( $order_detail_id<1 )
		{
			return array();
		}
		$this->set_mall_order_detail_tbl();
		return $this->find("order_detail_id={$order_detail_id}");
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @param boolean $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_detail_list($order_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$order_id = intval($order_id);
		
		//�����ѯ����
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
		
		//��ѯ
		$this->set_mall_order_detail_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @return array
	 */
	public function get_detail_list_all($order_id)
	{
		return $this->get_detail_list($order_id, false, '', 'order_detail_id ASC', '0,99999999');
	}

	/**
	 * ��ȡ��Ϣ
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
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @return array
	 */
	public function get_code_list_all($order_id)
	{
		return $this->get_code_list($order_id, false, '', 'code_id ASC', '0,99999999');
	}


	/**
	 * ��ȡ�б�
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

		//�����ѯ����
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

		//��ѯ
		$this->set_mall_order_code_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
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

		//�����ѯ����
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

		//��ѯ
		$this->set_mall_order_process_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}

	/**
	 * ��ȡ�б�
	 * @param int $order_id ����ID
	 * @param string $order_by
	 * @return array
	 */
	public function get_process_list_all($order_id, $order_by='process_id ASC')
	{
		return $this->get_process_list($order_id, false, '', $order_by, '0,99999999');
	}

	/**
	 * ��ȡ��������
	 * @param int $status
	 * @param int $type_id
	 * @return int
	 */
	private function get_expire_seconds($status, $type_id)
	{
		$expire_seconds = 0;
		
		$status = intval($status);
		$type_id = intval($type_id);
		if( $status===self::STATUS_WAIT_PAY ) //��֧��
		{
			$expire_seconds_arr = array(
				31 => 1*3600, //ģ�ط���1Сʱ
				//31 => 300, //Ϊ������ԣ���ʱ5����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 1*3600; //Ĭ��1Сʱ
				//$expire_seconds = 300; //Ϊ������ԣ���ʱ5����
			}
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM ) //��ȷ��
		{
			$expire_seconds_arr = array(
				31 => 24*3600, //ģ�ط���24Сʱ
				//31 => 600, //Ϊ������ԣ���ʱ10����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 24*3600; //Ĭ��24Сʱ
				//$expire_seconds = 600; //Ϊ������ԣ���ʱ10����
			}
		}
		elseif( $status===self::STATUS_WAIT_SIGN ) //��ǩ��
		{
			$expire_seconds_arr = array(
				31 => 48*3600, //ģ�ط���48Сʱ
				//31 => 900, //Ϊ������ԣ���ʱ15����
			);
			if( array_key_exists($type_id, $expire_seconds_arr) )
			{
				$expire_seconds = $expire_seconds_arr[$type_id];
			}
			else
			{
				$expire_seconds = 48*3600; //Ĭ��48Сʱ
				//$expire_seconds = 900; //Ϊ������ԣ���ʱ15����
			}
		}
		
		return $expire_seconds;
	}

	/**
	 * ��ʹ���Ż�ȯ
	 * @param array $order_info
	 * @return array array('result'=>0, 'message'=>'', 'used_amount'=>0)
	 */
	private function not_use_order_coupon($order_info)
	{
		$result = array('result'=>0, 'message'=>'', 'used_amount'=>0);

		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$total_amount = $order_info['total_amount']*1;
		$is_use_coupon = intval($order_info['is_use_coupon']);
		$is_pay = intval($order_info['is_pay']);

		//��鶩��
		if( $is_pay!=0 )
		{
			$result['result'] = -2;
			$result['message'] = '֧��״̬����';
			return $result;
		}
		if( $is_use_coupon==0 )
		{
			$result['result'] = 1;
			$result['message'] = '�˵�û��ʹ��ȯ';
			return $result;
		}

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//��ʹ���Ż�ȯ
		$coupon_obj = POCO::singleton('pai_coupon_class');
		$not_use_ret = $coupon_obj->not_use_coupon_by_oid($this->channel_module, $order_id);
		if( $not_use_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -3;
			$result['message'] = $not_use_ret['message'];
			return $result;
		}

		//����Ϊ0
		$data = array(
			'discount_amount' => 0,
			'is_use_coupon' => 0,
			'coupon_sn' => '',
			'pending_amount' => $total_amount,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_use_coupon=1 AND is_pay=0 AND total_amount={$total_amount}");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '���²�ʹ���Ż�ʧ��';
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['used_amount'] = $not_use_ret['used_amount'];
		return $result;
	}

	/**
	 * �ύ����
	 * @param int $buyer_user_id ����û�ID
	 * @param array $detail_list ��Ʒ��ϸ�б�
	 * @param array $more_info ������Ϣ
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 *
	 * $detail_list = array( array(
	 * 	'goods_id' => 0, //��ƷID
	 * 	'prices_type_id' => '',
	 * 	'service_time' => 0, //����ʱ��
	 * 	'service_location_id' => '',
	 * 	'service_address' => '',
	 *  'service_people' => 0,
	 *  'prices' => 0, //���ۣ���������������������
	 * 	'quantity' => 0, //����
	 *  'goods_promotion_id' => 0, //��Ʒ����ID
	 * ), ... );
	 *
	 * $more_info = array(
	 * 	'seller_user_id' => 0, //�����û�ID����������������������
	 * 	'description' => '', //��������ע
	 *  'is_auto_accept' => 0, //�Ƿ��Զ����ܣ��µ���֧�������ܲ�����֪ͨ
	 *  'is_auto_sign' => 0, //�Ƿ��Զ�ǩ����ǩ�������۲�����֪ͨ
	 *  'referer' => '', //������Դ��app weixin pc wap oa
	 * );
	 *
	 */
	public function submit_order($buyer_user_id, $detail_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'');

		//������
		$buyer_user_id = intval($buyer_user_id);
		if( $buyer_user_id<1 || !is_array($detail_list) || count($detail_list)!=1 || !is_array($detail_list[0]) || empty($detail_list[0]) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$detail_info = $detail_list[0];
		$detail_goods_id = intval($detail_info['goods_id']);
		if( $detail_goods_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '����ID����';
			return $result;
		}

		//�Ƿ��������true�������false��������
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		$is_special = $goods_obj->check_goods_special($detail_goods_id);
		if( $is_special )
		{
			return $this->submit_order_special($buyer_user_id, $detail_list, $more_info);
		}

		//��ȡ������Ϣ
		$goods_info = $goods_obj->get_goods_info($detail_goods_id);
		if( empty($goods_info) )
		{
			$result['result'] = -6;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$type_id = intval($goods_info['goods_data']['type_id']);
		$seller_id = intval($goods_info['goods_data']['seller_id']);
		$store_id = intval($goods_info['goods_data']['store_id']);
		$seller_user_id = intval($goods_info['goods_data']['user_id']);
		$detail_goods_name = trim($goods_info['goods_data']['titles']);
		$detail_goods_images = trim($goods_info['goods_data']['images']);
		$detail_goods_location_ids = trim($goods_info['goods_data']['location_id']);
		$detail_goods_version = trim($goods_info['goods_data']['version']);
		if( $seller_user_id<1 )
		{
			$result['result'] = -7;
			$result['message'] = '�̼��û�IDΪ��';
			return $result;
		}
		if( $buyer_user_id==$seller_user_id )
		{
			$result['result'] = -8;
			$result['message'] = '�ף����ܹ����Լ����۵ķ���Ŷ';
			return $result;
		}

		//��ǰʱ��
		$cur_time = time();

		//��������е�����
		$detail_prices_type_id = intval($detail_info['prices_type_id']);
		$detail_service_time = intval($detail_info['service_time']);
		$detail_service_location_id = intval($detail_info['service_location_id']);
		$detail_service_address = trim($detail_info['service_address']);
		$detail_service_people = intval($detail_info['service_people']);
		$detail_prime_prices = $detail_info['prices']*1; //�µ�ʱ�������ģ�����۸�
		$detail_quantity = intval($detail_info['quantity']);
		$detail_goods_promotion_id = intval($detail_info['goods_promotion_id']);
		$description = trim($more_info['description']);
		$is_auto_accept = intval($more_info['is_auto_accept']);
		$is_auto_sign = intval($more_info['is_auto_sign']);
		$referer = trim($more_info['referer']);
		if( $detail_quantity<1 )
		{
			$result['result'] = -5;
			$result['message'] = '������������';
			return $result;
		}
		if( $detail_service_time<1 )
		{
			$result['result'] = -3;
			$result['message'] = '����ʱ�����';
			return $result;
		}
		//Լ��ѵ���⴦���µ�ʱ���ñ�����ֹʱ���жϣ���������洢����ʱ�䣩
		//����ʱ��[60] => 2015-12-19 00:00 ������ֹʱ��[403] => 2016-01-31 00:00
		if( $type_id==5 )
		{
			$attend_deadline = strtotime(trim($goods_info['goods_att'][403])); //��goods_objȡ���������ڸ�ʽ��ǰ�˴�������Ϊʱ���
			$attend_deadline = empty($attend_deadline) ? intval($detail_info['service_time']) : $attend_deadline;
			if( $attend_deadline<=$cur_time )
			{
				$result['result'] = -4;
				$result['message'] = '�ѹ��˱�����ֹʱ��';
				return $result;
			}
		}
		else
		{
			if( $detail_service_time<=$cur_time )
			{
				$result['result'] = -4;
				$result['message'] = '�ѹ��˷���ʱ��';
				return $result;
			}
		}

		//��ȡƷ������
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_info = $type_obj->get_type_info($type_id);
		if( empty($type_info) )
		{
			$result['result'] = -9;
			$result['message'] = 'Ʒ��Ϊ��';
			return $result;
		}
		$type_name = trim($type_info['name']);

		//��ȡ�������
		$detail_prices_spec = '';
		if( $detail_prices_type_id>0 )
		{
			$detail_prices_info = array();
			foreach( $goods_info['prices_data_list'] as $prices_info_tmp )
			{
				if( $detail_prices_type_id==$prices_info_tmp['id'] ) $detail_prices_info = $prices_info_tmp;
			}
			if( empty($detail_prices_info) )
			{
				$result['result'] = -10;
				$result['message'] = '���Ϊ��';
				return $result;
			}
			$detail_prices_spec = trim($detail_prices_info['name']);
		}

		//�����ַ������ʡ������
		if( !empty($detail_service_location_id) )
		{
			$detail_service_address = get_poco_location_name_by_location_id($detail_service_location_id) . ' ' . $detail_service_address;
			$detail_service_address = trim($detail_service_address);
		}

		//��ȡ����۸�
		$goods_prices_rst = $goods_obj->get_goods_prices($detail_goods_id, array('num'=>$detail_quantity, 'type_id'=>$detail_prices_type_id));
		if( $goods_prices_rst['result']!=1 )
		{
			$result['result'] = -11;
			$result['message'] = '����۸�Ϊ��';
			return $result;
		}
		$detail_prime_prices = ($goods_prices_rst['data']['prices'])*1;
		if( $detail_prime_prices<=0 )
		{
			$result['result'] = -12;
			$result['message'] = '����۸�Ϊ0';
			return $result;
		}

		//�����û�ID
		$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
		$org_user_id = intval($org_info['org_id']);

		//����ʱ��
		$expire_time = $cur_time + $this->get_expire_seconds(self::STATUS_WAIT_PAY, $type_id);

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//������д���
		$detail_is_goods_promotion = 0;
		$detail_goods_promotion_amount = 0;
		$detail_promotion_ref_id = 0; //��������������ID
		if( $detail_goods_promotion_id>0 )
		{
			$use_goods_param_info = array(
				'org_user_id' => $org_user_id, //�����û�ID
				'location_id' => $detail_goods_location_ids, //�������ڵ���ID
				'seller_user_id' => $seller_user_id, //�̼��û�ID
				'mall_type_id' => $type_id, //Ʒ��ID
			);
			$use_prices_info = array(
				'channel_gid' => $detail_goods_id, //����
				'prices_type_id' => $detail_prices_type_id, //����
				'goods_prices' => $detail_prime_prices, //����
				'quantity' => $detail_quantity, //����
			);
			$promotion_obj = POCO::singleton('pai_promotion_class');
			$use_rst = $promotion_obj->use_promotion($buyer_user_id, 'goods', $detail_goods_promotion_id, $this->channel_module, $use_goods_param_info, $use_prices_info);
			if( $use_rst['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -13;
				$result['message'] = trim($use_rst['message']);
				return $result;
			}
			$detail_is_goods_promotion = 1;
			$detail_goods_promotion_amount = $use_rst['promotion_amount']; //������Ʒ�����˶��ٽ��
			$detail_promotion_ref_id = $use_rst['promotion_ref_id'];
		}

		//���������
		$detail_original_prices = $detail_prime_prices - $detail_goods_promotion_amount;
		$detail_original_amount = $detail_original_prices * $detail_quantity;
		$detail_is_change_price = 0;
		$detail_prices = $detail_original_prices;
		$detail_amount = $detail_prices * $detail_quantity;
		if( $detail_prices<=0 || $detail_amount<=0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -14;
			$result['message'] = '�����������Ϊ0';
			return $result;
		}

		//����������
		$check_ret = $goods_obj->check_can_buy($detail_goods_id, array('num'=>$detail_quantity, 'type_id'=>$detail_prices_type_id));
		if( $check_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -15;
			$result['message'] = trim($check_ret['message']);
			return $result;
		}

		//����ԭ���
		$order_prime_amount = $detail_amount;

		//�������д���
		$order_promotion_amount = 0;
		$is_order_promotion = 0;
		$order_promotion_id = 0;
		$order_promotion_ref_id = 0;

		//���㶩�����
		$order_original_amount = $order_prime_amount - $order_promotion_amount;
		$order_is_change_price = 0;
		$order_total_amount = $order_original_amount;
		if( $order_original_amount<=0 || $order_total_amount<=0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -17;
			$result['message'] = '�����󶩵����Ϊ0';
			return $result;
		}

		//���涩��
		$order_data = array(
			'order_type' => 'detail',
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
			'is_auto_accept' => $is_auto_accept,
			'is_auto_sign' => $is_auto_sign,
			'is_special' => 0, //��������
			'referer' => $referer,
			'status' => self::STATUS_WAIT_PAY,
			'expire_time' => $expire_time,
			'lately_time'  => $cur_time,
			'add_time' => $cur_time,
		);
		$order_id = $this->add_order($order_data);
		if( $order_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -18;
			$result['message'] = '��������ʧ��';
			return $result;
		}
		$order_sn = rand(10, 99) . $order_id . rand(0, 9);
		$rst = $this->update_order(array('order_sn'=>$order_sn), $order_id);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -19;
			$result['message'] = '����������ʧ��';
			return $result;
		}

		//��������
		if( $order_promotion_ref_id>0 )
		{
			//�Ѷ���ID�����µ�����ϵͳ
		}

		//������ϸ
		$detail_data = array(
			'order_id' => $order_id,
			'type_id' => $type_id,
			'goods_id' => $detail_goods_id,
			'goods_name' => $detail_goods_name,
			'goods_images' => $detail_goods_images,
			'prices_type_id' => $detail_prices_type_id,
			'prices_spec' => $detail_prices_spec,
			'goods_location_ids' => $detail_goods_location_ids,
			'goods_version' => $detail_goods_version,
			'service_time' => $detail_service_time,
			'service_location_id' => $detail_service_location_id,
			'service_address' => $detail_service_address,
			'service_people' => $detail_service_people,
			'prime_prices' => $detail_prime_prices, //��Ʒԭ�ۣ�����ǰ����Ʒ�۸�
			'goods_promotion_amount' => $detail_goods_promotion_amount, //��Ʒ�����������˶��ٽ�
			'is_goods_promotion' => $detail_is_goods_promotion,
			'goods_promotion_id' => $detail_goods_promotion_id,
			'original_prices' => $detail_original_prices, //���������Ʒ�۸���Ʒ�ļ�ǰ�ļ۸�
			'prices' => $detail_prices, //��Ʒʵ�ۣ���Ʒ�ļۺ����Ʒ�۸�
			'quantity' => $detail_quantity,
			'original_amount' => $detail_original_amount, //�ϼƽ��۸�*��������Ʒ�ļ�ǰ�Ľ�
			'amount' => $detail_amount, //�ϼƽ�ʵ��*��������Ʒ�ļۺ�Ľ�
			'is_change_price' => $detail_is_change_price, //��Ʒ�Ƿ��Ѹļۣ�0��1��
		);
		$order_detail_id = $this->add_order_detail($detail_data);
		if( $order_detail_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -21;
			$result['message'] = '���񱣴�ʧ��';
			return $result;
		}

		//�������
		if( $detail_promotion_ref_id>0 )
		{
			$promotion_obj = POCO::singleton('pai_promotion_class');
			$rst = $promotion_obj->update_ref_order(array('channel_oid'=>$order_id, 'channel_did'=>$order_detail_id), $detail_promotion_ref_id);
			if( !$rst )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -22;
				$result['message'] = '���´���ϵͳʧ��';
				return $result;
			}
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//��������
		$process = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�µ�',
			'process_result' => '��֧��',
			'process_content' => '{buyer_nickname} �µ��˷���: ' . $detail_goods_name,
			'process_time' => $cur_time,
		);
		$this->add_process($process);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->submit_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_id'] = $order_id;
		$result['order_sn'] = $order_sn;
		return $result;
	}

	/**
	 * �ύ����
	 * @param int $buyer_user_id ����û�ID
	 * @param array $detail_list ��Ʒ��ϸ�б�
	 * @param array $more_info ������Ϣ
	 * @return array array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'')
	 * @tutorial
	 *
	 * $detail_list = array( array(
	 * 	'goods_id' => 0, //��ƷID
	 * 	'prices_type_id' => '',
	 * 	'service_time' => 0, //����ʱ��
	 * 	'service_location_id' => '',
	 * 	'service_address' => '',
	 *  'service_people' => 0,
	 *  'prices' => 0, //���ۣ�����������
	 * 	'quantity' => 0, //����
	 * ), ... );
	 *
	 * $more_info = array(
	 * 	'seller_user_id' => 0, //�����û�ID������������
	 * 	'description' => '', //��������ע
	 *  'is_auto_accept' => 0, //�Ƿ��Զ����ܣ��µ���֧�������ܲ�����֪ͨ
	 *  'is_auto_sign' => 0, //�Ƿ��Զ�ǩ����ǩ�������۲�����֪ͨ
	 *  'referer' => '', //������Դ��app weixin pc wap oa
	 * );
	 *
	 */
	private function submit_order_special($buyer_user_id, $detail_list, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_id'=>0, 'order_sn'=>'');

		//������
		$buyer_user_id = intval($buyer_user_id);
		if( $buyer_user_id<1 || !is_array($detail_list) || count($detail_list)!=1 || !is_array($detail_list[0]) || empty($detail_list[0]) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$detail_info = $detail_list[0];
		$detail_goods_id = intval($detail_info['goods_id']);
		if( $detail_goods_id<1 )
		{
			$result['result'] = -2;
			$result['message'] = '����ID����';
			return $result;
		}

		//�Ƿ��������true�������false��������
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		$is_special = $goods_obj->check_goods_special($detail_goods_id);
		if( !$is_special )
		{
			$result['result'] = -3;
			$result['message'] = '���������';
			return $result;
		}

		//��ǰʱ��
		$cur_time = time();

		//��������е�����
		$detail_prices_type_id = intval($detail_info['prices_type_id']);
		$detail_service_time = intval($detail_info['service_time']);
		$detail_service_location_id = intval($detail_info['service_location_id']);
		$detail_service_address = trim($detail_info['service_address']);
		$detail_service_people = intval($detail_info['service_people']);
		$detail_prime_prices = $detail_info['prices']*1; //�µ�ʱ�������ģ�����۸�
		$detail_quantity = intval($detail_info['quantity']);
		$seller_user_id = intval($more_info['seller_user_id']); //�µ�ʱ�������ģ��̼�ID
		$description = trim($more_info['description']);
		$is_auto_accept = intval($more_info['is_auto_accept']);
		$is_auto_sign = intval($more_info['is_auto_sign']);
		$referer = trim($more_info['referer']);
		if( $detail_service_time<1 )
		{
			$result['result'] = -4;
			$result['message'] = '����ʱ�����';
			return $result;
		}
		if( $detail_prime_prices<=0 )
		{
			$result['result'] = -5;
			$result['message'] = '����۸�Ϊ0';
			return $result;
		}
		if( $detail_quantity<1 )
		{
			$result['result'] = -6;
			$result['message'] = '������������';
			return $result;
		}
		if( $seller_user_id<1 )
		{
			$result['result'] = -7;
			$result['message'] = '�̼��û�IDΪ��';
			return $result;
		}
		if( $buyer_user_id==$seller_user_id )
		{
			$result['result'] = -8;
			$result['message'] = '�ף����ܹ����Լ����۵ķ���Ŷ';
			return $result;
		}

		//��ȡ�̼���Ϣ
		$seller_obj = POCO::singleton('pai_mall_seller_class');
		$seller_info = $seller_obj->get_seller_info($seller_user_id, 2);
		if( empty($seller_info) )
		{
			$result['result'] = -9;
			$result['message'] = '�̼�Ϊ��';
			return $result;
		}
		$seller_id = intval($seller_info['seller_data']['seller_id']);
		$store_id = intval($seller_info['seller_data']['company'][0]['store'][0]['store_id']);

		//��ȡ������Ϣ
		$goods_info = $goods_obj->get_goods_info($detail_goods_id);
		if( empty($goods_info) )
		{
			$result['result'] = -10;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$type_id = intval($goods_info['goods_data']['type_id']);
		$detail_goods_name = trim($goods_info['goods_data']['titles']);
		$detail_goods_images = trim($goods_info['goods_data']['images']);
		$detail_goods_location_ids = trim($goods_info['goods_data']['location_id']);
		$detail_goods_version = trim($goods_info['goods_data']['version']);

		//��ȡƷ������
		$type_obj = POCO::singleton('pai_mall_goods_type_class');
		$type_info = $type_obj->get_type_info($type_id);
		if( empty($type_info) )
		{
			$result['result'] = -11;
			$result['message'] = 'Ʒ��Ϊ��';
			return $result;
		}
		$type_name = trim($type_info['name']);

		//��ȡ�������
		$detail_prices_spec = '';
		if( $detail_prices_type_id>0 )
		{
			$detail_prices_info = array();
			foreach( $goods_info['prices_data_list'] as $prices_info_tmp )
			{
				if( $detail_prices_type_id==$prices_info_tmp['id'] ) $detail_prices_info = $prices_info_tmp;
			}
			if( empty($detail_prices_info) )
			{
				$result['result'] = -12;
				$result['message'] = '���Ϊ��';
				return $result;
			}
			$detail_prices_spec = trim($detail_prices_info['name']);
		}

		//�����ַ������ʡ������
		if( !empty($detail_service_location_id) )
		{
			$detail_service_address = get_poco_location_name_by_location_id($detail_service_location_id) . ' ' . $detail_service_address;
			$detail_service_address = trim($detail_service_address);
		}

		//�����û�ID
		$relate_org_obj = POCO::singleton('pai_model_relate_org_class');
		$org_info = $relate_org_obj->get_org_info_by_user_id($seller_user_id);
		$org_user_id = intval($org_info['org_id']);

		//����ʱ��
		$expire_time = $cur_time + $this->get_expire_seconds(self::STATUS_WAIT_PAY, $type_id);

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//������д���
		$detail_is_goods_promotion = 0;
		$detail_goods_promotion_amount = 0;
		$detail_goods_promotion_id = 0;
		$detail_promotion_ref_id = 0; //��������������ID

		//���������
		$detail_original_prices = $detail_prime_prices - $detail_goods_promotion_amount;
		$detail_original_amount = $detail_original_prices * $detail_quantity;
		$detail_is_change_price = 0;
		$detail_prices = $detail_original_prices;
		$detail_amount = $detail_prices * $detail_quantity;
		if( $detail_prices<=0 || $detail_amount<=0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -13;
			$result['message'] = '�����������Ϊ0';
			return $result;
		}

		//����ԭ���
		$order_prime_amount = $detail_amount;

		//�������д���
		$order_promotion_amount = 0;
		$is_order_promotion = 0;
		$order_promotion_id = 0;
		$order_promotion_ref_id = 0;

		//���㶩�����
		$order_original_amount = $order_prime_amount - $order_promotion_amount;
		$order_is_change_price = 0;
		$order_total_amount = $order_original_amount;
		if( $order_original_amount<=0 || $order_total_amount<=0 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -14;
			$result['message'] = '�����󶩵����Ϊ0';
			return $result;
		}

		//���涩��
		$order_data = array(
			'order_type' => 'detail',
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
			'is_auto_accept' => $is_auto_accept,
			'is_auto_sign' => $is_auto_sign,
			'is_special' => 1, //�������
			'referer' => $referer,
			'status' => self::STATUS_WAIT_PAY,
			'expire_time' => $expire_time,
			'lately_time'  => $cur_time,
			'add_time' => $cur_time,
		);
		$order_id = $this->add_order($order_data);
		if( $order_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -15;
			$result['message'] = '��������ʧ��';
			return $result;
		}
		$order_sn = rand(10, 99) . $order_id . rand(0, 9);
		$rst = $this->update_order(array('order_sn'=>$order_sn), $order_id);
		if( !$rst )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -16;
			$result['message'] = '����������ʧ��';
			return $result;
		}

		//��������
		if( $order_promotion_ref_id>0 )
		{
			//�Ѷ���ID�����µ�����ϵͳ
		}

		//������ϸ
		$detail_data = array(
			'order_id' => $order_id,
			'type_id' => $type_id,
			'goods_id' => $detail_goods_id,
			'goods_name' => $detail_goods_name,
			'goods_images' => $detail_goods_images,
			'prices_type_id' => $detail_prices_type_id,
			'prices_spec' => $detail_prices_spec,
			'goods_location_ids' => $detail_goods_location_ids,
			'goods_version' => $detail_goods_version,
			'service_time' => $detail_service_time,
			'service_location_id' => $detail_service_location_id,
			'service_address' => $detail_service_address,
			'service_people' => $detail_service_people,
			'prime_prices' => $detail_prime_prices, //��Ʒԭ�ۣ�����ǰ����Ʒ�۸�
			'goods_promotion_amount' => $detail_goods_promotion_amount, //��Ʒ�����������˶��ٽ�
			'is_goods_promotion' => $detail_is_goods_promotion,
			'goods_promotion_id' => $detail_goods_promotion_id,
			'original_prices' => $detail_original_prices, //���������Ʒ�۸���Ʒ�ļ�ǰ�ļ۸�
			'prices' => $detail_prices, //��Ʒʵ�ۣ���Ʒ�ļۺ����Ʒ�۸�
			'quantity' => $detail_quantity,
			'original_amount' => $detail_original_amount, //�ϼƽ��۸�*��������Ʒ�ļ�ǰ�Ľ�
			'amount' => $detail_amount, //�ϼƽ�ʵ��*��������Ʒ�ļۺ�Ľ�
			'is_change_price' => $detail_is_change_price, //��Ʒ�Ƿ��Ѹļۣ�0��1��
		);
		$order_detail_id = $this->add_order_detail($detail_data);
		if( $order_detail_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -17;
			$result['message'] = '���񱣴�ʧ��';
			return $result;
		}

		//�������
		if( $detail_promotion_ref_id>0 )
		{
			//�Ѷ���ID����ϸID�����µ�����ϵͳ
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//��������
		$process = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�µ�',
			'process_result' => '��֧��',
			'process_content' => '{buyer_nickname} �µ��˷���: ' . $detail_goods_name,
			'process_time' => $cur_time,
		);
		$this->add_process($process);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->submit_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_id'] = $order_id;
		$result['order_sn'] = $order_sn;
		return $result;
	}

	/**
	 * ����֧��ҳ�Ľ��Ա���ʾ
	 * @param $order_sn
	 * @param $user_id
	 * @param $is_available_balance
	 * @param string $coupon_sn
	 * @return array
	 */
	public function cal_pay_order($order_sn, $user_id, $is_available_balance, $coupon_sn='')
	{
		$result = array('result'=>0, 'message'=>'', 'response_data'=>array());

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		$is_available_balance = intval($is_available_balance);
		$coupon_sn = trim($coupon_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']);
		$total_amount = $order_info['total_amount']*1;
		$cur_amount = $total_amount;
		$status = intval($order_info['status']);

		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$is_goods_promotion = intval($detail_list[0]['is_goods_promotion']);
		$goods_promotion_id = intval($detail_list[0]['goods_promotion_id']);

		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -4;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -5;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -5;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -5;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -5;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬����';
			return $result;
		}

		$response_data = array(
			'total_amount' => number_format($total_amount, 2, '.', ''),
			'is_allow_coupon' => 1, //�Ƿ�ʹ���Ż�ȯ
			'coupon_sn' => '', //�Ż�ȯ��
			'batch_name' => '', //�Ż�ȯ����
			'coupon_amount' => 0, //�Ż�ȯ���
			'is_available_balance' => $is_available_balance, //�Ƿ�ʹ��Ǯ��
			'available_balance' => 0, //Ǯ��ʣ����
			'use_balance' => 0, //Ǯ��ʹ�ý��
			'is_use_third_party_payment' => 1, //�Ƿ�ʹ�õ�����֧����ʽ
			'pending_amount' => 0, //����֧���۸�
		);

		//�Ƿ�����ʹ���Ż�ȯ
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

		//�����Ż�ȯ
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
				$cur_amount = bcsub($cur_amount, $response_data['coupon_amount'],2);
//				$cur_amount = $cur_amount - $response_data['coupon_amount'];
			}
		}

		//��ȡǮ����Ϣ
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
		$result['message'] = '�ɹ�';
		$result['response_data'] = $response_data;
		return $result;
	}

	/**
	 * �ύ֧��
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @param double $available_balance ҳ�浱ǰ���
	 * @param int $is_available_balance �Ƿ�ʹ����0�� 1��
	 * @param string $third_code ֧����ʽ alipay_purse tenpay_wxapp�����û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $redirect_url ֧���ɹ�����ת��url ���û�ʹ�����ȫ��֧��ʱ��Ϊ��
	 * @param string $notify_url
	 * @param string $coupon_sn
	 * @param array $more_info array('page_total_amount'=>0)
	 * @return array array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'')
	 * result 1��ȡ������֧����2���֧���ɹ�
	 */
	public function submit_pay_order($order_sn, $user_id, $available_balance, $is_available_balance, $third_code, $redirect_url, $notify_url='', $coupon_sn='', $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'payment_no'=>'', 'request_data'=>'');

		//������
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
			$result['message'] = '��������';
			return $result;
		}
		if( !is_array($more_info) ) $more_info = array();
		$page_total_amount = $more_info['page_total_amount']*1;

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$type_id = intval($order_info['type_id']);
		$is_order_promotion = intval($order_info['is_order_promotion']);
		$order_promotion_id = intval($order_info['order_promotion_id']); //��������ID
		$total_amount = $order_info['total_amount']*1;
		$pending_amount = $total_amount;
		$status = intval($order_info['status']);
		$is_auto_accept = intval($order_info['is_auto_accept']);

		//��ȡ������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$goods_id = intval($detail_list[0]['goods_id']);
		$goods_name = trim($detail_list[0]['goods_name']);
		$is_goods_promotion = intval($detail_list[0]['is_goods_promotion']);
		$goods_promotion_id = intval($detail_list[0]['goods_promotion_id']); //��Ʒ����ID

		//��鶩��
		if( $user_id>0 && $user_id!=$buyer_user_id )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		if( $page_total_amount>0 && $page_total_amount!=$total_amount )
		{
			$result['result'] = -4;
			$result['message'] = '���������󣬿����Ѹļ�';
			return $result;
		}

		//�������˵ĵ��Ƿ����ʹ���Ż�ȯ
		if( strlen($coupon_sn)>0 && ($is_order_promotion==1 || $is_goods_promotion==1) )
		{
			$promotion_obj = POCO::singleton('pai_promotion_class');
			if( $is_order_promotion==1 )
			{
				$order_promotion_info = $promotion_obj->get_promotion_info($order_promotion_id);
				if( empty($order_promotion_info) || $order_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '��������������ʹ���Ż�ȯ';
					return $result;
				}
			}
			if( $is_goods_promotion==1 )
			{
				$goods_promotion_info = $promotion_obj->get_promotion_info($goods_promotion_id);
				if( empty($goods_promotion_info) || $goods_promotion_info['is_allow_coupon']!=1 )
				{
					$result['result'] = -5;
					$result['message'] = '��Ʒ����������ʹ���Ż�ȯ';
					return $result;
				}
			}
		}

		$cur_time = time();

		//��ȥ����һ�ε��Ż�ȯ
		$not_use_ret = $this->not_use_order_coupon($order_info);
		if( $not_use_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = '��ʹ���Ż�ȯʧ��';
			return $result;
		}

		$discount_amount = 0;
		$is_use_coupon = 0;
		if( strlen($coupon_sn)>0 )
		{
			//����ʼ
			POCO_TRAN::begin($this->getServerId());

			$param_info = array(
				'module_type' => $this->channel_module, //ģ������ waipai yuepai topic task_request mall_order
                'mall_order_type' => 'detail',
				'order_total_amount' => $total_amount, //�����ܽ��
				'model_user_id' => $seller_user_id, //ģ���û�ID�����ݾ�Լ��ȯ������
				'event_user_id' => $seller_user_id, //��֯���û�ID���������ĵ�����
                'event_id' => 0, // �ID���������ĵ�����
				'org_user_id' => $org_user_id, //����ID,
				'mall_type_id' => $type_id, //��ƷƷ��ID
				'seller_user_id' => $seller_user_id, //�����û�ID
				'mall_goods_id' => $goods_id, //��ƷID
                'mall_stage_id' => 0, //�����ID
			);
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$coupon_ret = $coupon_obj->use_coupon($buyer_user_id, 1, $coupon_sn, $this->channel_module, $order_id, $param_info);
			if( $coupon_ret['result']!=1 )
			{
				//����ع�
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
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '�Żݽ������';
				return $result;	//�Ż�ȯ�����ڶ�������ʹ�ɹ�ʹ��Ҳ�˻ء�
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
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -5;
				$result['message'] = '�����Żݽ��ʧ��';
				return $result;
			}

			//�����ύ
			POCO_TRAN::commmit($this->getServerId());
		}

		$payment_obj = POCO::singleton('pai_payment_class');

		//�����֧��
		if( $is_available_balance )
		{
			$account_info = $payment_obj->get_user_account_info($buyer_user_id);
			if( $account_info['available_balance']<$pending_amount )
			{
				//���㣬�ñ꣬����ʹ�õ�����
				$amount = $pending_amount - $account_info['available_balance'];
				$redirect_third = 1;
			}
			else
			{
				//����ʼ
				POCO_TRAN::begin($this->getServerId());

				//�����û������֧�������¶���״̬
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
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = $submit_ret['message'];
					return $result;
				}

				//֧������
				$pay_ret = $this->pay_order($order_info);
				if( !$pay_ret )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -7;
					$result['message'] = '֧��ʧ��';
					return $result;
				}

				//�����ύ
				POCO_TRAN::commmit($this->getServerId());

				//�¼�����
				$trigger_params = array(
					'order_sn' => $order_sn,
				);
				POCO::singleton('pai_mall_trigger_detail_class')->pay_order_after($trigger_params);

				//֧�����Զ�����
				if( $is_auto_accept==1 )
				{
					$this->accept_order_for_system($order_sn);
				}

				$result['result'] = 2;
				$result['message'] = '���֧���ɹ�';
				return $result;
			}
		}
		else
		{
			//ֱ���õ�����֧��
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
					$result['message'] = 'û�а�΢�ź�';
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
				$result['message'] = $recharge_ret['message'];//��ת��������֧����������  ��ϸ��Ϣ��recharge_ret';
				return $result;
			}

			$result['result'] = 1;
			$result['message'] = '����ת��������֧����';
			$result['payment_no'] = trim($recharge_ret['payment_no']);
			$result['request_data'] = trim($recharge_ret['request_data']);
			return $result;
		}

		$result['result'] = -10;
		$result['message'] = 'δ֪����';
		return $result;
	}

	/**
	 * ֧������
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
		$type_id = intval($order_info['type_id']);

		$cur_time = time();

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//����֧��״̬
		$expire_time = $cur_time + $this->get_expire_seconds(self::STATUS_WAIT_CONFIRM, $type_id);
		$data = array(
			'status' => self::STATUS_WAIT_CONFIRM,
			'is_pay' => 1,
			'pay_time' => $cur_time,
			'expire_time' => $expire_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_pay=0 AND status IN (" . self::STATUS_WAIT_PAY .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '֧��',
			'process_result' => '��ȷ��',
			'process_content' => '{buyer_nickname} ��֧������',
			'process_time' => $cur_time,
		);
		$process_id = $this->add_process($data);
		if( $process_id<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return false;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		return true;
	}

	/**
	 * ֧������������֧����Ϣ
	 * @param array $payment_info
	 * @return array array('result'=>0, 'message'=>'') result 1,֧���ɹ���else ֧��ʧ��
	 */
	public function pay_order_by_payment_info($payment_info)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		if( !is_array($payment_info) || empty($payment_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		$payment_status = intval($payment_info['status']);
		if( $payment_info['channel_module']!='recharge' || $payment_status!=8 )
		{
			$result['result'] = -2;
			$result['message'] = '֧������';
			return $result;
		}
		$payment_no = trim($payment_info['payment_no']);
		$third_total_fee = $payment_info['third_total_fee']*1; //ʵ�ս��
		$channel_param = trim($payment_info['channel_param']);

		if($third_total_fee<=0 )
		{
			$result['result'] = -3;
			$result['message'] = '������';
			return $result;
		}
		if( strlen($channel_param)<1 )
		{
			$result['result'] = -4;
			$result['message'] = '֧������';
			return $result;
		}
		$channel_param_arr = unserialize($channel_param);
		if( empty($channel_param_arr) )
		{
			$result['result'] = -5;
			$result['message'] = '֧������';
			return $result;
		}
		$order_id = intval($channel_param_arr['enroll_id_str']);

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -6;
			$result['message'] = '����Ϊ��';
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

		//��ȡ������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -6;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$goods_name = trim($detail_list[0]['goods_name']);

		//��֧��
		if( $order_info['is_pay']==1 )
		{
			if( $payment_no==$order_info['payment_no'] )
			{
				$result['result'] = 1;
				$result['message'] = '�ɹ�';
				return $result;
			}

			$result['result'] = -7;
			$result['message'] = '�ظ�֧��';
			return $result;
		}

		//ȫ���õ�����Ϊ0�������������һ��ʹ��Ϊ1
		if( $pending_amount>$third_total_fee )
		{
			$is_balance = 1;
		}
		else
		{
			$is_balance = 0;
		}

		//����ʼ
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
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -9;
			$result['message'] = $submit_ret['message'];
			return $result;
		}

		$pay_ret = $this->pay_order($order_info);
		if( !$pay_ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -10;
			$result['message'] = '֧��ʧ��';
			return $result;
		}

		$this->update_order(array('payment_no'=>$payment_no), $order_id);

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->pay_order_after($trigger_params);

		//֧�����Զ�����
		if( $is_auto_accept==1 )
		{
			$this->accept_order_for_system($order_sn);
		}

		$result['result'] = 1;
		$result['message'] = '֧���ɹ�';
		return $result;
	}

	/**
	 * ���ң����ܶ���
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$is_auto_sign = intval($order_info['is_auto_sign']);
		
		//��鶩��
		if( $user_id>0 && $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
		
		//���ܶ���
		$more_info = array(
			'accept_by' => 'seller',
			'cur_time' => $cur_time,
		);
		$accept_ret = $this->_accept_order($order_info, $more_info);
		if( $accept_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $accept_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '����',
			'process_result' => '��ǩ��',
			'process_content' => '{seller_nickname} �ѽ��ܶ���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->accept_order_after($trigger_params);
		
		//���ܺ��Զ�ǩ��
		if( $is_auto_sign==1 )
		{
			$this->sign_order_for_system($order_sn);
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ϵͳ�����ܶ���
	 * @param string $order_sn
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function accept_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		$is_auto_sign = intval($order_info['is_auto_sign']);
		
		//��鶩��
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		$cur_time = time();
	
		//���ܶ���
		$more_info = array(
			'accept_by' => 'sys',
			'cur_time' => $cur_time,
		);
		$accept_ret = $this->_accept_order($order_info, $more_info);
		if( $accept_ret['result']!=1 )
		{
			$result['result'] = -5;
			$result['message'] = $accept_ret['message'];
			return $result;
		}
		
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '����',
			'process_result' => '��ǩ��',
			'process_content' => '{sys_nickname} ϵͳ�����˶���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);
		
		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->accept_order_after($trigger_params);
		
		//���ܺ��Զ�ǩ��
		if( $is_auto_sign==1 )
		{
			$this->sign_order_for_system($order_sn);
		}
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}
	
	/**
	 * ���ܶ���
	 * @param array $order_info
	 * @param array $more_info array( 'accept_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function _accept_order($order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');
		
		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$type_id = intval($order_info['type_id']);
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$status = intval($order_info['status']);
		
		if( !is_array($more_info) ) $more_info = array();
		$accept_by = trim($more_info['accept_by']);
		$cur_time = intval($more_info['cur_time']);
		if( $cur_time<1 ) $cur_time = time();
		
		//���������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$service_time = $detail_list[0]['service_time']*1;
		
		//��鶩��
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			return $result;
		}
		
		//����ʼ
		POCO_TRAN::begin($this->getServerId());
		
		//����״̬
		$expire_time = $service_time + $this->get_expire_seconds(self::STATUS_WAIT_SIGN, $type_id);
		$data = array(
			'status' => self::STATUS_WAIT_SIGN,
			'accept_by' => $accept_by,
			'accept_time' => $cur_time,
			'expire_time' => $expire_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status IN (" . self::STATUS_WAIT_CONFIRM .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());
				
			$result['result'] = -4;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}
		
		//����ǩ����
		$detail_list = $this->get_detail_list_all($order_id);
		foreach($detail_list as $detail_info)
		{
			$code_sn = $this->generate_code_sn($buyer_user_id, $seller_user_id, $order_id, $detail_info['order_detail_id']);
			if( strlen($code_sn)<1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());
				
				$result['result'] = -5;
				$result['message'] = '����ǩ����ʧ��';
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
	 * ǩ������
	 * �����������ǩ������ҳ�ʾǩ���룬���ҵ���ɨ�뾵ͷ��
	 * @param string $code_sn ǩ����
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order($code_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//������
		$code_sn = trim($code_sn);
		$user_id = intval($user_id);
		if( strlen($code_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//��ȡǩ������Ϣ
		$code_info = $this->get_code_info_recently($code_sn);
		if( empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$order_id = intval($code_info['order_id']);
		$is_check = intval($code_info['is_check']);

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '��ǩ���벻��ȷ�������³���';
			$result['is_limit_error'] = 1;
			return $result;
		}
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = '��ǩ����֮ǰ�Ѿ�ǩ����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		$cur_time = time();

		//ǩ������
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => 'ǩ��',
			'process_result' => '�����',
			'process_content' => '{buyer_nickname} �ѳɹ�ǩ��',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->sign_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * ϵͳǩ������
	 * @param string $order_sn
	 * @param boolean $is_expired �Ƿ���Ϊ���ڣ�����ϵͳǩ��
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	public function sign_order_for_system($order_sn, $is_expired=false)
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			$result['is_limit_error'] = 1;
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			$result['is_limit_error'] = 0;
			return $result;
		}
		$order_id = trim($order_info['order_id']);
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//��ȡǩ����
		$code_list = $this->get_code_list_all($order_id);
		$code_info = $code_list[0];
		if( !is_array($code_list) || empty($code_list) || !is_array($code_info) || empty($code_info) )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ�������';
			$result['is_limit_error'] = 1;
			return $result;
		}
		$code_id = intval($code_info['code_id']);
		$is_check = intval($code_info['is_check']);

		//��鶩��
		if( $is_check==1 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ������ǩ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $is_check!=0 )
		{
			$result['result'] = -4;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		$cur_time = time();

		//ǩ������
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

		//��������
		if( $is_expired )
		{
			$process_content = '{sys_nickname} ������ʱδǩ�����������Զ������̼��˻�';
		}
		else
		{
			$process_content = '{sys_nickname} �ѳɹ�ǩ��';
		}
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => 'ǩ��',
			'process_result' => '�����',
			'process_content' => $process_content,
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->sign_order_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * ǩ������
	 * @param array $code_info
	 * @param array $order_info
	 * @param array $more_info array( 'sign_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0)
	 */
	private function _sign_order($code_info, $order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'order_sn'=>'', 'is_limit_error'=>0);

		//������
		if( !is_array($code_info) || empty($code_info) || !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
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

		//��鶩��
		if( $is_check!=0 )
		{
			$result['result'] = -2;
			$result['message'] = 'ǩ����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//����ǩ����״̬
		$more_info = array(
			'check_time' => $cur_time,
		);
		$ret = $this->update_code_check($code_id, $more_info);
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '����ǩ��״̬ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//���¶���״̬
		$data = array(
			'status' => self::STATUS_SUCCESS,
			'sign_time' => $cur_time,
			'sign_by' => $sign_by,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND status IN (" . self::STATUS_WAIT_SIGN .")");
		if( !$ret )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -5;
			$result['message'] = '���¶���״̬ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//���㶩��
		$end_ret = $this->end_order($order_id);
		if( $end_ret['result']!=1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -6;
			$result['message'] = '���㶩��ʧ��';
			$result['order_sn'] = $order_sn;
			$result['is_limit_error'] = 0;
			return $result;
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['order_sn'] = $order_sn;
		$result['is_limit_error'] = 0;
		return $result;
	}

	/**
	 * ���㶩��
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
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$buyer_user_id = intval($order_info['buyer_user_id']);
		$seller_user_id = intval($order_info['seller_user_id']);
		$org_user_id = intval($order_info['org_user_id']);
		$total_amount = $order_info['total_amount']*1;
		$discount_amount = $order_info['discount_amount']*1;
		$pending_amount = bcsub($total_amount, $discount_amount, 2);

		//��ȡ������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$goods_name = trim($detail_list[0]['goods_name']);

		//��������
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

		//�����Ż�ȯ
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
			$result['message'] = '��ɶ���ʧ��';
			return $result;
		}

		//�������
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
				$result['message'] = '���ִ���ʧ��';
				return $result;
			}
			$settle_rst = $promotion_obj->settle_promotion($ref_order_info['id']);
			if( $settle_rst['result']!=1 )
			{
				$result['result'] = -5;
				$result['message'] = '�������ʧ��';
				return $result;
			}
		}

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * ������۶���
	 * @param int $order_id
	 * @param int $user_id ����û�ID
	 * @param int $is_anonymous �Ƿ�����
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_buyer($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//����״̬
		$data = array(
			'is_buyer_comment' => 1,
			'buyer_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_buyer_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '����',
			'process_result' => '������',
			'process_content' => '{buyer_nickname} �������� {seller_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->comment_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * �������۶���
	 * @param int $order_id
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function comment_order_for_seller($order_id, $user_id, $is_anonymous)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_id = intval($order_id);
		$user_id = intval($user_id);
		$is_anonymous = intval($is_anonymous);
		if( strlen($order_id)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info_by_id($order_id);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_sn = trim($order_info['order_sn']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status!==self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//����״̬
		$data = array(
			'is_seller_comment' => 1,
			'seller_comment_time' => $cur_time,
			'lately_time' => $cur_time,
		);
		$ret = $this->update_order_by_where($data, "order_id={$order_id} AND is_seller_comment=0 AND status IN (". self::STATUS_SUCCESS .")");
		if( !$ret )
		{
			$result['result'] = -5;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '����',
			'process_result' => '������',
			'process_content' => '{seller_nickname} �������� {buyer_nickname}',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
			'is_anonymous' => $is_anonymous,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->comment_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		return $result;
	}

	/**
	 * �ܾ�����
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refuse_order($order_sn, $user_id=0)
	{
		return $this->close_wait_confirm_order_for_seller($order_sn, $user_id);
	}

	/**
	 * ���ҹرն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_seller($order_sn, $user_id=0)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		//�رն���
		if( $status===self::STATUS_WAIT_PAY )
		{
			return $this->close_wait_pay_order_for_seller($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_CONFIRM )
		{
			return $this->close_wait_confirm_order_for_seller($order_sn, $user_id);
		}
		elseif( $status===self::STATUS_WAIT_SIGN )
		{
			return $this->close_wait_sign_order_for_seller($order_sn, $user_id);
		}

		$result['result'] = -5;
		$result['message'] = 'δ֪����';
		return $result;
	}

	/**
	 * ��ҹرն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		//�رն���
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
		$result['message'] = 'δ֪����';
		return $result;
	}

	/**
	 * ��������˿�
	 * @param string $order_sn
	 * @param int $user_id
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function refund_order_for_buyer($order_sn, $user_id)
	{
		return $this->close_wait_sign_order_for_buyer($order_sn, $user_id);
	}

	/**
	 * ϵͳ�رն���
	 * ����֧�������˿�
	 * @param string $order_sn
	 * @param string $reason �ر�ԭ��
	 * @return array array('result'=>0, 'message'=>'')
	 */
	public function close_order_for_system($order_sn,$reason='')
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$status = intval($order_info['status']);

		//��鶩��
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		//�رն���
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
		$result['message'] = 'δ֪����';
		return $result;
	}

	/**
	 * ��ң��رմ�֧������
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} �ѹرն���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_pay_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ���ң��رմ�֧������
	 * @param string $order_sn ������
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{seller_nickname} �ѹرն���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_pay_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ϵͳ���رմ�֧������
	 * @param string $order_sn ������
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_pay_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{sys_nickname} ������ʱδ֧���ѹر�',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ��ң��رմ�ȷ�϶���
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} ��ȡ������',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_confirm_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ���ң��رմ�ȷ�϶������ܾ���
	 * @param string $order_sn ������
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '�ܾ�',
			'process_result' => '�ѹر�',
			'process_content' => '{seller_nickname} �Ѿܾ�����',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_confirm_order_for_seller_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ϵͳ���رմ�ȷ�϶���
	 * @param string $order_sn ������
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_confirm_order_for_system($order_sn)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '������ǩ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{sys_nickname} ������ʱδ�����ѹر�',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ��ң��رմ�ǩ������
	 * @param string $order_sn ������
	 * @param int $user_id ����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_buyer($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		//��ȡ����ǳ�
		$buyer_nickname = get_user_nickname_by_user_id($user_id);

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//���������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if ( empty($detail_list) )
		{
			$result['result'] = -2;
			$result['message'] = '��������Ϊ��';
			return $result;
		}
		$service_time = $detail_list[0]['service_time']*1;

		//��鶩��
		if( $user_id!=$order_info['buyer_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�Ƿ������˿�
		$allow_time = self::ALLOW_BUYER_REFUND_TIME;
		$service_time_prev = $service_time - $allow_time*3600; //ǰ12Сʱ
		if( $service_time_prev<$cur_time )
		{
			$result['result'] = -5;
			$result['message'] = '�������ʼ����12Сʱ�����������˿�';
			return $result;
		}

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'buyer',
			'process_user' => '���',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{buyer_nickname} ������Чʱ���������˿�˻������յ�����',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_sign_order_for_buyer_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ���ң��رմ�ǩ������
	 * @param string $order_sn ������
	 * @param int $user_id �����û�ID
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_seller($order_sn, $user_id)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$user_id = intval($user_id);
		if( strlen($order_sn)<1 || $user_id<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $user_id!=$order_info['seller_user_id'] )
		{
			$result['result'] = -3;
			$result['message'] = '�Ƿ�����';
			return $result;
		}
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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

		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'seller',
			'process_user' => '����',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => '{seller_nickname} �ر��˶���',
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * ϵͳ���رմ�ǩ������
	 * @param string $order_sn ������
	 * @param string $reason �ر�ԭ��
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function close_wait_sign_order_for_system($order_sn,$reason)
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		$order_sn = trim($order_sn);
		$reason = trim($reason);
		if( strlen($order_sn)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}

		//��ȡ������Ϣ
		$order_info = $this->get_order_info($order_sn);
		if( empty($order_info) )
		{
			$result['result'] = -2;
			$result['message'] = '����Ϊ��';
			return $result;
		}
		$order_id = intval($order_info['order_id']);
		$status = intval($order_info['status']);

		//��鶩��
		if( $status===self::STATUS_WAIT_PAY )
		{
			$result['result'] = -4;
			$result['message'] = '������֧��';
			return $result;
		}
		if( $status===self::STATUS_WAIT_CONFIRM )
		{
			$result['result'] = -4;
			$result['message'] = '������ȷ��';
			return $result;
		}
		if( $status===self::STATUS_CLOSED )
		{
			$result['result'] = -4;
			$result['message'] = '�����ѹر�';
			return $result;
		}
		if( $status===self::STATUS_SUCCESS )
		{
			$result['result'] = -4;
			$result['message'] = '���������';
			return $result;
		}
		if( $status!==self::STATUS_WAIT_SIGN )
		{
			$result['result'] = -4;
			$result['message'] = '����״̬����';
			return $result;
		}

		$cur_time = time();

		//�رն���
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
			$reason = '{sys_nickname} ϵͳ�ر��˶���';
		}
		//��������
		$data = array(
			'order_id' => $order_id,
			'process_by' => 'sys',
			'process_user' => 'ϵͳ',
			'process_action' => '�ر�',
			'process_result' => '�ѹر�',
			'process_content' => 'ϵͳ�رգ�ԭ��'.$reason,
			'process_time' => $cur_time,
		);
		$this->add_process($data);

		//�¼�����
		$trigger_params = array(
			'order_sn' => $order_sn,
		);
		POCO::singleton('pai_mall_trigger_detail_class')->close_wait_sign_order_for_system_after($trigger_params);

		$result['result'] = 1;
		$result['message'] = '�رճɹ�';
		return $result;
	}

	/**
	 * �رն���
	 * @param array $order_info
	 * @param array $more_info array( 'close_by'=>'', 'cur_time'=>0 )
	 * @return array array('result'=>0, 'message'=>'')
	 */
	private function _close_order($order_info, $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'');

		//������
		if( !is_array($order_info) || empty($order_info) )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
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

		//��鶩��
		if( !in_array($status, array(self::STATUS_WAIT_PAY, self::STATUS_WAIT_CONFIRM, self::STATUS_WAIT_SIGN), true) )
		{
			$result['result'] = -3;
			$result['message'] = '����״̬����';
			return $result;
		}

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		//����״̬
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
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -4;
			$result['message'] = '����״̬ʧ��';
			return $result;
		}

		//���������ϸ�б�
		$detail_list = $this->get_detail_list_all($order_id);
		if( empty($detail_list) )
		{
			$result['result'] = -5;
			$result['message'] = '��������Ϊ��';
			return $result;
		}

		//�������
		$goods_obj = POCO::singleton('pai_mall_goods_class');
		foreach($detail_list as $detail_info)
		{
			$goods_id_tmp = $detail_info['goods_id'];
			$goods_quantity_tmp = $detail_info['quantity'];
			$prices_type_id_tmp = $detail_info['prices_type_id'];
			if( !$is_special ) //��������
			{
				$change_ret = $goods_obj->change_goods_stock($goods_id_tmp, $goods_quantity_tmp, $prices_type_id_tmp); //ͨ���ӿ��޸���Ʒ���
				if( $change_ret!=1 )
				{
					//����ع�
					POCO_TRAN::rollback($this->getServerId());

					$result['result'] = -6;
					$result['message'] = "������ʧ��";
					return $result;
				}
			}
		}

		//�˻���������
		$promotion_rst = POCO::singleton('pai_promotion_class')->refund_promotion_by_oid($this->channel_module, $order_id);
		if( $promotion_rst['result']!==1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			$result['result'] = -7;
			$result['message'] = $promotion_rst['message'];
			return $result;
		}

		//����֧�������˿��ȯ
		if( $is_pay==1 )
		{
			$payment_obj = POCO::singleton('pai_payment_class');
			$cancel_ret = $payment_obj->cancel_event_v2($this->channel_module, $order_id);
			if( $cancel_ret['error']!==0 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -8;
				$result['message'] = 'ȡ������ʧ��';
				return $result;
			}

			//���Ż�ȯ
			$coupon_obj = POCO::singleton('pai_coupon_class');
			$refund_ret = $coupon_obj->refund_coupon_by_oid($this->channel_module, $order_id);
			if( $refund_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -9;
				$result['message'] = '�˻��Ż�ȯʧ��';
				return $result;
			}
		}
		else
		{
			//��δ�����ʹ���Ż�ȯ
			$not_use_ret = $this->not_use_order_coupon($order_info);
			if( $not_use_ret['result']!=1 )
			{
				//����ع�
				POCO_TRAN::rollback($this->getServerId());

				$result['result'] = -10;
				$result['message'] = '��ʹ���Ż�ȯʧ��';
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
	 * ��ȡĳ��ʱ�������ڱ��ܾ��Ķ�������
	 * @param  int   $type_id        ����id
	 * @param  int 	 $user_id		 ����id
	 * @param  int	 $start_time 	 ��ʼ��ѯʱ��
	 * @param  int	 $end_time		 ������ѯʱ��
	 * @return array
	 */
	public function get_order_refuse_by_lasting($type_id, $user_id, $start_time, $end_time)
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS count ';

		//�����ѯ����
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
	 * ��ȡĳ��ʱ����������Ʒ���۵�����
	 * @param  int   $type_id        ����id
	 * @param  int	 $start_time 	 ��ʼ��ѯʱ��
	 * @param  int	 $end_time		 ������ѯʱ��
	 * @param  int   $limit 		 ��ѯ����
	 * @return array
	 */
	public function get_goods_sales_ranking($type_id, $start_time, $end_time, $limit='0,9999999999')
	{
		$type_id = intval($type_id);
		$start_time = intval($start_time);
		$end_time = intval($end_time);

		$fields = ' d.goods_id,COUNT(d.goods_id) AS sales_count ';

		//�����ѯ����
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
	 * ����ǩ����
	 * @param int $buyer_user_id
	 * @param int $seller_user_id
	 * @param int $order_id
	 * @param int $order_detail_id
	 * @return string
	 */
	private function generate_code_sn($buyer_user_id, $seller_user_id, $order_id, $order_detail_id)
	{
		//������
		$buyer_user_id = intval($buyer_user_id);
		$seller_user_id = intval($seller_user_id);
		$order_id = intval($order_id);
		$order_detail_id = intval($order_detail_id);
		if( $buyer_user_id<1 || $seller_user_id<1 || $order_id<1 || $order_detail_id<1 )
		{
			return '';
		}

		//����ʼ
		POCO_TRAN::begin($this->getServerId());

		$code_sn = '';
		$while_count = 0;
		while($while_count<9999)
		{
			//��ȡ����ַ���
			$rand_str = $this->get_rand_str(6);
			if( strlen($rand_str)<1 )
			{
				//ǩ����Ϊ��
				break;
			}

			//����Ƿ��ܸ���
			$c = $this->check_code_recently($rand_str);
			if( $c>0 )
			{
				$while_count++;
				continue;
			}

			//����
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

			//�ٴμ���Ƿ��ܸ���
			$c = $this->check_code_recently($rand_str);
			if( $c>1 )
			{
				//��������һ������Ҳ������
				$while_count++;
				continue;
			}

			$code_sn = $rand_str;
			break;
		}
		if( strlen($code_sn)<1 )
		{
			//����ع�
			POCO_TRAN::rollback($this->getServerId());

			return '';
		}

		//�����ύ
		POCO_TRAN::commmit($this->getServerId());

		return $code_sn;
	}

	/**
	 * ��ȡ����ַ���
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

		//��1λ������1��3��5��7��9����������������ϵͳ
		$pattern_str = '31597';
		$pattern_len = strlen($pattern_str);
		$code .= substr($pattern_str, rand(0, $pattern_len-1), 1);

		//��λ
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
