<?php
/*
 * ���۲�����
 */

class pai_task_review_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_task_db' );
		$this->setTableName ( 'task_review_tbl' );
	}
	
	/*
	 * �������
	 * @param array �ύ����
	 * return int
	 */
	public function add_review($insert_data)
	{
		if (empty ( $insert_data['quotes_id'] ) || empty ( $insert_data['request_id'] ) || empty ( $insert_data['from_user_id'] ) || empty ( $insert_data['to_user_id'] ))
		{
			$result['result'] = -3;
			$result['message'] = '��������';
			return $result;
		}
		
		
		$check_is_review = $this->check_is_review($insert_data['quotes_id'],$insert_data['from_user_id']);
		
		if($check_is_review)
		{
			$result['result'] = -2;
			$result['message'] = '�����۹���';
			return $result;
		}
		
		$insert_data['add_time'] = time();
		$ret = $this->insert ( $insert_data );
		
		if($ret)
		{
			$task_quotes_obj = POCO::singleton('pai_task_quotes_class');
			$quotes_info = $task_quotes_obj->get_quotes_info($insert_data['quotes_id']);
			
			//����������
			$review_ret = $task_quotes_obj->review_quotes($insert_data['quotes_id']);
			if( $review_ret )
			{
				//����
				$task_request_obj = POCO::singleton('pai_task_request_class');
				$end_ret = $task_request_obj->end_request($quotes_info['request_id']);
				
				//��־
				pai_log_class::add_log($end_ret, 'add_review', 'task_review');
			}
			
			$task_profile_obj = POCO::singleton('pai_task_profile_class');
			$task_profile_obj->update_average_review($quotes_info['profile_id'], $insert_data['rank']);
			
			$result['result'] = 1;
			$result['message'] = '���۳ɹ�';
			return $result;
		}
		else
		{
			$result['result'] = -1;
			$result['message'] = '����ʧ��';
			return $result;
		}
		
	}
	
	/*
	 * ��ȡ�����б�
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_review_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	
	/*
	 * ��ȡ�û������б�
	 * @param bool $b_select_count
	 * @param int $user_id
	 * @param string $limit 
	 * 
	 * return array
	 */
	public function get_user_review_list($b_select_count = false,$user_id='',$limit='0,10')
	{
	
		$user_id = ( int ) $user_id;
		
		$where_str = "to_user_id={$user_id}";
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} 
		else
		{
			$list = $this->findAll ( $where_str, $limit, 'add_time desc', '*' );
			return $this->fill_quotes_review_list($list);
		}
		
		return $ret;
	}
	
	/*
	 * ��ȡ�û������б�
	 * @param bool $b_select_count
	 * @param int $profile_id
	 * @param string $limit 
	 * 
	 * return array
	 */
	public function get_user_review_list_by_profile($b_select_count = false,$profile_id='',$limit='0,10')
	{
	
		$profile_id = ( int ) $profile_id;
		
		$where_str = "profile_id={$profile_id}";
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} 
		else
		{
			$list = $this->findAll ( $where_str, $limit, 'add_time desc', '*' );
			return $this->fill_quotes_review_list($list);
		}
		
		return $ret;
	}	


	/**
	 * ������ϸ��Ϣ
	 * @param array $list
	 * @return array
	 */
	private function fill_quotes_review_list($list)
	{
		if( !is_array($list) )
		{
			return $list;
		}
		
		
		foreach($list as $key=>$info)
		{
			$list[$key]['add_time'] = date("Y-m-d",$info['add_time']);
			$list[$key]['from_nickname'] = get_user_nickname_by_user_id($info['from_user_id']);
			$list[$key]['from_user_icon'] = get_user_icon($info['from_user_id'], 165);
		}
		
		return $list;
	}
	
	/*
	 * �û��Ƿ��ѶԱ�������
	 * @param int $quote_id
	 * @param int $user_id
	 * @return bool
	 */
	public function check_is_review($quote_id,$user_id)
	{
		$quote_id = ( int ) $quote_id;
		$user_id = ( int ) $user_id;
		
		$where_str = "quotes_id={$quote_id} and from_user_id={$user_id}";
		$ret = $this->findCount ( $where_str );
		
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

}

?>