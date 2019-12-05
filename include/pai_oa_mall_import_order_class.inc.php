<?php
/*
 * OA�����̳Ƕ���������
 */

class pai_oa_mall_import_order_class extends POCO_TDG
{
	
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_import_mall_order_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_order($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		return $this->insert ( $insert_data );
	
	}
    

	
	/**
	 * ����
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_order($data, $id)
	{
		if (empty($data)) 
		{
			return false;
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			return false;
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
	public function get_order_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_order_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

	

}

?>