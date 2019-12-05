<?php
/*
 * 改价记录操作类
 */

class pai_alter_price_log_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_alter_price_log_tbl' );
	}
	
	/*
	 * 添加记录
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_log($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data,"IGNORE" );
	
	}
    
	/*
	 * 删除记录
	 */
    public function del_log($id)
    {
        $id = (int)$id;

		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "log_id = {$id}";
		return $this->delete($where_str);
        
    }
    
	/*
	 * 删除记录
	 */
    public function del_log_by_user_id($alter_topic_id,$user_id)
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
    
	
	/**
	 * 更新记录
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_log($data, $id)
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
		
		$where_str = "log_id = {$id}";
		return $this->update($data, $where_str);
	}
	
	public function update_topic_log($data)
	{
		if (empty($data)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':数组不能为空');
		}
		
		
		return $this->insert ( $data,"REPLACE" );
	}
	
	
	/*
	 * 获取记录列表
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_log_list($b_select_count = false, $where_str = '', $order_by = 'log_id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_log_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "log_id={$id}" );
		return $ret;
	}
	
	public function get_log_id_info($alter_topic_id=0,$user_id=0,$style='')
	{
		$ret = $this->find ( "alter_topic_id={$alter_topic_id} and user_id={$user_id} and style='{$style}'" );
		return $ret;
	}
	
	public function change_alter_price($alter_type,$type_value,$price,$hour)
	{
		if($alter_type=='discount_price')
		{
			$alter_price = $price*$type_value*0.1;
		}
		elseif($alter_type=='alter_price')
		{
			$alter_price = $type_value;
			$hour = 2;
		}
		elseif($alter_type=='reduce_price')
		{
			$reduce_p = $price-$type_value;
			$alter_price = $reduce_p;
		}
		
		if($alter_price<1)
		{
			$alter_price=1;
		}
		
		$ret['price'] = sprintf ( "%.2f", $alter_price);
		$ret['hour'] = $hour;
		return $ret;
	}
}

?>