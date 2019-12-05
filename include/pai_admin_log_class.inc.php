<?php
/**
 * @desc:   管理员操作记录类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/30
 * @Time:   17:05
 * version: 1.0
 */

class pai_admin_log_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_log_db' );
        $this->setTableName ( 'pai_admin_log_tbl' );
    }

    /**
     * 获取log列表
     * @param bool $b_select_count  是否查询条数(true表示查询条数,false 查询数组)
     * @param string $module    模块名
     * @param $action           操作,示例:insert(表示插入),update(更新),del(表示删除)
     * @param $operate_id       操作者ID,示例:100000
     * @param string $where_str 查询条件,示例:$where_str ="add_time=1438249981";
     * @param string $order_by  排序,示例:'add_time DESC,id DESC'
     * @param string $limit     查询条数,示例:'0,10'（表查询10条）
     * @param string $fields    查询字段,示例:'module,action,add_time'
     * @return array|int       返回值
     */
    public function get_admin_log_list($b_select_count = false,$module,$action,$operate_id,$where_str = '', $order_by = 'add_time DESC,id DESC', $limit = '0,20', $fields = '*')
    {
        $module = trim($module);
        $action = trim($action);
        $operate_id = intval($operate_id);
        if(strlen($module)>0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "module=:x_module";
            sqlSetParam($where_str,"x_module",$module);
        }
        if(strlen($action) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "action=:x_action";
            sqlSetParam($where_str,'x_action',$action);
        }
        if($operate_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "operate_id={$operate_id}";
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
     * @param array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_info($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        return $this->insert ( $insert_data );
    }

    /**
     * 添加log数据
     * @param string $module 模块名称
     * @param string $action 操作
     * @return bool
     * @throws App_Exception
     */
    public function add_admin_log($module,$action)
    {
        global $yue_login_id;
        global $_INPUT;
        $action_arr = array('insert','update','del');
        if(!in_array($action,$action_arr)) return false; //限制添加的信息

        $operate_id = intval($yue_login_id);
        if($operate_id <1) return false;
        $module = trim($module);
        $action = trim($action);
        $data['module'] = $module;
        $data['action'] = $action;
        $data['log'] = serialize($_INPUT);
        $data['add_time'] = time();
        $data['operate_id'] = $operate_id;
        $this->add_info($data);
        return true;
    }

}