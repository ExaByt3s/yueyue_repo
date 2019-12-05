<?php
/*
 * 图片审核操作类
 */

class pai_pic_pass_examine_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pic_examine_pass_log_201412' );
	}

	
	/*
	 * 查询数据库中有多少张pic_examine_pass_log%表
	 * 
	 *
	*/
	/*public function get_pic_pass_tab()
	{
		//$tmp_tab = 'pic_examine_pass_log_%';
		$get_sql = "show tables from pai_log_db";
		$res     = $this->findBySql($get_sql);

	}*/
	
	/*
	 * 获取图片
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_pic_pass_examine_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*', $year = '', $month = '')
	{
		if (empty($year)) 
		{
			$year = date('Y', time());
		}
		if (empty($month)) 
		{
			$month = date('m', time());
		}
		$tab = 'pic_examine_pass_log_'.$year.$month;
		//判断表是否存在
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
		//die('skksk');
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