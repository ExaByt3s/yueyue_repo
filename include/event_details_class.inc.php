<?php
/**
 * 活动正式类
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_details_class extends POCO_TDG
{
    /**
     * 最后一次错误提示
     * @var string
     */
    protected $_last_err_msg = null;
    
    /**
     * 活动资料数组
     *
     * @var array
     */
    protected $_event_detail_arr = array();
    
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        //$this->setServerId(false);
        //$this->setDBName('event_db');
        //$this->setTableName('event_details_tbl');
    }
    
    /**
     * 设置错误提示
     * @param string $msg
     */
    protected function set_err_msg($msg)
    {
        $this->_last_err_msg = $msg;
    }
    
    /**
     * 获取错误提示
     */
    public function get_err_msg()
    {
        return $this->_last_err_msg;
    }

    /**
     * 获取数据
     *
     * @param int $event_id
     * @return array
     */
    public function get_event_by_event_id($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','get_event_by_event_id',$param);
        return $ret;
    }
    
    /*
     * 获取活动状态
     */
    public function get_event_info_status($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','get_event_info_status',$param);
        return $ret;
    }
    
    /**
     * 取列表
     *
     * @param string $where_str    查询条件
     * @param bool $b_select_count 是否返回总数：TRUE返回总数 FALSE返回列表
     * @param string $limit        查询条数
     * @param string $order_by     排序条件
     * @return array|int
     */
    public function get_event_list($where_str = '', $b_select_count = false, $limit = '0,10', $order_by = 'last_update_time DESC')
    {
        $param[] = $where_str;
        $param[] = $b_select_count;
        $param[] = $limit;
        $param[] = $order_by;
        $ret = curl_event_data('event_details_class','get_event_list',$param);
        return $ret;
    }



    /**
     * 活动全文高级搜索(2011-06-16)
     *
     * @param array $querys 搜索条件数组：
     * @param bool $b_select_count TRUE:取记录总数 FALSE:取具体数据
     * @param string $limit 记录数
     * @param string $order_by 排序
     * @param bool $b_group_count TRUE:取分组统计数字 FALSE:取数据
     * @return array
     */
     public function event_fulltext_search($querys, $b_select_count = false, $limit = '0,10', $order_by = 'last_update_time DESC',$b_group_count = false)
     {
         $param[] = $querys;
         $param[] = $b_select_count;
         $param[] = $limit;
         $param[] = $order_by;
         $param[] = $b_group_count;
         $ret = curl_event_data('event_details_class','event_fulltext_search',$param);
         return $ret;

     }

    
    /**
     * 更新数据
     *
     * @param array $data
     * @param int $event_id
     * @return bool
     */
    public function update_event($data, $event_id)
    {
        $event_id = (int)$event_id;
        if( $event_id < 1 )
        {
            $this->set_err_msg('event_id不正确');
            return false;
        }
        $param[] = $data;
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','update_event',$param);
        return $ret;
        
    }

    
    /**
     * 设置活动结束，活动结束流程
     * @param $event_id 活动ID
     * 
     * */
    public function set_event_status_by_finish($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','set_event_status_by_finish',$param);
        return $ret;
    }

    /**
     * 设置活动结束，活动结束流程
     * @param $event_id 活动ID
     * 
     * */
    public function set_event_status_by_cancel($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','set_event_status_by_cancel',$param);
        return $ret;
    }

    
    /**
     * 检查日期是否已经过期
     *
     * @param int $event_id   	活动id
     * @return bool
     */
    public function check_date_is_over($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','check_date_is_over',$param);
        return $ret;

    }

    /*
     * 添加约约活动
     */
    public function add_synchronous_event($data)
    {
        return false;
        $param[] = $data;
        $ret = curl_event_data('event_details_class','add_synchronous_event',$param);
        return $ret;
    }


}
?>