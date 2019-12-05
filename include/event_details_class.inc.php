<?php
/**
 * ���ʽ��
 *
 * @author tom
 * @copyright 2010-12-31
 */



class event_details_class extends POCO_TDG
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
    protected $_event_detail_arr = array();
    
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
     * ��ȡ�״̬
     */
    public function get_event_info_status($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','get_event_info_status',$param);
        return $ret;
    }
    
    /**
     * ȡ�б�
     *
     * @param string $where_str    ��ѯ����
     * @param bool $b_select_count �Ƿ񷵻�������TRUE�������� FALSE�����б�
     * @param string $limit        ��ѯ����
     * @param string $order_by     ��������
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
     * �ȫ�ĸ߼�����(2011-06-16)
     *
     * @param array $querys �����������飺
     * @param bool $b_select_count TRUE:ȡ��¼���� FALSE:ȡ��������
     * @param string $limit ��¼��
     * @param string $order_by ����
     * @param bool $b_group_count TRUE:ȡ����ͳ������ FALSE:ȡ����
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
     * ��������
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
            $this->set_err_msg('event_id����ȷ');
            return false;
        }
        $param[] = $data;
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','update_event',$param);
        return $ret;
        
    }

    
    /**
     * ���û���������������
     * @param $event_id �ID
     * 
     * */
    public function set_event_status_by_finish($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','set_event_status_by_finish',$param);
        return $ret;
    }

    /**
     * ���û���������������
     * @param $event_id �ID
     * 
     * */
    public function set_event_status_by_cancel($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','set_event_status_by_cancel',$param);
        return $ret;
    }

    
    /**
     * ��������Ƿ��Ѿ�����
     *
     * @param int $event_id   	�id
     * @return bool
     */
    public function check_date_is_over($event_id)
    {
        $param[] = $event_id;
        $ret = curl_event_data('event_details_class','check_date_is_over',$param);
        return $ret;

    }

    /*
     * ���ԼԼ�
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