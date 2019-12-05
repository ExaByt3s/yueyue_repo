<?php
/**
 * @file event_date_class.inc.php
 * @synopsis 约拍活动类
 * @author wuhy@yueus.com
 * @version null
 * @date 2015-09-03
 */

class event_date_class extends POCO_TDG
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
    protected $_event_date_arr = array();

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
     * @param int $apply_id
     * @return array
     */
    public function get_date_id_by_enroll_id($apply_id)
    {
        $param[] = $apply_id;
        $ret = curl_event_data('event_date_class','get_date_id_by_enroll_id',$param);
        return $ret;
    }

    /**
        * @synopsis 获取全部活动数据
        *
        * @param $count
        * @param $where_str
        * @param $order_by_str
        * @param $limit_str
        *
        * @returns 
     */
    public function get_all_event_date($count,$where_str, $order_by_str, $limit_str)
    {
        $param[] = $count;
        $param[] = $where_str;
        $param[] = $order_by_str;
        $param[] = $limit_str;
        $ret = curl_event_data('event_date_class','get_all_event_date',$param);
        return $ret;
    }
}
?>
