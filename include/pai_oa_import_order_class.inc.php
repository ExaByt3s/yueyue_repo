<?php
/*
 * OA导入订单操作类
 */

class pai_oa_import_order_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_user_library_db' );
		$this->setTableName ( 'model_oa_import_order_tbl' );
	}
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_order($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    public function del_order($id)
    {
        $id = (int)$id;

		if (empty($id)) 
		{
			throw new App_Exception(__CLASS__.'::'.__FUNCTION__.':ID不能为空');
		}
		
		$where_str = "id = {$id}";
		return $this->delete($where_str);
        
    }
    
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_order($data, $id)
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
	public function get_order_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_order_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}

    public function set_oa_last_visit($user_id, $visit_time, $location_id)
    {
        $user_id    = (int)$user_id;
        $visit_time = (int)$visit_time;

        if($user_id && $visit_time)
        {
            $sql_str = "REPLACE INTO pai_log_db.oa_last_visit_tbl(user_id, last_time, location_id) VALUES ($user_id, $visit_time, $location_id)";
            db_simple_getdata($sql_str, TRUE, 101);
            return TRUE;
        }
        return FALSE;
    }

    public function get_oa_last_visit($user_id, $location_id)
    {
        $user_id = (int)$user_id;
        $location_id = (int)$location_id?(int)$location_id:101029001;
        $visit_time = 0;
        if($user_id)
        {
            $sql_str = "SELECT last_time FROM pai_log_db.oa_last_visit_tbl
                        WHERE user_id = $user_id AND location_id = $location_id";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if($result['last_time'])
            {
                $visit_time = $result['last_time'];
            }
        }

        return $visit_time;
    }
	

}

?>