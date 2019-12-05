<?php
/**
 * @desc:   约约管理员角色类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   9:38
 * version: 1.0
 */
class pai_admin_role_index_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_user_library_db' );
    }

    /**
     * 设置角色表
     */
    private function  set_admin_role_tbl()
    {
        $this->setTableName( 'pai_admin_role_tbl' );
    }

    /**
     * 设置角色关联管理员表
     */
    private  function set_admin_ref_role_tbl()
    {
        $this->setTableName( 'pai_admin_index_ref_role_tbl' );
    }
    /**
     * 获取角色列表
     * @param bool   $b_select_count 是否查询条数(true表示查询条数,false 查询数组)
     * @param string $where_str  条件
     * @param string $order_by  排序
     * @param string $limit   循环条数
     * @param string $fields  查询字段
     * @return array|int
     */
    public function get_admin_role_index_list($b_select_count = false,$where_str = '', $order_by = 'sort DESC,role_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_role_tbl();
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
        $this->set_admin_role_tbl();
        return $this->insert ( $insert_data );
    }

    /**
     * 添加角色
     * @param string $role_name  角色名称 例如:'超级管理员'
     * @param int $sort      排序 例如:100
     * @param array $op_arr    权限数组 例如:array(1,2,3);
     * @return int           返回值
     * @throws App_Exception
     */
    public function add_info_role($role_name,$sort=100,$op_arr)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_name = trim($role_name);
        $sort = intval($sort);
        $data['role_name'] = $role_name;
        $data['sort'] = $sort;
        $retID = $this->add_info($data);
        $retID = intval($retID);
        if($retID >0)
        {
            $admin_op_obj->add_role_op($retID,$op_arr);
        }
        return intval($retID);
    }

    /**
     * 添加多条用户角色数据进角色关联用户表
     * @param int $user_id 用户ID,示例:100000
     * @param int $role_id  角色一维数组ID，示例: array(1,2,3);
     * @return bool 返回值
     */
    private function add_admin_ref_role_info($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id <1 || $role_id <1) return false;
        $this->set_admin_ref_role_tbl();
        $data['user_id'] = $user_id;
        $data['role_id'] = $role_id;
        $this->insert($data,'REPLACE');
        return true;
    }

    /**
     * 通过用户ID删除用户与角色的关联数据
     * @param int $user_id  用户ID,示例:100000
     * @return bool
     * @throws App_Exception
     */
    public function del_admin_ref_role_info($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_role_tbl();
        return $this->delete("user_id={$user_id}");
    }

    /**
     * 添加多条 用户角色数据接收器
     * @param int $user_id    用户ID,示例:100000
     * @param int $role_id 角色一维数组ID，示例: array(1,2,3);
     * @return bool  返回值
     */
    public function add_admin_ref_role($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id <1 || $role_id <1) return false;
        $this->add_admin_ref_role_info($user_id,$role_id);
    }

    /**
     * 修改用户角色数据接收器
     * @param int $user_id  用户ID,示例:100000
     * @param int $role_id  角色一维数组ID，示例:1
     * @return bool
     */
    public function update_admin_ref_role($user_id,$role_id)
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id >0)
        {
            $this->del_admin_ref_role_info($user_id);
        }
        if($role_id >0)
        {
            $this->add_admin_ref_role_info($user_id,$role_id);
        }
       return true;
    }


    /**
     * 根据角色ID获取单条数据
     * @param int $role_id
     * @return array|int 返回值
     */
    public function get_info_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id <1)
        {
           return 0;
        }
        $this->set_admin_role_tbl();
        return $this->find("role_id={$role_id}");
    }

    /**
     * 根据角色ID 更新数据
     * @param int $role_id  角色ID 例如:1
     * @param array $update_data   更新数据的数组
     * @return mixed
     * @throws App_Exception
     */
    public function update_info($role_id,$update_data)
    {
        $role_id = intval($role_id);
        if($role_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':角色ID不能为空' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_admin_role_tbl();
        return $this->update($update_data, "role_id={$role_id}");
    }

    /**
     * 更新角色数据
     * @param int $role_id       角色ID,示例:$role_id=1
     * @param string $role_name  角色名,示例:$role_name ='超级管理员'
     * @param int $sort          排序,示例:$sort=10
     * @param array $op_arr      操作ID一维数组,示例:$op_arr = array(1,2,3);
     * @return int
     * @throws App_Exception
     */
    public function update_info_role($role_id,$role_name,$sort,$op_arr)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_id = intval($role_id);
        $role_name = trim($role_name);
        $sort = intval($sort);
        if($role_id < 1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':角色ID不能为空' );
        }
        $data['role_name'] = $role_name;
        $data['sort'] = $sort;
        $retID = $this->update_info($role_id,$data);
        if($retID >=0)
        {
            $admin_op_obj->update_role_op($role_id,$op_arr);
        }
        return true;
    }

    /**
     * 通过角色ID删除单条数据
     * @param int $role_id  角色ID,示例:1
     * @return int
     * @throws App_Exception
     */
    public function del_info_by_role_id($role_id)
    {
        $admin_op_obj = POCO::singleton( 'pai_admin_op_class' );
        $role_id = intval($role_id);
        if($role_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':角色ID不能为空' );
        }
        $this->set_admin_role_tbl();
        $retID = $this->delete("role_id={$role_id}");
        if($retID >0)
        {
            //删除角色关联的op操作
            $admin_op_obj->del_op_id_by_role_id($role_id);
        }
        return intval($retID);
    }

    /**
     * 获取管理员关联角色列表
     * @param bool $b_select_count  $b_select_count 是否查询条数(true表示查询条数,false 查询数组)
     * @param int $user_id  用户ID,示例：100000
     * @param int $role_id  角色ID,示例:1
     * @param string $where_str 查询条件,示例:$where_str ="user_id=100000";
     * @param string $order_by  排序,示例 user_id DESC,role_id DESC
     * @param string $limit     循环条数,示例:0,100
     * @param string $fields    查询字段,示例:* 获取 user_id,role_id
     * @return array|int
     */
    public function get_admin_ref_role_list($b_select_count = false,$user_id,$role_id,$where_str = '', $order_by = 'user_id DESC,role_id DESC', $limit = '0,20', $fields = '*')
    {
        $user_id = intval($user_id);
        $role_id = intval($role_id);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($role_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "role_id = {$role_id}";
        }
        $this->set_admin_ref_role_tbl();
        if($b_select_count == true)
        {
            return $this->findCount ( $where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;

    }

    /**
     * 通过user_id获取单条角色
     * @param int $user_id 用户ID
     * @return int|bool
     */
    public function get_admin_role_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_role_tbl();
        $row = $this->find("user_id={$user_id}",null,"role_id");
        return intval($row['role_id']);
    }

    /**
     * 获取管理员列表
     * @param int $user_id 用户ID
     * @return array|int
     */
    public function get_role_sort_by_user_id($user_id =0)
    {
        $user_id = intval($user_id);
        $role_ret = $this->get_admin_role_index_list(false,'','sort DESC,role_id DESC','0,99999999','*'); //获取角色数据
        if(!is_array($role_ret)) $role_ret = array();
        if($user_id <1) return $role_ret;
        $role_id = $this->get_admin_role_by_user_id($user_id);//被选择的角色
        if($role_id <1) return $role_ret;
        //被管理员选择的角色
        foreach($role_ret as &$v)
        {
            if($v['role_id']== $role_id) {
                $v['selected'] = true;
            }

        }
        return $role_ret;
    }

}