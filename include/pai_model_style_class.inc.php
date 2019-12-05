<?php
/*
 * 模特拍摄风格操作类
 */

class pai_model_style_class extends POCO_TDG
{
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_model_style_tbl' );
	}
	
	/*
	 * 添加模特拍摄风格
	 * 
	 * @param int    $user_id 用户ID
	 * @param array  $type_arr 拍摄风格
	 * @param array  $price_arr 拍摄价格
	 * 
	 * return bool 
	 */
	public function add_model_style($user_id, $type_arr, $price_arr)
	{
		$user_id = ( int ) $user_id;
		
		if (empty ( $user_id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		if (empty ( $type_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':拍摄风格数组不能为空' );
		}
		
		if (empty ( $price_arr ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':拍摄价格数组不能为空' );
		}
		
		//把原有的删除再重新添加新的风格和价格
		$this->del_model_style ( $user_id );
		
		foreach ( $type_arr as $k => $style )
		{
			$insert_data ['user_id'] = $user_id;
			$insert_data ['style'] = $style;
			$insert_data ['price'] = $price_arr [$k];
			$this->insert ( $insert_data );
		}
	}
	
	/*
	 * 获取模特拍摄风格
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_model_style_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,15', $fields = '*')
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
	 * 删除模特拍摄风格
	 * 
	 * @param int $user_id
	 */
	public function del_model_style($user_id)
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
	 * 根据用户ID获取模特拍摄风格
	 * 
	 * @param int $user_id
	 * 
	 * return array
	 */
	public function get_model_style_by_user_id($user_id, $fields = '*')
	{
		$user_id = ( int ) $user_id;
		$ret = $this->get_model_style_list ( false, "user_id={$user_id}", 'id DESC', '0,20', $fields );
		return $ret;
	}
}

?>