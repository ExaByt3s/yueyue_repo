<?php

/**
 *举报类
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-04 11:09:14
 * @version 1
 */

class pai_log_inform_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_log_db' );
		$this->setTableName ( 'pai_examine_inform_tbl' );
	}

	/**
	 * 获取举报列表
	 * @param  boolean $b_select_count [是否查询个数]
	 * @param  string  $where_str      [条件]
	 * @param  string  $order_by       [排序]
	 * @param  string  $limit          [循环个数]
	 * @param  string  $fields         [查询字段]
	 * @return [array] $ret            [返回值]
	 */
	public function get_inform_list($b_select_count = false, $where_str = '',$order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
		if ($b_select_count == true) 
		{
			$ret = $this->findCount($where_str);
		}
		else
		{
			$ret = $this->findAll($where_str, $limit, $order_by, $fields);
		}
		return $ret;
	}


	/**
	 * 添加数据
	 * @param  [array] $data [需要更新的数据]
	 * @return [boolean]   [返回值true|false]
	 */
	public function add_info($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$ret = $this->insert($data, 'IGNORE');
		return $ret;
	}


	/**
	 * 更新数据
	 * @param  [array] $data [需要更新的数据]
	 * @param  [int] $id   [主键ID]
	 * @return [boolean]   [返回值true|false]
	 */
	public function update_info($data, $id)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		$id = (int)$id;
		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		$where_str = "id = {$id}";
		$ret = $this->update($data, $where_str);
		return $ret;
	}
	/**
	 * 获取举报数据
	 * @param  [int] $id [主机ID]
	 * @return [false|array]    [返回值]
	 */
	public function get_info($id)
	{
		$id = (int)$id;
		if(!$id)
		{
			return false;
		}
		return $this->find("id = {$id}");
	}

	/**
	 * 获取举报人或者举报者次数
	 * @param  [int]    $user_id [用户ID]
	 * @param  [string] $type    [by表示举报者|to表示被举报者]
	 * @return [int]             [返回值]
	 */
	public function get_inform_count($user_id, $type = 'by')
	{
		$user_id = (int)$user_id;
		if(!$user_id){return false;}
		//作为举报者
		if($type == 'by')
		{
			$where_str = "by_informer = {$user_id}";
		}
		//被举报者
		else
		{
			$where_str = "to_informer = {$user_id}";
		}
		return $this->findCount($where_str);
	}
}

?>