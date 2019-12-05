<?php
/*
 * 模特拍摄类型操作类
 */

class pai_model_type_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_type_tbl' );
	}
	
	/*
	 * 添加模特拍摄类型
	 * 
	 * @param int    $user_id 用户ID
	 * @param array  $type_arr 拍摄类型
	 * 
	 * return bool 
	 */
	public function add_model_type($user_id, $type_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		if (empty ( $type_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':拍摄类型数组不能为空' );
		}
		
		//把原有的删除再重新添加新的类型
		$this->del_model_type ( $user_id );
		
		foreach ( $type_arr as $type )
		{
			if (! empty ( $type ))
			{
				$insert_data ['user_id'] = $user_id;
				$insert_data ['type'] = $type;
				$this->insert ( $insert_data );
			}
		}
	}
	
	/*
	 * 获取模特拍摄类型
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_model_type_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	/*
	 * 删除模特拍摄类型
	 * 
	 * @param int $user_id
	 * 
	 * return bool
	 */
	public function del_model_type($user_id)
	{
		$user_id = ( int ) $user_id;
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		$where_str = "user_id = {$user_id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * 根据用户ID获取拍摄类型
	 * 
	 * @param int $user_id
	 * 
	 * return array
	 */
	public function get_model_type_by_user_id($user_id, $fields = '*')
	{
		$user_id = ( int ) $user_id;
		
		$where_str = "user_id={$user_id}";
		$ret = $this->get_model_type_list ( false, $where_str, 'id DESC', '0,10', $fields );
		return $ret;
	}
}

?>