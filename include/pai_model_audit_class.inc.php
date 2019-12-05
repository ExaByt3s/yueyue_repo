<?php
/*
 * ģ����˲�����
 */

class pai_model_audit_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_audit_tbl' );
	}
	
	
	/*
	 * ��ȡģ��
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

	/*public function get_model_list_by_sql($b_select_count = false, $where_str = '', $order_by = 'aut.user_id DESC', $limit = '0,10')
	{
		$where_str = $where_str != '' ? "WHERE {$where_str}" : '';
		if ($b_select_count == true) 
		{
			$sql = "SELECT count(aut.user_id) as c FROM `pai_db`.`pai_model_audit_tbl` aut,`pai_db`.`pai_user_tbl` u {$where_str}";
			$result = $this->query($sql);
			$ret = $result[0]['c'];
		}
		else
		{
			$sql = "SELECT aut.*,u.location_id FROM `pai_db`.`pai_model_audit_tbl` aut,`pai_db`.`pai_user_tbl` u {$where_str} order by {$order_by} limit {$limit}";
			$ret = $this->findBySql($sql);
		}
		//var_dump($ret);
		return $ret;
	}*/
	
	public function get_model_list_by_sql($b_select_count = false, $where_str = '', $order_by = 'aut.user_id DESC', $limit = '0,10')
	{
		$where_str = $where_str != '' ? "WHERE {$where_str}" : '';
		if ($b_select_count == true) 
		{
			//$sql = "SELECT count(aut.user_id) as c FROM `pai_db`.`pai_model_audit_tbl` AS aut,`pai_db`.`pai_user_tbl` AS u left join (SELECT u.user_id FROM `pai_user_library_db`.`model_relation_org_tbl`) AS o ON {$where_str}";
			$sql = "SELECT count(aut.user_id) as c FROM `pai_db`.`pai_model_audit_tbl` aut,`pai_db`.`pai_user_tbl` u {$where_str} (SELECT user_id FROM `pai_user_library_db`.`model_relation_org_tbl` GROUP BY user_id) ";
			$result = $this->query($sql);
			$ret = $result[0]['c'];

		}
		else
		{
			$sql = "SELECT aut.*,u.location_id FROM `pai_db`.`pai_model_audit_tbl` aut,`pai_db`.`pai_user_tbl` u {$where_str} (SELECT user_id FROM `pai_user_library_db`.`model_relation_org_tbl` GROUP BY user_id) order by {$order_by} limit {$limit}";
			$ret = $this->findBySql($sql);
		}
		//var_dump($sql);
		return $ret;
	}
	
	public function get_model_info($user_id)
	{
		$user_id = ( int ) $user_id;

		$row = $this->find ( "user_id={$user_id}" );
		return $row;
	}
	
	
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $user_id
	 * @return bool 
	 */
	public function update_model($data, $user_id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':���鲻��Ϊ��');
		}
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER ID����Ϊ��');
		}
		
		global $yue_login_id;
		
		$login_id = (int)$yue_login_id;
		$status = (int)$data['is_approval'];
		$add_time= time();	
		
		
		$where_str = "user_id = {$user_id}";
		$ret = $this->update($data, $where_str);
		
		if($ret)
		{
			$fulltext_obj = POCO::singleton ( 'pai_fulltext_class' );
			$fulltext_obj->add_fulltext_act ( $user_id );
			
			$sql = "insert into pai_log_db.model_audit_log_tbl set login_id={$login_id},status={$status},user_id={$user_id},add_time={$add_time}";
			db_simple_getdata($sql,false,101);
		}
		
		return $ret;
	}


	/*
	* ��ȡid
	*@param int $user_id
	*@param int $status
	*return boolean true|false
	*/
	public function get_id_by_user_id_status($user_id, $status)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			return false;
			//throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':USER ID����Ϊ��');
		}
		$status = (int)$status;
		$where_str = "user_id = {$user_id} AND is_approval = {$status}";
		$user_id = $this->find($where_str, 'user_id');
		if ($user_id) 
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	/*
     *��ȡ״̬
     *
	*/
	public function get_status_by_user_id($user_id)
	{
		$user_id = (int)$user_id;
		if (empty($user_id)) 
		{
			return false;
		}
		$where_str = "user_id = {$user_id}";
		$ret = $this->find($where_str, 'is_approval');
		return $ret['is_approval'];
	}

	/*
	* ��ȡid
	*@param string $where_str
	*return boolean true|false
	*/
	public function get_id_by_user_id_where_str($where_str)
	{
		
		if (empty($where_str)) 
		{
			return false;
			//throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':��������Ϊ��');
		}
		$user_id = $this->find($where_str, 'user_id');
		if ($user_id) 
		{
			return true;
		}
		else
		{
			return false;
		}

	}
	
	/*
	 * ģ���Ƿ������
	 */
	public function check_model_is_approval($user_id)
	{
		$user_id = (int)$user_id;
		$row = $this->find ( "user_id={$user_id}" );
		if($row['is_approval']==1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * ��Ӱʦ�Ƿ�����ģ������ʹ���Ż�ȯ
	 */
	public function check_model_is_approval_for_coupon($user_id)
	{
		$user_id = (int)$user_id;
		$row = $this->find ( "user_id={$user_id}" );
		if($row['is_approval']==1 || $row['is_approval']==3)
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