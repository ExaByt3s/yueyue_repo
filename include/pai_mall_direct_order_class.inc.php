<?php
/*
 * 直接下单配置操作类
 */

class pai_mall_direct_order_class extends POCO_TDG
{
	
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'mall_db' );
		$this->setTableName ( 'mall_direct_order_config_tbl' );
	}
    
    private function set_mall_goods_tbl()
	{
		$this->setTableName('mall_goods_tbl');
	}
    
    private function set_mall_direct_order_config_tbl()
    {
        $this->setTableName('mall_direct_order_config_tbl');
    }
	
	/*
	 * 添加
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function add_config($insert_data)
	{
		
		if (empty ( $insert_data ))
		{
			return false;
		}
		
		return $this->insert ( $insert_data );
	
	}
    
    
    public function add_by_user_id($user_id,$params)
    {
        $user_id = (int)$user_id;
        if( ! $user_id )
        {
            return false;
        }
        if( empty($params) )
        {
            return false;
        }
        $this->set_mall_goods_tbl();
        $goods_list = $this->findAll("user_id='{$user_id}'");
        if( ! empty($goods_list) )
        {
            foreach($goods_list as $k => $v)
            {
                
                $good_one = array();
                $unit = array();
                $this->set_mall_direct_order_config_tbl();
                $good_one = $this->get_config_info_by_goods_id($v['goods_id']);
                if( ! empty($good_one) )
                {
                    //upate
                    $unit['goods_id'] = $v['goods_id'];
                    $unit['type_id'] = $v['type_id'];
                    $unit['location_id'] = $v['location_id'];
                    $unit['is_auto_accept'] = $params['is_auto_accept'];
                    $unit['is_auto_sign'] = $params['is_auto_sign'];
                    $unit['address'] = get_poco_location_name_by_location_id($v['location_id']);
                    $this->update($unit, "goods_id='{$v['goods_id']}'");
                }else
                {
                    //insert
                    $unit['goods_id'] = $v['goods_id'];
                    $unit['type_id'] = $v['type_id'];
                    $unit['location_id'] = $v['location_id'];
                    $unit['add_time'] = time();
                    $unit['service_time'] = time();
                    $unit['is_auto_accept'] = $params['is_auto_accept'];
                    $unit['is_auto_sign'] = $params['is_auto_sign'];
                    $unit['address'] = get_poco_location_name_by_location_id($v['location_id']);
                    $this->add_config($unit);
                }
                
                
            }
        }
        return true;
        
        
    }
    
	
	/**
	 * 更新
	 *
	 * @param array $data
	 * @param int $id
	 * @return bool
	 */
	public function update_config($data, $id)
	{
		$id = (int)$id;
		if (empty($data) || empty($id)) 
		{
			return false;
		}
		
		$where_str = "id = {$id}";
		return $this->update($data, $where_str);
	}


    public function del_config($id)
    {
        return $this->delete("id={$id}");
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
	public function get_config_list($b_select_count = false, $where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
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
	
	public function get_config_info($id)
	{
		$id = ( int ) $id;
		$ret = $this->find ( "id={$id}" );
		return $ret;
	}
	
	
	public function get_config_info_by_goods_id($goods_id)
	{
		$goods_id = ( int ) $goods_id;
		$ret = $this->find ( "goods_id={$goods_id}" );
		return $ret;
	}
	

	

}

?>