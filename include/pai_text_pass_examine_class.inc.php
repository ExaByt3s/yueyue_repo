<?php
/*
 * ��˹������ֲ�����
 */

class pai_text_pass_examine_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'text_examine_pass_log_201412' );
	}
	
	
	/*
	 * ����ͼƬ
	 * @param bool $b_select_count
	 * @param string $where_str ��ѯ����
	 * @param string $order_by ����
	 * @param string $limit 
	 * @param string $fields ��ѯ�ֶ�
	 * 
	 * return array
	 */
	public function get_text_pass_examine_list($b_select_count = false, $where_str = '',$year = '', $month = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
	{
		if (empty($year)) 
		{
			$year = date('Y', time());
		}
		if (empty($month)) 
		{
			$month = date('m', time());
		}
		$tab = 'text_examine_pass_log_'.$year.$month;
		$res = $this->query("SHOW TABLES FROM pai_log_db LIKE '{$tab}'");
		if (empty($res) || !is_array($res)) 
		{
			if ($b_select_count == true) 
			{
				return 0;
			}
			else
			{
				return array();
			}
		  exit;
		}
		$this->setTableName ( $tab );
		$user_id = (int)$user_id;
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
	

}

?>