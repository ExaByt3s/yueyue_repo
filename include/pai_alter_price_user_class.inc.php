<?php
/*
 * 改价用户操作类
 */

class pai_alter_price_user_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_alter_price_user_tbl' );
	}
	
	/*
	 * 添加用户
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_user($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data,'IGNORE' );
	
	}
	
	
	/**
	 * 更新用户
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_user($data, $id)
	{
		if (empty ( $data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		$id = ( int ) $id;
		if (empty ( $id ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
		}
		
		$where_str = "id = {$id}";
		return $this->update ( $data, $where_str );
	}
	
	/*
	 * 删除记录
	 */
    public function del_user_by_user_id($alter_topic_id,$user_id)
    {
        $alter_topic_id = (int)$alter_topic_id;
 		$user_id = (int)$user_id;
 		
		if (empty($user_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':用户ID不能为空');
		}
		
    	if (empty($alter_topic_id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':专题ID不能为空');
		}
		
		$where_str = "user_id = {$user_id} and alter_topic_id={$alter_topic_id}";
		return $this->delete($where_str);
        
    }
    
	
	/*
	 * 获取用户列表
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_user_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,100000', $fields = '*')
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
	 * 批量更新用户标签
	 */
	public function update_user_topic_tag($topic_id,$tag='')
	{
		$topic_id = (int)$topic_id;
		$where_str = "alter_topic_id = {$topic_id}";
		
		$data['tag'] = $tag;
		return $this->update ( $data, $where_str );
	}
	
	

}

?>