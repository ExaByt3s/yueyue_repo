<?php
/*
 * ���֤��˲�����
 */

class pai_id_audit_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_id_audit_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_audit($insert_data)
	{
		$user_id = $insert_data ['user_id'];
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':�û�����Ϊ��' );
		}
		
		$audit_info = $this->get_audit_info ( $user_id );
		
		//�����ͨ�����Բ��ϸ���ͼƬ
		if ($audit_info ['status'] == 2 || $audit_info ['status'] == 0)
		{
			$insert_data ['status'] = 0;
			$insert_data ['add_time'] = time ();
			$this->insert ( $insert_data ,'REPLACE');;
			return db_simple_get_affected_rows();
		}
		elseif ($audit_info ['status'] == 1)
		{
		//���ͨ����������
			return 1;
		}
		else
		{
			$insert_data ['add_time'] = time ();
			return $this->insert ( $insert_data ,'IGNORE');
		}
	}
	
	public function del_audit($user_id)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':USER ID����Ϊ��' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	
	}
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_audit($data, $user_id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':USER ID����Ϊ��' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->update ( $data, $where_str );
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
	public function get_audit_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		}
		else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_audit_info($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}
	
	public function check_user_is_upload($user_id)
	{
		$user_id = ( int ) $user_id;
		$ret = $this->get_audit_list ( false, 'user_id=' . $user_id );
		if ($ret)
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