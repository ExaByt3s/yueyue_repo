<?php
/**
 * @desc:   专题统计类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/11
 * @Time:   13:46
 * version: 1.0
 */
class pai_topic_report_class extends POCO_TDG
{
    /**
     *  构造函数
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_v2_db' );
    }

    /**
     *  设置主统计表
     */
    private function set_main_topic_tbl()
    {
        $this->setTableName( 'yueyue_main_topic_tbl' );
    }

    /**
     *设置详情表
     */
    private function set_topic_info_tbl()
    {
        $this->setTableName( 'yueyue_topic_info_tbl' );
    }

    /**
     * @param bool $b_select_count
     * @param $topic_id
     * @param $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_main_topic_list($b_select_count=false,$topic_id,$where_str,$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_main_topic_tbl();
        $topic_id = (int)$topic_id;
        if($topic_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "topic_id={$topic_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    // +----------------------------------------------------------------------------------------------------------------
    // |这里是对次专题内容的处理
    // |获取条数和订单等数据
    // +----------------------------------------------------------------------------------------------------------------

    public function get_order_user_uv_by_topic_id($topic_id,$date_time)
    {
        $topic_id = (int)$topic_id;
        $date_time = trim($date_time);
        if($topic_id <1) return false;
            if(!preg_match("/\d\d\d\d-\d\d-\d\d/", $date_time) && !preg_match("/\d\d\d\d\d\d\d\d/", $date_time)) return false;
        $date_time = date('Y-m-d',strtotime($date_time));
        $where_str = '';
        if($topic_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "topic_id={$topic_id}";
        }
        if(strlen($date_time)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "date_time=:x_date_time";
            sqlSetParam($where_str,'x_date_time',$date_time);
        }
        $this->set_topic_info_tbl();
        $ret = $this->find($where_str,null,"COUNT(DISTINCT(buyer_user_id)) AS buyer_user_UV");
        if(!is_array($ret)) $ret = array();
        return (int)$ret['buyer_user_UV'];
    }

    public function get_topic_info_list($b_select_count=false,$topic_id,$date_time,$where_str,$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_topic_info_tbl();
        $topic_id = (int)$topic_id;
        $date_time = trim($date_time);
        if($topic_id >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "topic_id ={$topic_id}";
        }
        if(preg_match("/\d\d\d\d-\d\d-\d\d/", $date_time) || preg_match("/\d\d\d\d\d\d\d\d/", $date_time))
        {
            $date_time = date('Y-m-d',strtotime($date_time));
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "date_time =:x_date_time";
            sqlSetParam($where_str,'x_date_time',$date_time);
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
}