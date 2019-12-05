<?php
/**
 * @desc:   商家消息回复数
 *@User:    xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/18
 * @Time:   16:05
 * version: 1.0
 */

class pai_sendserver_seller_reply_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_sendserver_for_seller_db' );
    }

    /**
     * 设置数据库
     * @param string $date
     * @return bool
     * @throws App_Exception
     */
    private function set_for_seller_tbl($date)
    {
        if(strlen($date) <1 ) $date = date('Ym',time()-3600*24);
        $date = date('Ym', strtotime($date));
        $table_name = 'sendserver_for_seller_reply_log_'.$date;
        $res = $this->query("SHOW TABLES FROM {$this->_db_name} LIKE '{$table_name}'");
        if (empty($res) || !is_array($res))
        {
            return false;
        }
        $this->setTableName($table_name);
        return true;
    }

    /**
     *设置七天回复表
     */
    private function set_seven_days_tbl()
    {
        $this->setTableName( 'sendserver_for_seller_reply_seven_days_log' );
    }

    /**
     * 获取数据列表
     * @param bool $b_select_count
     * @param string $date string类型的时间格式的，示例：'2015-08-07'
     * @param int $type_id string  商家认证分类
     * @param string $where_str  条件
     * @param string $group_by   以group by 方式存在
     * @param string $order_by   排序
     * @param string $limit   循环
     * @param string $fields  字段
     * @return array|bool|int
     */
    public function get_info_list($b_select_count = false,$date,$type_id,$where_str = '',$group_by='',$order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $group_by = trim($group_by);
        $type_id = intval($type_id);
        $retID = $this->set_for_seller_tbl($date);
        $retID = intval($retID);
        if($retID <1) return false;//没有能够选择库退出
         if($type_id >0)
         {
             if(strlen($where_str) >0) $where_str .= ' AND ';
             $where_str .= "FIND_IN_SET({$type_id},type_id)";
         }
        if(strlen($group_by) >0)
        {
            $where_str .= ' ';
            $where_str .= $group_by;
        }
        if ($b_select_count == true) {
            $ret = $this->findCount ( $where_str,$fields );
        }
        else {
            $ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
        }
        return $ret;
    }

    /**
     * 通过商家ID查询回复率
     * @param int $seller_id 商家ID
     * @param int $interval_time 回复间隔时间，秒，示例:5分钟 5*60
     * @param int $type_id  分类
     * @param int $type 类型，0表示查询全部，1表示只查询商家用户发送的回复率，2只查询系统发送的回复率
     * @param string $where_str 条件
     * @return string
     */
    public function get_seven_reply_by_seller_id($seller_id,$interval_time = 600,$type_id = 0,$type=1,$where_str ='')
    {
        $seller_id = intval($seller_id);
        $interval_time = intval($interval_time);
        $type_id = intval($type_id);
        $type = intval($type);
        if($seller_id <1) return false;
        if($interval_time <0) return false;
        if($seller_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "receive_id={$seller_id}";
        }
        if($type >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            if($type == 1) $where_str .= "sender_id >= 100000"; //单纯查用户发送的
            else $where_str .= "sender_id <100000";//查系统发送的
        }
        /*if($interval_time >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "interval_time>={$interval_time} AND interval_time>0";
        }*/
        if($type_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "FIND_IN_SET({$type_id},type_id)";
        }
        $this->set_seven_days_tbl();//设定表
        $sql_str = "SELECT (SUM(CASE WHEN interval_time>0&&interval_time<={$interval_time} THEN 1 ELSE 0 END)/SUM(1)) AS scale FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where_str}";
        $ret = db_simple_getdata($sql_str,TRUE,22);
        return sprintf('%.2f',$ret['scale']);
    }

}