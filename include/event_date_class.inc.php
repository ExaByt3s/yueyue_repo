<?php
/**
 * @file event_date_class.inc.php
 * @synopsis Լ�Ļ��
 * @author wuhy@yueus.com
 * @version null
 * @date 2015-09-03
 */

class event_date_class extends POCO_TDG
{
    /**
     * ���һ�δ�����ʾ
     * @var string
     */
    protected $_last_err_msg = null;

    /**
     * ���������
     *
     * @var array
     */
    protected $_event_date_arr = array();

    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        //$this->setServerId(false);
        //$this->setDBName('event_db');
        //$this->setTableName('event_details_tbl');
    }

    /**
     * ���ô�����ʾ
     * @param string $msg
     */
    protected function set_err_msg($msg)
    {
        $this->_last_err_msg = $msg;
    }

    /**
     * ��ȡ������ʾ
     */
    public function get_err_msg()
    {
        return $this->_last_err_msg;
    }

    /**
     * ��ȡ����
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
        * @synopsis ��ȡȫ�������
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
