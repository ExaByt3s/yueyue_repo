<?php
/*
 * ���ַ�������
 */

class pai_find_style_class extends POCO_TDG
{
	
	private $cache_key = "YUEYUE_INTERFACE_FIND_STYLE_";
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_find_style_tbl' );
	}
	
	/*
	 * ���
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add($insert_data)
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
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}
	 
	
	/**
	 *  ɾ��
	 *
	 * @param int $id
	 * @return bool
	 */
	public function delete_info($id)
	{
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID����Ϊ��');
		}
		
		$where_str = "id = {$id}";
		return $this->delete($where_str);
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
	public function get_style_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_style_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	public function discover_model_by_style($location_id=0,$type_name=''){
		//$location_id = (int)$location_id;
  
		$where_str .= "1";
		
/*		if($location_id)
		{
			$where_str .= " and location_id like '%{$location_id}%'";
		}*/
		$where_str .= " and style='{$type_name}'";
		$ret = $this->get_style_list(false, $where_str, 'sort DESC,id DESC', $limit = '0,9');
		foreach($ret as $k=>$val)
		{
			$ret [$k] ['user_icon_165'] = get_user_icon($val ['user_id'], 165 );
			$ret [$k] ['user_icon_468'] = get_user_icon($val ['user_id'], 468 );
		}
		return $ret;
	}
	
	
	public function get_find_style_cache_key($user_id)
	{
		return $this->cache_key . $user_id;
	}
}

?>