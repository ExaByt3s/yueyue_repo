<?php
/**
 * @desc:   版本开通类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/8
 * @Time:   14:16
 * version: 1.0
 */

class pai_version_open_class extends POCO_TDG
{

    /**
     *   构造函数
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_admin_db' );
        $this->setTableName ( 'pai_open_version_tbl' );
    }

    /**
     * 添加版本
     * @param int $version_num   版本号 列如:2.0.1
     * @return blea|int          返回值
     * //@throws App_Exception 报错
     */
    public function add_info($version_num = 0)
    {
        global $yue_login_id;
        $version_num = trim($version_num);
        if(strlen($version_num) <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':版本号不能为空' );
        }
        $data['version_num'] = $version_num;
        $data['user_id']     = $yue_login_id;
        $data['add_time']    = date('Y-m-d H:i:s',time());
        return $this->insert($data, 'IGNORE');
    }


    /**
     * 更新数据
     * @param int     $id
     * @param string   $version_num    //版本号 列如:2.0.1
     * @return mixed  返回更新值
     * @throws App_Exception
     */
    public function update_info($id,$version_num)
    {
        $id = intval($id);
        if($id <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        if(strlen($version_num) <1)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':版本号不能为空' );
        }
        $where_str = "id= {$id}";
        $data['version_num']  = $version_num;
        return $this->update($data,$where_str);
    }

    /**
     * 获取一条数据
     * @param  int $id    版本ID
     * @return array|bool 返回值值
     */
    public function get_info($id)
    {
        $id = intval($id);
        if($id <0) return false;
        return $this->find("id={$id}");
    }

    /**
     * 通过版本号 获取版本ID
     *
     * @param string $version_num  //版本号
     * @return int|boo
     */
    public function get_version_id_by_version_num($version_num ='')
    {
        $version_num = trim($version_num);
        if(strlen($version_num) <1) return false;
        $where_str = "version_num=:x_version_num";
        sqlSetParam($where_str,'x_version_num',$version_num);
        $ret = $this->find($where_str,'','id');
        return intval($ret['id']);
    }


    /**
     * 获取版本数据列表
     * @param bool $b_select_count    $b_select_count = true //表示查询数据条数
     * @param string $start_time      $start_time 添加开始数据，可以为空 格式 '2015-01-31'
     * @param string $end_time        $start_time 添加结束数据，可以为空 格式 '2015-01-31'
     * @param int    $user_id         $user_id    用户ID 可以为0
     * @param string $where_str       $where_str 查询条件
     * @param string $order_by        排序
     * @param string $limit           循环条数
     * @param string $fields          查询字段
     * @return array|int              返回值
     */
    public function get_list($b_select_count = false, $start_time = '',$end_time = '',$user_id = 0,$where_str = '', $order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $start_time = trim($start_time);
        $end_time   = trim($end_time);
        $user_id    = intval($user_id);
        $where_str  = trim($where_str);
        if(strlen($start_time) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m-%d') >= :x_start_time";
            sqlSetParam($where_str,'x_start_time',$start_time);
        }
        if(strlen($end_time) >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "FROM_UNIXTIME(UNIX_TIMESTAMP(add_time),'%Y-%m-%d') <= :x_end_time";
            sqlSetParam($where_str,'x_end_time',$end_time);
        }
        if($user_id >0)
        {
            $where_str .= "user_id = {$user_id}";
        }
        if ($b_select_count == true)
        {
            return $this->findCount ( $where_str );
        }
        return $this->findAll ( $where_str, $limit, $order_by, $fields );
    }

    /**
     * 删除版本
     * @param  int  $id //版本ID
     * @return bool|int     返回值
     * @throws App_Exception //报错提示
     */
    public function del_info($id)
    {
        $id = intval($id);
        if($id <0)
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':ID不能为空' );
        }
        return $this->delete("id={$id}");
    }
}