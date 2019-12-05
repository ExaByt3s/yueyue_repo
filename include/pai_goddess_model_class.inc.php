<?php
/*
 * ԼŮ�������
 */

class pai_goddess_model_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_goddess_model_tbl' );
	}
	
	/*
	 * ���ģ��
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_model($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
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
	
	
	public function get_model_info($id)
	{
		$id = ( int ) $id;

		$row = $this->find ( "id={$id}" );
		return $row;
	}
	
	public function check_user_exist($type,$user_id)
	{
		$user_id = ( int ) $user_id;
		$row = $this->find ( "user_id={$user_id} and type='{$type}'" );
		if($row)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $user_id
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
	 * ɾ��ģ��
	 * 
	 * @param int $user_id
	 */
	public function del_model($id)
	{
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':user_id����Ϊ��' );
		}
		
		$where_str = "id = {$id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * ��ȡ����ģ��
	 * 
	 * @param string $limit 
	 */
	public function get_hot_model($b_select_count = false, $location_id,$type, $limit = '0,6')
	{
		$where_str = "1";
		if ($location_id)
		{
			$where_str .= " AND location_id LIKE '{$location_id}%'";
		}
		
		if ($type)
		{
			$where_str .= " AND type='{$type}'";
		}
		
		$ret = $this->get_model_list ( $b_select_count, $where_str, 'sort desc,user_id desc', $limit );
		foreach ( $ret as $k => $val )
		{
			$ret [$k] ['nickname'] = get_user_nickname_by_user_id ($val ['user_id'] );
			$ret [$k] ['user_icon'] = get_user_icon ($val ['user_id'], 165 );
		}
		return $ret;
	}
}

?>