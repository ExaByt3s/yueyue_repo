<?php
/*
 * OAģ���б������
 */

class pai_model_oa_model_list_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_model_list_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_model($insert_data)
	{
        global $yue_login_id;
		
		if (empty ( $insert_data['order_id'] ))
		{
			trace ( "����ID����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}
		
		if (empty ( $insert_data['user_id'] ))
		{
			trace ( "�û�ID����Ϊ��", basename ( __FILE__ ) . " ��:" . __LINE__ . " ����:" . __METHOD__ );
			return false;
		}

        $insert_data['recommend_user_id'] = $yue_login_id;
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data );
	
	}
    
  
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_model($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	
	/*
	 * ��ȡ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_model_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_model_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	
	/*
	 * ɾ��
	 *  
	 * @param int $user_id
	 */
	public function del_model($id)
	{
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':id����Ϊ��' );
		}
		
		$where_str = "id = {$id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * �Ƿ������
	 */
	public function check_repeat($user_id,$order_id)
	{
		$user_id = (int)$user_id;
		$order_id = (int)$order_id;
		
		$where_str = "user_id={$user_id} and order_id={$order_id}";
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
	
	
	/*
	 * �Ƿ���ѡ����ʵ�ģ��
	 */
	public function check_select_model($order_id)
	{
		$order_id = (int)$order_id;
		
		$where_str = "order_id={$order_id} and status=1";
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
	
	/*
	 * �Ƿ������ģ��
	 */
	public function check_add_model($order_id)
	{
		$order_id = (int)$order_id;
		
		$where_str = "order_id={$order_id}";
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
	
	
	/*
	 * ��ȡ�ʾ�ģ��
	 */
	public function get_question_model_list($order_id,$limit='')
	{
		$order_id = (int)$order_id;
		$where_str = "order_id={$order_id}";
		$order_by = "add_time desc";
		$ret = $this->findAll ( $where_str, $limit, $order_by, '*' );
		return $ret;
	}
	

	

	

}

?>