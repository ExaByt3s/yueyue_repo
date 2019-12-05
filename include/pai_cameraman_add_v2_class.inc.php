<?php


/**
 * 摄影师库类版本2
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015年4月24日
 * @version 2
 */
class pai_cameraman_add_v2_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'cameraman_info_tbl' );
	}

	/*
	 * 添加摄影师基本信息
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
		if($this->get_info($user_id))
		{
			$where_str = "user_id = {$user_id}";
			return $this->update($data, $where_str);
		}
		else 
		{
			$data['user_id'] = $user_id;
			return $this->add_info($data);
		}
		
	}
	
	
	/**
	 * 获摄影师基本数据
	 * $user_id [int] 用户ID
	 * return array $data
	 * */
	public function get_info($user_id)
	{
		$user_id = intval($user_id);
		if($user_id <1) return false;
		$where_str = "user_id = {$user_id}";
		return $this->find($where_str);
	}
	
	
	/*
     * 摄影师列表
     * @param $b_select_count [boolean] true|false 查询个数或者列表
     * @param $where_str      [string]             查询条件
     * @param $order_by       [string]             排序
     * @param $limit          [string]             循环条数
     * @param $fields         [string]             字段名
     * return array $ret
	*/
	public function get_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		if ($b_select_count == true)
		{
			$sql_str = "SELECT COUNT(DISTINCT(C.user_id)) AS ct FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_db.pai_user_tbl P ON P.user_id=C.user_id";
			if(strlen($where_str)>0) $sql_str .= ' WHERE ';
			$sql_str .= "{$where_str}";
			$ret = db_simple_getdata($sql_str,true,101);
			return $ret['ct'];
			//return $this->findCount ( $where_str );
		}
		$sql_str = "SELECT C.*,P.nickname,P.cellphone,P.location_id FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_db.pai_user_tbl P ON P.user_id=C.user_id";
		if(strlen($where_str)>0) $sql_str .= ' WHERE ';
		$sql_str .= "{$where_str} GROUP BY C.user_id ORDER BY {$order_by} limit {$limit}";
		$ret = db_simple_getdata($sql_str,false,101);
		return $ret;
	}
	
	/*
	 * 摄影师搜索列表
	 * @param $b_select_count [boolean] true|false 查询个数或者列表
	 * @param $label_id       [string]             标签
	 * @param $f_start_time   [string]             跟进开始时间
	 * @param $f_end_time     [string]             跟进结束时间
	 * @param $where_str      [string]             查询条件
	 * @param $order_by       [string]             排序
	 * @param $limit          [string]             循环条数
	 * @param $fields         [string]             字段名
	 * return array $ret
	 */
	public function get_search_list($b_select_count = false,$label_id='',$f_start_time='',$f_end_time='', $where_str = '', $order_by = 'C.user_id DESC', $limit = '0,10', $fields = '*')
	{
		$label_id = trim($label_id);
		if(strlen($label_id) >0)
		{
			if(strlen($where_str)>0) $where_str .= ' AND ';
			$where_str .= "L.label_id IN (".mysql_escape_string($label_id).")";
		}
		if(strlen($f_start_time)>0)
		{
			if(strlen($where_str)>0) $where_str .=' AND ';
			$where_str .= "F.follow_time >= '{$f_start_time}'";
		}
		if (strlen($f_end_time)>0)
		{
			if(strlen($where_str)>0) $where_str .= ' AND ';
			$where_str .= "F.follow_time <= '{$f_end_time}'";
		}
		
		//echo $sql_str;
		if ($b_select_count == true)
		{
			$sql_str = "SELECT COUNT(DISTINCT(C.user_id)) AS ct FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_user_library_db.cameraman_label_tbl L ON C.user_id=L.user_id LEFT JOIN pai_user_library_db.cameraman_follow_infornation_tbl F ON F.user_id= C.user_id,pai_db.pai_user_tbl P WHERE P.user_id=C.user_id";
			if(strlen($where_str) >0) $sql_str .= ' AND ';
			$sql_str .= "{$where_str}";
			$ret = db_simple_getdata($sql_str,true,101);
			return $ret['ct'];
		}
		$sql_str = "SELECT C.*,P.location_id,P.nickname,P.cellphone FROM pai_user_library_db.cameraman_info_tbl C LEFT JOIN pai_user_library_db.cameraman_label_tbl L ON C.user_id=L.user_id LEFT JOIN pai_user_library_db.cameraman_follow_infornation_tbl F ON F.user_id= C.user_id,pai_db.pai_user_tbl P WHERE P.user_id=C.user_id";
		if(strlen($where_str) >0) $sql_str .= ' AND ';
	    $sql_str .= "{$where_str} GROUP BY C.user_id ORDER BY {$order_by} limit  {$limit}";
		//echo $sql_str;
	    $ret = db_simple_getdata($sql_str,false,101);
		return $ret;
	}
	

}

?>