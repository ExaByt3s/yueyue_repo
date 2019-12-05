<?php
/*
 * ģ���������Ͳ�����
 */

class pai_model_type_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_type_tbl' );
	}
	
	/*
	 * ���ģ����������
	 * 
	 * @param int    $user_id �û�ID
	 * @param array  $type_arr ��������
	 * 
	 * return bool 
	 */
	public function add_model_type($user_id, $type_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		if (empty ( $type_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�����������鲻��Ϊ��' );
		}
		
		//��ԭ�е�ɾ������������µ�����
		$this->del_model_type ( $user_id );
		
		foreach ( $type_arr as $type )
		{
			if (! empty ( $type ))
			{
				$insert_data ['user_id'] = $user_id;
				$insert_data ['type'] = $type;
				$this->insert ( $insert_data );
			}
		}
	}
	
	/*
	 * ��ȡģ����������
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_model_type_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	 * ɾ��ģ����������
	 * 
	 * @param int $user_id
	 * 
	 * return bool
	 */
	public function del_model_type($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * �����û�ID��ȡ��������
	 * 
	 * @param int $user_id
	 * 
	 * return array
	 */
	public function get_model_type_by_user_id($user_id, $fields = '*')
	{
		$user_id = ( int ) $user_id;
		
		$where_str = "user_id={$user_id}";
		$ret = $this->get_model_type_list ( false, $where_str, 'id DESC', '0,10', $fields );
		return $ret;
	}
}

?>