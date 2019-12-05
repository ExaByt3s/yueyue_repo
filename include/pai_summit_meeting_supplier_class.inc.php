<?php

/**
 * 峰会报名类
 * @authors 星星
 * @date    2015-03-10 16:06:04
 * 
 */

class pai_summit_meeting_supplier_class extends POCO_TDG
{
    
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_db' );
        $this->setTableName ( 'pai_summit_meeting_supplier_tbl' );
        

    }
    
    /*
     * 插入一条报名记录
     * 
     * 
     * return bool 
     */
    public function add_summit_meeting_supplier($insert_data)
    {
        if (empty ( $insert_data ))
        {
            return 0;
        }
        
        $insert_data ['phone'] = ( int ) $insert_data ['phone'];
        
        if (empty ( $insert_data ['phone'] ))
        {
            return 0;
        }
        
        if(empty($insert_data['add_time']))
        {
            $insert_data['add_time'] = time();
        }
        return $this->insert ( $insert_data, "IGNORE" );
    }
    
    
    
    
    /*
     * 更新报名表的信息
     * 
     * @param array $update_data
     * @param int   $_id  
     * 
     */
    public function update_summit_meeting_supplier($update_data, $id)
    {
        $id = ( int ) $id;
        
        if (empty ( $update_data ))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        if (empty ( $id ))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }

        
        if ($update_data)
        {
            $where_str = "id = {$id}";
            $ret = $this->update ( $update_data, $where_str );
        }
        

        return $ret;
    }
    

    /*
     * 获取单条信息
     * @param int $id
     * return array
     */
    
    public function get_summit_meeting_supplier_info($id)
    {
        $id = ( int ) $id;
        $ret = $this->find ( "id={$id}" );
        return $ret;
    }
    
    
    
    
    
    
    /*
     * 获取模特卡数据
     * @param bool $b_select_count
     * @param string $where_str 查询条件
     * @param string $order_by 排序
     * @param string $limit 
     * @param string $fields 查询字段
     * 
     * return array
     */
    public function get_summit_meeting_supplier_list($b_select_count = false, $where_str = '', $order_by = 'add_time DESC', $limit = '0,10', $fields = '*')
    {
        if ($b_select_count == true)
        {
            $ret = $this->findCount ( $where_str );
        }
        else
        {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        }
        return $ret;
    }
    
    
   
    /*
     * 获取某个手机号的报名信息
     * @param int $user_id
     * @param string $order_by
     * @param $limit 0,10
     * return array
     */
    public function get_summit_meeting_supplier_list_by_phone($phone, $b_select_count = false, $order_by = 'add_time DESC', $limit  = "0,10")
    {
        $user_id = (int)$user_id;
        $where_str = "phone = {$phone}";
        if(empty($limit))
        {
            $limit = "0,1000";
        }
        $ret = $this->get_summit_meeting_supplier_list($b_select_count,$where_str,$order_by,$limit);
        return $ret;
        
    }
    
    
    
     /*
     * 获取通过数组条件某个用户的报名信息
     * @param array $where_array
     * @param string $order_by
     * @param $limit 0,10
     * return array
     */
    public function get_summit_meeting_supplier_list_by_array($where_array, $b_select_count = false, $order_by = 'add_time DESC', $limit  = "0,10")
    {
        $where_str = "";
        $where_array['id'] = (int)$where_array['id'];
        $where_array['phone'] = (int)$where_array['phone'];
        
        if(!empty($where_array['id']) || $where_array['id']>0)
        {
            $where_str .= " id = ".$where_array['id']; 
        }
        if(!empty($where_array['phone']) || $where_array['phone']!="")
        {
            $where_str .= " phone = ".$where_array['phone']; 
        }
        
        
        if(empty($limit))
        {
            $limit = "0,1000";
        }
        $ret = $this->get_summit_meeting_supplier_list($b_select_count,$where_str,$order_by,$limit);
        return $ret;
        
    }
    
    
    
    /*
     * 删除某个信息
     * @param string $where_str
     * return array
     */
    public function delete_summit_meeting_supplier($where_str)
    {
        if (empty($where_str))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':条件不能为空' );
        }
        $ret = $this->delete($where_str);
        return $ret;
    }
    
    
    
     /*
     * 删除某个信息
     * @param string $where_str
     * return array
     */
    public function delete_summit_meeting_supplier_by_id($id)
    {
        $id = (int)$id;
        if (empty($id))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':id不能为空' );
        }
        $where_str = "id = {$id}";
        $ret = $this->delete($where_str);
        return $ret;
    }
}
?>