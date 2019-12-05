<?php
/**
 * @desc:   管理员操作类,其中关联管理员管理操作表，角色关联操作表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/28
 * @Time:   11:17
 * version: 1.0
 */
class pai_admin_op_class extends POCO_TDG
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
     * 设置管理员操作表
     */
    private function set_admin_op_tbl()
    {
        $this->setTableName ( 'pai_admin_op_tbl' );
    }

    /**
     *管理员操作管理表
     */
    private function set_admin_ref_op_tbl()
    {
        $this->setTableName( 'pai_admin_index_ref_op_tbl' );
    }
    /**
     * 设置角色关联操作表
     */
    private function set_role_ref_op_tbl()
    {
        $this->setTableName( 'pai_admin_role_ref_op_tbl' );
    }

    ##########################操作表的方法#################################################
    /**
     * 获取操作列表
     * @param bool $b_select_count 是否查询条数(true表示查询条数,false 查询数组)
     * @param int $op_id        操作ID 1
     * @param int $parent_id    操作父类ID
     * @param string $where_str 条件 $where_str ="user_id={$user_id}";
     * @param string $order_by  排序
     * @param string $limit    循环条数
     * @param string $fields   查询字段
     * @return array|int
     */
    public function get_op_list($b_select_count = false,$op_id,$parent_id,$where_str = '', $order_by = 'sort DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_op_tbl();
        $op_id = intval($op_id);
        $parent_id = intval($parent_id);
        if($op_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($parent_id >=0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "parent_id={$parent_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;

    }

    /**
     * 通过操作ID获取单条数据
     * @param $op_id 操作ID 例如1
     * @return array
     * @throws App_Exception
     */
    public function  get_op_info_by_op_id($op_id)
    {
        $op_id = intval($op_id);
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':操作ID不能为空' );
        }
        $this->set_admin_op_tbl();
        return $this->find("op_id={$op_id}");
    }

    /**
     * 获取操作选择类表
     * @param int $selected
     * @param int $parent_id
     * @param int $n
     * @return string
     */
    public function get_op_sort_option($selected=0,$parent_id=0,$n=-1)
    {
        $options = '';
        static $i = 0;
        if ($i == 0)
        {
            $options .= "<option value='0'>== 顶级操作 ==</option>";
        }
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $key=>$val)
        {
            $i++;
            $options .="<option value='{$val['op_id']}'";
            if ($val['op_id'] == $selected)
            {
                $options .=' selected ';
            }
            $val['parent_id'] != 0 ? $options .=">".str_repeat('&nbsp;&nbsp;&nbsp;',$n).'|-'.$val['op_name']."</option>\n" : $options .=">".str_repeat('&nbsp;&nbsp;&nbsp;',$n).$val['op_name']."</option>\n";
            $options .= self::get_op_sort_option($selected,$val['op_id'],$n);
        }
        return $options;
    }

    /**
     * 获取所有查询的列表数据，以表格的方式展示出来
     * @param int $parent_id 父级ID
     * @param int $n         级别符号
     * @return string
     */
    public function get_op_sort_list($parent_id=0,$n=-1)
    {
        $str = '';
        static $i = 0;
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $key=>$val)
        {
            $i++;
            $is_nav = $val['op_is_nav'] ==1 ? '是':'否';
            $str .="<tr><td align='center'>{$val['op_id']}</td><td>";
            $val['parent_id'] != 0 ? $str .=str_repeat('&nbsp;&nbsp;',$n).'|-'.$val['op_name']."</td>\n" : $str .=str_repeat('&nbsp;&nbsp;',$n).$val['op_name']."</td>\n";
            $str .= "<td align='center'>{$val['op_code']}</td>";
            $str .= "<td align='center'>{$val['op_url']}</td>";
            $str .= "<td align='center'>{$val['op_level']}</td>";
            $str .= "<td align='center'>{$is_nav}</td>";
            $str .= "<td align='center'><input type='text' value='{$val['sort']}' name='op[{$val['op_id']}]' class='input-text' size='4'/></td>";
            $str .= "<td align='center'><a href='admin_op_edit.php?op_id=".$val['op_id']."&act=edit'>编辑</a>&nbsp;|&nbsp;<a href='admin_op_edit.php?op_id=".$val['op_id']."&act=del'>删除</a></td></tr>";
            $str .= self::get_op_sort_list($val['op_id'],$n);
        }
        return $str;

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
        $this->set_admin_op_tbl();
        return $this->insert ( $insert_data );
    }

    /**
     * 添加操作
     * @param int $op_type_id 操作类型
     * @param string $op_name   操作名
     * @param string $op_code   操作代码块
     * @param string $op_url    操作链接
     * @param int $parent_id    父类ID 没有父类的情况下为0
     * @param int $sort         排序
     * @param int $op_is_nav    是否显示到菜单
     * @param array $option      拓展部分
     * @return int              返回值
     * @throws App_Exception
     */
    public function add_info_op($op_type_id,$op_name,$op_code,$op_url,$parent_id = 0,$sort=100,$op_is_nav,$option = array())
    {
        $op_type_id = intval($op_type_id);
        $op_name = trim($op_name);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $parent_id = intval($parent_id);
        $option = (array)$option;
        $op_level = 1;
        if($parent_id > 0) {//获取级别
            $row = $this->get_op_info_by_op_id($parent_id);
            if(!is_array($row)) $row = array();
            $op_level = intval($row['op_level'])+1;
        }
        $sort = intval($sort);
        $op_is_nav = intval($op_is_nav);
        $data['op_type_id'] = $op_type_id;
        $data['op_name'] = $op_name;
        $data['op_code'] = $op_code;
        $data['op_url'] = $op_url;
        $data['parent_id'] = $parent_id;
        $data['op_level'] = $op_level;
        $data['sort'] = $sort;
        $data['op_is_nav'] = $op_is_nav;
        $data['op_location'] = trim($option['op_location']);
        $retID = $this->add_info($data);
        return intval($retID);
    }


    /**
     * 通过操作父类ID获取单条数据
     * @param $parent_id 操作ID 例如1
     * @return array
     * @throws App_Exception
     */
    public function  get_op_info_by_parent_id($parent_id)
    {
        $parent_id = intval($parent_id);
        if($parent_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':操作ID不能为空' );
        }
        $this->set_admin_op_tbl();
        return $this->find("parent_id={$parent_id}");
    }

    /**
     * 通过操作ID删除单条数据
     * @param $op_id 操作ID 例如1
     * @return bool
     */
    public function del_op_info_by_op_id($op_id)
    {
        $op_id = intval($op_id);
        if($op_id <1) return false;
        $this->set_admin_op_tbl();
        $retID = $this->delete("op_id={$op_id}");
        return intval($retID);
    }

    /**
     * 根据操作ID 更新数据
     * @param int $op_id  操作ID 例如:1
     * @param array $update_data   更新数据的数组
     * @return mixed
     * @throws App_Exception
     */
    private function update_info($op_id,$update_data)
    {
        $op_id = intval($op_id);
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':操作ID不能为空' );
        }
        if (empty ( $update_data ) || !is_array($update_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_admin_op_tbl();
        return $this->update($update_data, "op_id={$op_id}");
    }


    /**
     * 程序排序
     * @param array $op 操作ID和排序先关联的一维数组,示例array(1=>3,2=>4,3=>8),1为op_id,3为sort
     * @return bool
     * @throws App_Exception
     */
    public function op_id_sort_again($op)
    {
       if(!is_array($op) || empty($op)) return false;
        $data = array();
        foreach($op as $op_id=>$sort)
        {
            $op_id = intval($op_id);
            $data['sort'] = intval($sort);
            $this->update_info($op_id,$data);
        }
        return true;
    }


    /**
     * 更新 操作
     * @param int $op_id   操作ID,示例1
     * @param int $op_type_id  类型,示例1
     * @param string $op_name
     * @param string $op_code
     * @param string $op_url
     * @param int $parent_id
     * @param  int $sort
     * @param  int $op_is_nav
     * @param  array $option
     * @return int
     * @throws App_Exception
     */
    public function update_info_op($op_id,$op_type_id,$op_name,$op_code,$op_url,$parent_id,$sort,$op_is_nav,$option)
    {
        $op_id = intval($op_id);
        $op_type_id = intval($op_type_id);
        $op_name = trim($op_name);
        $parent_id = trim($parent_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $sort = intval($sort);
        $op_is_nav = intval($op_is_nav);
        $option = (array)$option;
        if($op_id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':操作ID不能为空' );
        }
        $op_level = 1;
        if($parent_id > 0) {//获取级别
            $row = $this->get_op_info_by_op_id($parent_id);
            if(!is_array($row)) $row = array();
            $op_level = intval($row['op_level'])+1;
        }
        $data['op_type_id'] = $op_type_id;
        $data['op_name'] = $op_name;
        $data['op_code'] = $op_code;
        $data['op_url'] = $op_url;
        $data['parent_id'] = $parent_id;
        $data['op_level'] = $op_level;
        $data['sort'] = $sort;
        $data['op_is_nav'] = $op_is_nav;
        $data['op_location'] = $option['op_location'];
        $retID = $this->update_info($op_id,$data);
        return intval($retID);
    }


    /**
     * 判断父类下面是否存在 一个$op_id,存在无法选择归属到子孙下
     * @param int $parent_id 父类ID
     * @return array $arr = array(1,3,3);
     */
    public function is_check_parent_id($parent_id)
    {
        static $arr = array();
        $parent_id = intval($parent_id);
        if($parent_id <1) return array();
        $this->set_admin_op_tbl();
        $list = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','op_id');
        if(!is_array($list)) $list = array();
        foreach($list as $v)
        {
            $arr[] = $v['op_id'];
            self::is_check_parent_id($v['op_id']);
        }
       return $arr;
    }

    ############################操作表的方法#########################################################################

    /**
     * 获取角色关联的操作数据   是否查询条数(true表示查询条数,false 查询数组)
     * @param bool $b_select_count
     * @param int $role_id 角色ID,示例:2
     * @param int $op_id   操作ID,示例:3
     * @param string $where_str 查询条件,示例:$where_str ="role_id=1";
     * @param string $order_by  排序,示例:$order_by="role_id DESC";
     * @param string $limit     循环条数,示例:0,10
     * @param string $fields    查询字段，示例:'op';
     * @return array|int
     */
    public function get_op_ref_role($b_select_count = false,$role_id,$op_id,$where_str = '', $order_by = 'role_id DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_role_ref_op_tbl();
        $role_id = intval($role_id);
        $op_id = intval($op_id);
        if($role_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "role_id = {$role_id}";
        }
        if($op_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }

    /**
     * 获取操作选择类表
     * @param array $selected
     * @param int $parent_id
     * @param int $n
     * @return string
     */
    public function get_op_option_list($selected = array(),$parent_id=0,$n=-1)
    {
        if(!is_array($selected)) $selected = array();
        $options = '';
        static $i = 0;
        $res = $this->get_op_list(false,0,$parent_id,'','sort DESC,op_id DESC','0,99999999','*');
        if(!is_array($res)) $res = array();
        $n++;
        foreach($res as $val)
        {
            $i++;
            $options .= "<p>";
            $val['parent_id'] != 0 ? $options .= str_repeat('&nbsp;&nbsp;&nbsp;',$n)."|-\n" : $options .=str_repeat('&nbsp;&nbsp;&nbsp;',$n)."\n";
            $options .="<input type='checkbox' popid='{$val['parent_id']}' name='op_id[]' value='{$val['op_id']}'";
            foreach($selected as $key=>$v)
            {
                if ($val['op_id'] == $v['op_id'])
                {
                    $options .=' checked=true ';
                    unset($selected[$key]);
                }
            }
            $options .="/>".$val['op_name']."</p>\n";
            $options .= self::get_op_option_list($selected,$val['op_id'],$n);
        }
        return $options;
    }


    /**
     * 添加角色操作权限
     * @param int $role_id 角色ID，示例:1
     * @param array $op_arr 角色操作一维数组，示例: $op_arr = array(1,2,3);
     *  @return bool
     */
    public function add_role_op($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->add_role_op_info($role_id,$op_arr);
    }

    /**
     * 更新角色操作权限
     * @param int $role_id 角色ID，示例:1
     * @param array $op_arr 角色操作一维数组，示例: $op_arr = array(1,2,3);
     * @return bool
     */
    public function update_role_op($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        $this->del_op_id_by_role_id($role_id);
        if(is_array($op_arr) && !empty($op_arr)) $this->add_role_op_info($role_id,$op_arr);
    }

    /**
     * 添加角色权限如角色操作关联表中
     * @param int $role_id 角色ID，示例:1
     * @param array $op_arr 角色操作一维数组，示例: $op_arr = array(1,2,3);
     * @return bool
     */
    private function add_role_op_info($role_id,$op_arr)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;

        $this->set_role_ref_op_tbl();//指定表
        $data['role_id'] = $role_id;
        foreach($op_arr as $op_id)
        {
            $data['op_id'] = intval($op_id);
            $this->insert($data,'REPLACE');
        }
    }

    /**
     * 通过角色ID删除角色操作表中的数据
     * @param int $role_id 角色ID，示例:1
     * @return bool
     */
    public function del_op_id_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id <1) return false;

        $this->set_role_ref_op_tbl();
        return $this->delete("role_id={$role_id}");
    }

    /**
     * 添加多条 用户和操作数据接收器
     * @param int $user_id  用户ID,示例:100000
     * @param array $op_arr  操作一维数组ID，示例: array(1,2,3);
     * @return bool
     */
    public function add_op_ref_user($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->add_op_ref_user_info($user_id,$op_arr);
    }

    /**
     * 修改用户和操作数据接收器
     * @param int $user_id 用户ID,示例:100000
     * @param array $op_arr 操作一维数组ID，示例: array(1,2,3);
     * @return bool
     */
    public function update_op_ref_user($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->del_op_ref_by_user_id($user_id);
        if(is_array($op_arr) && !empty($op_arr)) $this->add_op_ref_user_info($user_id,$op_arr);
    }

    /**
     * 添加多条用户和操作数据接收器通过用户ID
     * @param int $user_id 用户ID,示例:100000
     * @param array $op_arr 操作一维数组ID，示例: array(1,2,3);
     * @return bool
     */
    private function add_op_ref_user_info($user_id,$op_arr)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $this->set_admin_ref_op_tbl();
        $data['user_id'] = $user_id;
        foreach($op_arr as $op_id)
        {
            $data['op_id'] = intval($op_id);
            $this->insert($data,'REPLACE');
        }
        return true;
    }

    /**
     * 通过用户ID 删除用户关联的操作
     * @param int $user_id 用户ID,示例:100000
     * @return bool
     * @throws App_Exception
     */
    public function del_op_ref_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id <1) return false;
        $this->set_admin_ref_op_tbl();
        return $this->delete("user_id={$user_id}");
    }

    /**
     * 获取关联员关联的操作数据
     * @param bool $b_select_count   是否查询条数(true表示查询条数,false 查询数组)
     * @param int $user_id 用户ID,示例:100000
     * @param int $op_id   操作ID,示例:3
     * @param string $where_str 查询条件,示例:$where_str ="role_id=1";
     * @param string $order_by  排序,示例:$order_by="role_id DESC";
     * @param string $limit     循环条数,示例:0,10
     * @param string $fields    查询字段，示例:'op';
     * @return array|int
     */
    public function get_op_ref_admin_list($b_select_count = false,$user_id,$op_id,$where_str = '', $order_by = 'user_id DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $this->set_admin_ref_op_tbl();
        $user_id = intval($user_id);
        $op_id = intval($op_id);
        if($user_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($op_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "op_id = {$op_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        return $ret;
    }
    /**
     * 通过角色ID获取权限列表，以string 返回的
     * @param int $role_id 角色ID
     * @return string
     */
    public function get_op_option_by_role_id($role_id)
    {
        $role_id = intval($role_id);
        if($role_id < 1) return $this->get_op_option_list();
        $selected = $this->get_op_ref_role(false,$role_id,0,'','role_id DESC,op_id DESC','0,99999999','*');
        //print_r($selected);
        return $this->get_op_option_list($selected);
    }

    /**
     * 通过用户ID获取权限列表，以string 返回的
     * @param int $user_id 用户ID,示例:1000000
     * @return string
     */
    public function get_admin_op_by_user_id($user_id)
    {
        $user_id = intval($user_id);
        if($user_id < 1) return $this->get_op_option_list();
        $selected = $this->get_op_ref_admin_list($b_select_count = false,$user_id,0,'','user_id DESC,op_id DESC','0,99999999','op_id');
        return $this->get_op_option_list($selected);
    }

    /**
     * 获取数据是否有权限
     * @param bool $b_select_count
     * @param int $user_id
     * @param string $op_code
     * @param string $op_url
     * @param int $op_level
     * @param int $op_is_nav
     * @param int $parent_id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function  get_op_full_list($b_select_count = false,$user_id,$op_code,$op_url,$op_level,$op_is_nav,$parent_id =0,$where_str = '', $order_by = 'sort DESC,op_id DESC', $limit = '0,20', $fields = '*')
    {
        $admin_index_obj = POCO::singleton( 'pai_admin_index_class' );
        $admin_role_obj = POCO::singleton( 'pai_admin_role_index_class' );
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $op_level = intval($op_level);
        $op_is_nav = intval($op_is_nav);

        $admin_ret = $admin_index_obj->get_info_by_user_id($user_id);//获取管理员数据
        $status = intval($admin_ret['status']);
        if($status <1) return false;//关闭状态就退出
        $role_id = $admin_role_obj->get_admin_role_by_user_id($user_id);//获取角色ID
        $op_arr = array(); //操作数据
        if($role_id >0)//执行role_ref_op 去拿数据
        {
            $op_arr = $this->get_op_ref_role(false,$role_id,0,'','role_id DESC,op_id DESC','0,99999999','op_id');
        }
        else
        {
            $op_arr = $this->get_op_ref_admin_list(false,$user_id,0,'','user_id DESC,op_id DESC','0,99999999','op_id');
        }
        if(!is_array($op_arr) || empty($op_arr)) return false;
        $sql_tmp_str = '';
        foreach($op_arr as $key=>$val)
        {
           if($key !=0) $sql_tmp_str .= ',';
            $sql_tmp_str .= $val['op_id'];
        }
        if(strlen($sql_tmp_str) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_id IN ({$sql_tmp_str})";
        }
        if(strlen($op_code) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_code=:x_op_code";
            sqlSetParam($where_str,'x_op_code',$op_code);
        }
        if(strlen($op_url)>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_url=:x_op_url";
            sqlSetParam($where_str,'x_op_url',$op_url);
        }
        if($op_level>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_level={$op_level}";
        }
        if($op_is_nav>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "op_is_nav={$op_is_nav}";
        }
        if($parent_id>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "parent_id={$parent_id}";
        }
        $ret = $this->get_op_list($b_select_count,0,-1,$where_str, $order_by, $limit, $fields);
        return $ret;
    }

    /**
     * 页面调用权限
     * @param int $user_id    用户ID
     * @param string $op_code 代码块
     * @param string $op_url         操作链接
     * @param int $parent_id  父级ID防止代码块或者操作链接与其他模块重复
     *  @return bool|array
     */
    public function check_op($user_id,$op_code,$op_url = '',$parent_id =0)
    {
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $op_url = trim($op_url);
        $parent_id = intval($parent_id);
        if($user_id <1) return false;
        if(strlen($op_code)<1 && strlen($op_url) <1)return false;
        $ret = $this->get_op_full_list(false,$user_id,$op_code,$op_url,0,0,$parent_id,'','sort DESC,op_id DESC','0,1','*');
        return $ret;
    }

    //生成头部获取left
    public function create_nav_list($user_id,$op_code ='',$param='',$op_is_nav = 1)
    {
        $str = '';
        $user_id = intval($user_id);
        $op_code = trim($op_code);
        $param = trim($param);
        $op_is_nav = intval($op_is_nav);
        if($user_id <1 || strlen($op_code) <1)return $str;
        $top_op = $this->get_op_full_list(false,$user_id,$op_code,'',0,$op_is_nav);
        if(!is_array($top_op)) $top_op = array();
        if(strlen($param) <1)//获取头部
        {
            foreach($top_op as $key=>$val)
            {
                $str .= "<li><span id=\"top_menu_{$val['op_id']}\" role-data=\"{$val['op_id']}\"><a href=\"#this\">{$val['op_name']}</a></span></li>";
            }
        }
        elseif($param == 'left')
        {
            foreach($top_op as $key=>$val)
            {
                $str .="<dl id=\"nav_{$val['op_id']}\" style=\"display: none;\" class=\"nav_info\">";
                $str .=" <dt>{$val['op_name']}</dt>";
                $left_op = $this->get_op_full_list(false,$user_id,'','',4,$op_is_nav,$val['op_id']);
                if(!is_array($left_op)) $left_op = array();
                foreach($left_op as $v)
                {
                    $str .="<dd class=\"off\">";
                    $str .="<span><a href=\"{$v['op_url']}\" target=\"main\">{$v['op_name']}</a></span>";
                    $str .="</dd>";
                }
                $str .="</dl>";
            }
        }
        elseif($param == 'left_v2') //有二级的
        {
            foreach($top_op as $key=>$val)
            {
                $str .="<dl id=\"nav_{$val['op_id']}\" style=\"display: none;\" class=\"nav_info\">";
                $str .=" <dt>{$val['op_name']}</dt>";
                $left_op = $this->get_op_full_list(false,$user_id,'','',4,$op_is_nav,$val['op_id']);
                if(!is_array($left_op)) $left_op = array();
                $str .="<dd class=\"off\">";
                foreach($left_op as $v)
                {
                    $str .="<span><a href=\"{$v['op_url']}\" target=\"main\"><strong>{$v['op_name']}</strong></a></span>";
                    $left_v2_op = $this->get_op_full_list(false,$user_id,'','',5,$op_is_nav,$v['op_id']);
                    if(!is_array($left_v2_op)) $left_v2_op = array();
                    foreach($left_v2_op as $vo)
                    {
                        $str .= "<div><a href=\"{$vo['op_url']}\" target=\"main\">{$vo['op_name']}</a></div>";
                    }
                }
                $str .="</dd>";
                $str .="</dl>";
            }

        }
        return $str;

    }


    /**
     * 友好提示信息
     * @param string $msg 信息
     * @param bool $b_reload
     * @param null $url
     * @param bool $parent 是否更新服务
     *
     */
    public static function pop_msg($msg,$b_reload = false,$url=NULL,$parent = true)
    {
        echo "<script language='javascript'>alert('{$msg}');";
        if($url && $parent) echo "parent.location.href = '{$url}';";
        if($url && !$parent) echo "location.href = '{$url}';";
        if($b_reload) echo "history.back();";
        echo "</script>";
        exit;
    }


}