<?php
/*
 * 搜索标签类
 */

class pai_search_tag_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_search_tag_config_tbl' );
	}
	
	/*
	 * 获取标签
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
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