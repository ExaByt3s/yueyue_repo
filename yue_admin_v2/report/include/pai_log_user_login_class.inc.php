<?php
/**
 * @desc:   用户登录log报表
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/28
 * @Time:   9:29
 * version: 1.0
 */
class pai_log_user_login_class extends POCO_TDG
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
     * 设置商家登录表
     */
    private function set_log_seller_login_tbl()
    {
        $this->setTableName ( 'yueyue_seller_login_tbl' );
    }

    /**
     *设置消费者登录表
     */
    private function set_log_buyer_login_tbl()
    {
        $this->setTableName ( 'yueyue_buyer_login_tbl' );
    }
    /**
     *设置商家数量表
     */
    private function set_yueyue_seller_num_tbl()
    {
        $this->setTableName( 'yueyue_seller_num_tbl' );
    }
    /**
     * 获取报表登录log
     * @param bool $b_select_count
     * @param int $type_id
     * @param string $where_str
     * @param string $group_by
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_log_seller_login_list($b_select_count=false,$type_id,$where_str,$group_by='',$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_log_seller_login_tbl();
        $type_id = intval($type_id);
        if($type_id >=0)
        {
            if(strlen($where_str)>1) $where_str .= ' AND ';
            $where_str .= "type_id={$type_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($group_by)>0)
        {
            if(strlen($where_str)>0)
            {
                $where_str .= ' ';
            }else{
                $where_str .= " 1 ";
            }
            $where_str .="{$group_by}";
        }
        return $this->findAll($where_str,$limit,$order_by,$fields);
    }

    public function get_seller_num_by_type_id($type_id,$add_time,$where_str ='')
    {
        $this->set_yueyue_seller_num_tbl();
        $type_id = (int)$type_id;
        $add_time = trim($add_time);
        if($type_id <0 || strlen($add_time) <1) return 0;
        if($type_id >=0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "type_id={$type_id}";
        }
        if(strlen($add_time)>0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "add_time=:x_ad_time";
            sqlSetParam($where_str,"x_ad_time",$add_time);
        }
        $ret = $this->find($where_str,null,"num");
        return intval($ret['num']);
    }

    /**
     * 获取报表登录log
     * @param bool $b_select_count
     * @param string $where_str
     * @param string $group_by
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_log_buyer_login_list($b_select_count=false,$where_str,$group_by='',$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_log_buyer_login_tbl();
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($group_by)>0)
        {
            if(strlen($where_str)>0)
            {
                $where_str .= ' ';
            }else{
                $where_str .= " 1 ";
            }
            $where_str .="{$group_by}";
        }
        return $this->findAll($where_str,$limit,$order_by,$fields);
    }

}