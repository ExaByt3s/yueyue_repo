<?php
/*
 * Ȩ��log������
 * xiao xiao
 * 2015-1-15 
 */

class pai_authority_log_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'authority_log_tbl' );
	}
	
	public function get_authority_list_log($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str);
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	/*
	 * ����log
	 * @param array $data
	 * return role_name
	 */
	public function insert_authority_log($data)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���ݲ���Ϊ��' );
		}
		$ret = $this->insert($data);
		return $ret;
	}
	
	
}

?>