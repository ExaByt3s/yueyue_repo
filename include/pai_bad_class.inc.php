<?php
/*
 * ������������
 */

class pai_bad_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_search_bad_list_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_bad($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
		}
		
		return $this->insert ( $insert_data,"IGNORE" );
	
	}
    
    public function del_bad($user_id)
    {
        $user_id = (int)$user_id;

		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':������ID����Ϊ��');
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete($where_str);
        
    }
    
	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_bad($data, $id)
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
	public function get_bad_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_bad_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	

}

?>

