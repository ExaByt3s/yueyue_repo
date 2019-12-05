<?php

/**
 * 摄影师标签分类管理器
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月29日
 * @version 1
 */
class pai_cameraman_add_label_cat_class extends POCO_TDG
{

	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_cat_label_tbl' );
	}
	
	
	/*
	 * 添加基本信息
	 *
	 * @param data   数据
	 *
	 * return id
	 */
	public function add_info($data)
	{
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':数据不能为空');
		}
		return $this->insert($data,"IGNORE");
	}
	
	/*
	 * 更新数据
	 *
	 * $data [array]     需要更新的数据
	 * $user_id  [int]       用户ID
	 * return [boolean]  true|false
	 * **/
	public function update_info($data,$user_id)
	{
		$user_id = intval($user_id);
		if($user_id < 1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':ID不能为空');
		}
		if(!is_array($data) || empty($data))
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':数据不能为空');
		}
		$where_str = "user_id = {$user_id}";
		return $this->update($data, $where_str);
	
	}
	
	
	/*
	 * 删除数据
	 * $cat_id
	 * 
	 * **/
	public function del_info($cat_id)
	{
		$cat_id = intval($cat_id);
		if($cat_id <1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':分类ID不能为空');
		}
		$where_str = "cat_id = {$cat_id}";
		return $this->delete($where_str);
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
	public function get_list($b_select_count = false, $where_str = '', $order_by = 'cat_id DESC', $limit = '0,20', $fields = '*')
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
	
	public function get_info($cat_id)
	{
		$cat_id = intval($cat_id);
		if($cat_id <1)
		{
			return 0;
		}
		$where_str = "cat_id = {$cat_id}";
		return  $this->find($where_str);
	}
	
	
 
}
 