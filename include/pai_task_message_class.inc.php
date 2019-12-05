<?php
/**
 * ����
 * @author Henry
 * @copyright 2015-04-09
 */

class pai_task_message_class extends POCO_TDG
{
	/**
	 * ���캯��
	 */
	public function __construct()
	{
		$this->setServerId(101);
		$this->setDBName('pai_task_db');
	}
	
	/**
	 * ָ����
	 */
	private function set_task_message_tbl()
	{
		$this->setTableName('task_message_tbl');
	}
	
	/**
	 * ���
	 * @param array $data
	 * @return int
	 */
	private function add_message($data)
	{
		if( !is_array($data) || empty($data) )
		{
			return 0;
		}
		$this->set_task_message_tbl();
		return $this->insert($data, 'IGNORE');
	}
	
	/**
	 * �����Ѳ鿴
	 * @param int $message_id
	 * @param array $more_info array('read_time'=>0)
	 * @return boolean
	 */
	public function update_message_read($message_id, $more_info=array())
	{
		$message_id = intval($message_id);
		if( $message_id<1 )
		{
			return false;
		}
		if( !is_array($more_info) ) $more_info = array();
		$data = array(
			'is_read' => 1,
		);
		$data = array_merge($more_info, $data);
		$this->set_task_message_tbl();
		$affected_rows = $this->update($data, "message_id={$message_id} AND is_read=0");
		return $affected_rows>0?true:false;
	}
	
	/**
	 * ��ȡ��Ϣ
	 * @param int $message_id
	 * @return array
	 */
	public function get_message_info($message_id)
	{
		$message_id = intval($message_id);
		if( $message_id<1 )
		{
			return array();
		}
		$this->set_task_message_tbl();
		return $this->find("message_id={$message_id}");
	}
	
	/**
	 * ��ȡ�б�
	 * @param int $quotes_id
	 * @param int $user_id
	 * @param bool $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @param string $fields
	 * @return array|int
	 */
	public function get_message_list($quotes_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields='*')
	{
		$quotes_id = intval($quotes_id);
		
		//�����ѯ����
		$sql_where = '';
		
		if( $quotes_id>0 )
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= "quotes_id={$quotes_id}";
		}
		
		if (strlen($where_str) > 0)
		{
			if( strlen($sql_where) > 0 ) $sql_where .= ' AND ';
			$sql_where .= $where_str;
		}
		
		//��ѯ
		$this->set_task_message_tbl();
		if( $b_select_count )
		{
			return $this->findCount($sql_where);
		}
		
		return $this->findAll($sql_where, $limit, $order_by, $fields);
	}
	
	/**
	 * �ύ����
	 * @param int $from_user_id
	 * @param int $quotes_id
	 * @param string $message_type ��������
	 * @param string $message_content
	 * @param array $more_info array('pay_amount'=>0)
	 * @return array
	 * @tutorial
	 * ��������
	 * message���ԣ�quotes���ۣ�read_quotes�鿴���ۣ�read_profile�鿴���ϣ�hired��Ӷ
	 * declinedл����earnest֧������refund_coins�˻����⿨��review����
	 */
	public function submit_message($from_user_id, $quotes_id, $message_type, $message_content='', $more_info=array())
	{
		$result = array('result'=>0, 'message'=>'', 'message_id'=>0);
		
		$from_user_id = intval($from_user_id);
		$quotes_id = intval($quotes_id);
		$message_type = trim($message_type);
		$message_content = trim($message_content);
		if( !is_array($more_info) ) $more_info = array();
		
		if( $from_user_id<1 || $quotes_id<1 || strlen($message_type)<1 )
		{
			$result['result'] = -1;
			$result['message'] = '��������';
			return $result;
		}
		
		//��ȡ������Ϣ
		$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
		$quotes_info = $task_quotes_obj->get_quotes_info($quotes_id);
		if( empty($quotes_info) )
		{
			$result['result'] = -2;
			$result['message'] = '��������';
			return $result;
		}
		$request_id = intval($quotes_info['request_id']);
		$seller_user_id = intval($quotes_info['user_id']);
		
		//��ȡ������Ϣ
		$task_request_obj = POCO::singleton('pai_task_request_class');
		$request_info = $task_request_obj->get_request_info($request_id);
		if( empty($request_info) )
		{
			$result['result'] = -3;
			$result['message'] = '��������';
			return $result;
		}
		$buyer_user_id = intval($request_info['user_id']);
		
		//��ʼ���Է��û�ID
		$to_user_id = 0;
		if( $from_user_id==$seller_user_id )
		{
			$to_user_id = $buyer_user_id;
		}
		elseif( $from_user_id==$buyer_user_id )
		{
			$to_user_id = $seller_user_id;
		}
		if( $to_user_id<1 )
		{
			$result['result'] = -4;
			$result['message'] = '��������';
			return $result;
		}
		$from_user_nickname = get_user_nickname_by_user_id($from_user_id);
		//$to_user_nickname = get_user_nickname_by_user_id($to_user_id);
		
		$cur_time = time();
		
		$quotes_content = '';
		switch ($message_type)
		{
			case 'quotes': //����
				$price_tmp = trim($quotes_info['price']);
				$price_str_tmp = '��' . ((ceil($price_tmp)==$price_tmp)?$price_tmp*1:$price_tmp);
				$quotes_content = "���۹��ۣ�{$price_str_tmp}";
				break;
			case 'read_quotes': //�鿴����
				$message_content = "{$from_user_nickname} �Ѿ��鿴����ı���";
				break;
			case 'read_profile': //�鿴����
				$message_content = "{$from_user_nickname} �Ѿ��鿴����ĸ�������";
				break;
			case 'hired': //��Ӷ
				$message_content = "��ϲ�㣬{$from_user_nickname}�Ѿ�ȷ������ı��ۣ���ȥ��Ta��ϵ�ɡ�";
				break;
			case 'declined': //л��
				$message_content = "���ź� {$from_user_nickname}��û��ѡ���Ӷ��";
				break;
			case 'earnest': //֧������
				$pay_amount_tmp = trim($more_info['pay_amount']);
				$message_content = "{$from_user_nickname} �Ѿ�֧���˷���𣬷������Ϊ����{$pay_amount_tmp}";
				break;
			case 'review': //����
				$cur_time_str = date('Y-m-d H:i:s', $cur_time);
				$message_content = "{$from_user_nickname}��{$cur_time_str}��������";
				break;
			case 'refund_coins': //�˻����⿨
				$message_content = "����{$from_user_nickname}��48Сʱ��δ�鿴��ı��ۣ�������⿨�Ѿ����˻�������˻���";
				break;
			case 'message': //����
				 break;
			default:
				break;
		}
		
		$data = array(
			'message_type' => $message_type,
			'quotes_id' => $quotes_id,
			'request_id' => $request_id,
			'from_user_id' => $from_user_id,
			'to_user_id' => $to_user_id,
			'quotes_content' => $quotes_content,
			'message_content' => $message_content,
			'add_time' => $cur_time,
		);
		$message_id = $this->add_message($data);
		if( $message_id<1 )
		{
			$result['result'] = -5;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
		//�Ƿ���Ҫ����ǰ���Ӵ֣����ң�
		if( $to_user_id==$seller_user_id )
		{
			$task_quotes_obj->update_quotes_important($quotes_id, 1);
		}
		
		//�¼�����
		$trigger_params = array('message_id'=>$message_id);
		$task_trigger_obj = POCO::singleton('pai_task_trigger_class');
		$task_trigger_obj->message_submit_after($trigger_params);
		
		$result['result'] = 1;
		$result['message'] = '�ɹ�';
		$result['message_id'] = $message_id;
		return $result;
	}
	
	/**
	 * ������ϸ��Ϣ
	 * @param array $list
	 * @return array
	 */
	private function fill_message_detail_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		
		$cut_time = time();
		
		foreach($list as $key=>$info)
		{
			$message_type = trim($info['message_type']);
			$from_user_id = intval($info['from_user_id']);
			$from_user_nickname = get_user_nickname_by_user_id($from_user_id);
			$from_user_icon = get_user_icon($from_user_id);
			
			$to_user_id = intval($info['to_user_id']);
			$to_user_nickname = get_user_nickname_by_user_id($to_user_id);
			$to_user_icon = get_user_icon($to_user_id);
			
			$add_time = intval($info['add_time']);
			$add_time_str = yue_time_format_convert($add_time);
			
			$info['from_user_nickname'] = $from_user_nickname;
			$info['from_user_icon'] = $from_user_icon;
			$info['to_user_nickname'] = $to_user_nickname;
			$info['to_user_icon'] = $to_user_icon;
			$info['add_time_str'] = $add_time_str;
			$list[$key] = $info;
		}
		
		return $list;
	}
	
	/**
	 * ��ȡ���������Ϣ
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_message_info_lately_by_quotes_id($quotes_id)
	{
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "message_type IN ('message', 'quotes')";
		$message_list = $this->get_message_list($quotes_id, false, $where_str, 'message_id DESC', '0,1');
		$message_detail_list = $this->fill_message_detail_list($message_list);
		$message_detail_info = $message_detail_list[0];
		if( !is_array($message_detail_info) ) $message_detail_info = array();
		return $message_detail_info;
	}
	
	/**
	 * ��ȡ�����̬��Ϣ
	 * @param int $user_id
	 * @param int $quotes_id
	 * @return array
	 */
	public function get_feed_info_lately_by_user_id($user_id, $quotes_id)
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "message_type NOT IN ('message', 'quotes') AND to_user_id={$user_id}";
		$message_list = $this->get_message_list($quotes_id, false, $where_str, 'message_id DESC', '0,1');
		$message_detail_list = $this->fill_message_detail_list($message_list);
		$message_detail_info = $message_detail_list[0];
		if( !is_array($message_detail_info) ) $message_detail_info = array();
		return $message_detail_info;
	}
	
	/**
	 * ��ȡ�����б������û�ID
	 * @param int $user_id
	 * @param int $quotes_id
	 * @param string $b_select_count
	 * @param string $where_str
	 * @param string $order_by
	 * @param string $limit
	 * @return array|int
	 */
	public function get_message_list_by_user_id($user_id, $quotes_id, $b_select_count=false, $order_by='message_id ASC', $limit='0,20')
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $quotes_id<1 )
		{
			return array();
		}
		$where_str = "(message_type IN ('message', 'quotes') OR (message_type NOT IN ('message', 'quotes') AND to_user_id={$user_id}))";
		$message_list = $this->get_message_list($quotes_id, $b_select_count, $where_str, $order_by, $limit);
		return $this->fill_message_detail_list($message_list);
	}
	
	/**
	 * ��ȡδ�鿴����������
	 * @param int $user_id
	 * @param int $quotes_id
	 * @return int
	 */
	public function get_message_count_unread($user_id, $quotes_id)
	{
		$user_id = intval($user_id);
		$quotes_id = intval($quotes_id);
		if( $user_id<1 || $quotes_id<1 )
		{
			return 0;
		}
		$where_str = "to_user_id={$user_id} AND is_read=0";
		return $this->get_message_list($quotes_id, true, $where_str);
	}
	
}
