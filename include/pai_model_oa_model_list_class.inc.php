<?php
/*
 * OA模特列表操作类
 */

class pai_model_oa_model_list_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_model_list_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_model($insert_data)
	{
        global $yue_login_id;
		
		if (empty ( $insert_data['order_id'] ))
		{
			trace ( "订单ID不能为空", basename ( __FILE__ ) . " 行:" . __LINE__ . " 方法:" . __METHOD__ );
			return false;
		}
		
		if (empty ( $insert_data['user_id'] ))
		{
			trace ( "用户ID不能为空", basename ( __FILE__ ) . " 行:" . __LINE__ . " 方法:" . __METHOD__ );
			return false;
		}

        $insert_data['recommend_user_id'] = $yue_login_id;
		$insert_data['add_time'] = time();
		
		return $this->insert ( $insert_data );
	
	}
    
  
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_model($data, $id)
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
		return $this->update($data, $where_str);
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
	public function get_model_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_model_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	
	/*
	 * 删除
	 *  
	 * @param int $user_id
	 */
	public function del_model($id)
	{
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':id不能为空' );
		}
		
		$where_str = "id = {$id}";
		return $this->delete ( $where_str );
	}
	
	/*
	 * 是否已添加
	 */
	public function check_repeat($user_id,$order_id)
	{
		$user_id = (int)$user_id;
		$order_id = (int)$order_id;
		
		$where_str = "user_id={$user_id} and order_id={$order_id}";
		$ret = $this->findCount ( $where_str );
		
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/*
	 * 是否已选择合适的模特
	 */
	public function check_select_model($order_id)
	{
		$order_id = (int)$order_id;
		
		$where_str = "order_id={$order_id} and status=1";
		$ret = $this->findCount ( $where_str );
		
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/*
	 * 是否有添加模特
	 */
	public function check_add_model($order_id)
	{
		$order_id = (int)$order_id;
		
		$where_str = "order_id={$order_id}";
		$ret = $this->findCount ( $where_str );
		
		if($ret)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	
	/*
	 * 获取问卷模特
	 */
	public function get_question_model_list($order_id,$limit='')
	{
		$order_id = (int)$order_id;
		$where_str = "order_id={$order_id}";
		$order_by = "add_time desc";
		$ret = $this->findAll ( $where_str, $limit, $order_by, '*' );
		return $ret;
	}
	

	

	

}

?>