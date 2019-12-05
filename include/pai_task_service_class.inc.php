<?php
/*
 * 行业服务操作类
 */

class pai_task_service_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_task_db' );
		
	}
	
	public function set_db_utf8()
	{
		$this->query("SET sql_mode='',character_set_client=binary,character_set_connection=utf8,character_set_results=utf8");
	}	
	
	private function set_task_service_tbl() {
		$this->setTableName ( 'task_service_tbl' );
	}
	
	private function set_task_service_faq_tbl() {
		$this->setTableName ( 'task_service_faq_tbl' );
	}
	

	/*
	 * 获取行业资料
	 * @param int $service_id
	 * 
	 * return array
	 */
	public function get_service_info($service_id)
	{
		$this->set_task_service_tbl();
		
		$service_id = ( int ) $service_id;
		
		$where_str = "service_id={$service_id}";
		$ret = $this->find ( $where_str);
		return $ret;
	}
	
	/*
	 * 获取行业列表
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_service_list($b_select_count = false, $where_str = '', $order_by = 'service_id ASC', $limit = '0,100', $fields = '*')
	{
		$this->set_task_service_tbl();
		
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
	 * 获取商家FAQ
	 * @param int $service_id
	 * 
	 * return array
	 */
	public function get_service_faq_list($service_id)
	{
		$this->set_task_service_faq_tbl();
		
		$service_id = ( int ) $service_id;
		
		$where_str = "service_id={$service_id}";
		$ret = $this->findAll ( $where_str, '0,1000', 'faq_id asc', '*');
		return $ret;
	}

}

?>