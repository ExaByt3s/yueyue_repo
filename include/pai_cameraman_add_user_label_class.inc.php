<?php

/**
 * 给摄影师打标签类
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月30日
 * @version 1
 */
class pai_cameraman_add_user_label_class extends POCO_TDG
{

	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_label_tbl' );
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
	 * 删除数据
	 * $id
	 * 
	 * **/
	public function del_info($user_id)
	{
		$user_id = intval($user_id);
		if($user_id <1)
		{
			throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':UID不能为空');
		}
		$where_str = "user_id = {$user_id}";
		return $this->delete($where_str);
	}
	
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param int $user_id
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit
	 * @param string $fields 查询字段
	 *
	 * return array
	 */
	public function get_list($b_select_count = false,$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,20', $fields = '*')
	{
		$user_id = intval($user_id);
		if($user_id >0)
		{
			if(strlen($where_str)>0) $where_str .= ' AND ';
			$where_str .= "user_id = {$user_id}";
		}
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
	 * 获取用户的标签
	 * @param int $user_id
	 * 
	 * return string $label
	 * 
	 * **/
	public function get_info_by_user_id($user_id)
	{
		//标签列表类
		$cameraman_add_label = POCO::singleton('pai_cameraman_add_label_class');
		
		$user_id = intval($user_id);
		if($user_id <1) return false;
		$label_ret = $this->get_list(false,$user_id,'','id ASC','0,10');
		if(!is_array($label_ret)) $label_ret = array();
		$label_tmp_str = '';
		foreach ($label_ret as $key=>$label_val)
		{
			if($key !=0) $label_tmp_str .= ',';
			$label_tmp_str .= $label_val['label_id'];
		}
		if(strlen($label_tmp_str)>0)
		{
			$sql_label_str = "id IN ({$label_tmp_str})";
			$label_list_ret = $cameraman_add_label->get_list(false,'', $sql_label_str,'id ASC','0,10','id as label_id,label');
			if(is_array($label_list_ret)) $label_ret = combine_arr($label_ret, $label_list_ret, 'label_id');
		}
		$label_name = '';
		foreach ($label_ret as $key=>$label_vo)
		{
			if($key != 0) $label_name .= ',';
			$label_name .= $label_vo['label'];
		}
		return $label_name;
	}
	
	
 
}
 