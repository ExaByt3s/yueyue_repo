<?php
/*
 * ����ID��
 * xiao xiao
 */

class pai_dmid_for_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_dmid_for_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_info($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data );
	
	}
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $app_id
	 * @return bool 
	 */
	public function update_info($data, $id)
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
		
		$where_str = "dmid = {$id}";
		return $this->update($data, $where_str);
	}
	 
	
	/**
	 *  ɾ��
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete_info($id, $b_select_count = false)
	{   
		if ($b_select_count == true) 
		{
			$sql = "DELETE FROM `pai_db`.`pai_rank_event_tbl`";
			return $this->query($sql);
		}
		else
		{
			$id = (int)$id;
			if (empty($id)) 
			{
				throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
			}
			$where_str = "dmid = {$id}";
			return $this->delete($where_str);
		}
		
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
	public function get_dmid_for_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
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
	 * ��ȡ
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_serialize_rank_event_list()
	{
		$total_count  = $this->findCount ( $where_str );
		$where_str = "1";
		$ret = $this->findAll($where_str, "0,{$total_count}", "sort_order DESC,id DESC");
		return serialize($ret);
	}
	
	public function get_dmid_for_info($id)
	{
		$id = ( int ) $id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		$ret = $this->find ( "dmid={$id}" );
		return $ret;
	}

	
}

?>