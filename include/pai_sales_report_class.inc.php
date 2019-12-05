<?php
/*
 * 销售概括操作类
 * xiao xiao
 */

class pai_sales_report_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 22 );
		$this->setDBName ( 'yueyue_stat_db' );
		$this->setTableName ( 'yueyue_sales_report_tbl_201502' );
	}
	/**
	 * 获取表名
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
		$tablename = 'yueyue_sales_report_tbl_'.$month;
		$res = $this->query("SHOW TABLES FROM `yueyue_stat_db` LIKE '{$tablename}'");
		if (empty($res) || !is_array($res)) 
		{
			return false;
		}
		return $tablename;
	}

	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_sale_report_report_list($tablename , $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,20', $fields = '*')
	{
		if (empty($tablename)) 
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：表选择出错' );
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