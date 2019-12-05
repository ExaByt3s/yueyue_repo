<?php
/**
 * @desc:   约约管理员类V2
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/27
 * @Time:   15:53
 * version: 1.0
 */
class pai_admin_index_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_user_library_db' );
        $this->setTableName ( 'pai_admin_index_tbl' );
    }

    /**
     * 获取管理员列表
     * @param bool $b_select_count
     * @param int $user_id 用户ID 例如:100000
     * @param int $status  用户状态 默认值为全部(-1表示全部，0关闭中，1表示开启)
     * @param string $where_str  查询条件
     * @param string $order_by   排序
     * @param string $limit      循环次数 0,10
     * @param string $fields    字段名，默认*，例如'user_id,status'
     * @return array|int        返回值
     */
    public function get_admin_index_list($b_select_count = false,$user_id,$status =-1,$where_str = '', $order_by = 'add_time DESC,user_id DESC', $limit = '0,20', $fields = '*')
    {
        $user_id = intval($user_id);
        $status = intval($status);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($status >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "status = {$status}";
        }
        if($b_select_count == true)
        {
            return $this->findCount ( $where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }

    /**
     * 添加数据
     * @param array $insert_data 入库数据
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        return $this->insert ($insert_data,'REPLACE');
    }

    /**
     * 通过用户ID 删除数据
     * @param int $user_id
     * @return bool
     * @throws App_Exception
     */
    private function del_info($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
        }
        return $this->delete("user_id={$user_id}");
    }

    /**
     * 通过用户ID删除管理员的一切数据
     * @param int $user_id
     * @return bool
     * @throws App_Exception
     */
    public function del_admin_index_by_user_id($user_id)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
        }
        $retID = $this->del_info($user_id);
        if($retID >0)
        {
            $admin_role_index_obj->del_admin_ref_role_info($user_id);
            $admin_op_obj->del_op_ref_by_user_id($user_id);
        }
        return $retID;
    }

    /**
     * 添加管理员
     * @param int $user_id 用户ID 例如:100000
     * @param int $status  用户状态 默认值为全部(-1表示全部，0关闭中，1表示开启)
     * @param string $real_name 真实姓名
     * @param string $department 所在部门
     * @param array $role_id 角色一维数组
     * @param array $op_arr  操作一维数组
     * @return int
     * @throws App_Exception
     */
    public function  add_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        $status = intval($status);
        $real_name = trim($real_name);
        $department = trim($department);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
        }
        $data['user_id'] = $user_id;
        $data['status'] = $status;
        $data['real_name'] = $real_name;
        $data['department'] = $department;
        $data['add_time'] = time();
        $retID = $this->add_info($data);
        if($retID >=0)
        {
            $admin_role_index_obj->add_admin_ref_role($user_id,$role_id);
            $admin_op_obj->add_op_ref_user($user_id,$op_arr);
        }
        return true;
    }

    /**
     * 根据用户ID获取单条数据
     * @param int $user_id 用户ID 例如:100000
     * @return array|bool
     */
    public function get_info_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        return $this->find("user_id={$user_id}");
    }

    /**
     * 根据用户ID 更新数据
     * @param int $user_id  用户ID 例如:100000
     * @param array $update_data   更新数据的数组
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($user_id,$update_data)
    {
        $user_id = intval($user_id);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        return $this->update($update_data, "user_id={$user_id}");
    }

    /**
     * 更新管理员数据
     * @param int $user_id 用户ID 例如:100000
     * @param int $status  用户状态 默认值为全部(-1表示全部，0关闭中，1表示开启)
     * @param string $real_name 真实姓名
     * @param string $department 所在部门
     * @param int $role_id
     * @param array $op_arr
     * @return int
     * @throws App_Exception
     */
    public function update_info_index($user_id,$status,$real_name,$department,$role_id,$op_arr)
    {
        $admin_role_index_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $user_id = intval($user_id);
        $status = intval($status);
        $real_name = trim($real_name);
        $department = trim($department);
        if($user_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
        }
        $data['status'] = $status;
        $data['real_name'] = $real_name;
        $data['department'] = $department;
        $retID = $this->update_info($user_id,$data);
        if($retID >=0)
        {
            $admin_role_index_obj->update_admin_ref_role($user_id,$role_id);
            $admin_op_obj->update_op_ref_user($user_id,$op_arr);
        }
        return true;
    }
}