<?php
/*
 * ��Ӱʦ��չ���������
 * xiao xiao
 */

class pai_cameraman_report_class extends POCO_TDG
{
	
	/**
	 * ���캯��
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_stat_db' );
		$this->setTableName ( 'yueyue_cameraman_userinfo_tbl_201502' );
	}
	
	/**
	 * ��ȡ����
	 * @param int $month
	 */
	public function get_tablename_by_month($month)
	{
		$month = $month;
		if (empty($month)) 
		{
			$month = date('Y-m', time());
		}
		$month = date('Ym', strtotime($month) );
		$tablename = 'yueyue_cameraman_userinfo_tbl_'.$month;
		$res = $this->query("SHOW TABLES FROM `yueyue_stat_db` LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			return false;
		}
		return $tablename;
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
	public function get_cameraman_report_list($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,20', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '����ѡ�����' );
		}
		$this->setTableName($tablename);
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	

}

?>