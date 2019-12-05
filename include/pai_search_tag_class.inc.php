<?php
/*
 * ������ǩ��
 */

class pai_search_tag_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_search_tag_config_tbl' );
	}
	
	/*
	 * ��ȡ��ǩ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_tag_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function add_tag($tag, $sort = 0)
	{
		$insert_data ['tag'] = $tag;
		$insert_data ['sort'] = ( int ) $sort;
		$insert_data ['add_time'] = date ( 'Y-m-d H:i:s' );
		$this->insert ( $insert_data );
		return true;
	}
	
	public function del_tag($id)
	{
		$where_str = "id = {$id}";
		return $this->delete ( $where_str );
	}

}

?>