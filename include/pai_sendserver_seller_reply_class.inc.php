<?php
/**
 * @desc:   �̼���Ϣ�ظ���
 *@User:    xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/18
 * @Time:   16:05
 * version: 1.0
 */

class pai_sendserver_seller_reply_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_sendserver_for_seller_db' );
    }

    /**
     * �������ݿ�
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
     *��������ظ���
     */
    private function set_seven_days_tbl()
    {
        $this->setTableName( 'sendserver_for_seller_reply_seven_days_log' );
    }

    /**
     * ��ȡ�����б�
     * @param bool $b_select_count
     * @param string $date string���͵�ʱ���ʽ�ģ�ʾ����'2015-08-07'
     * @param int $type_id string  �̼���֤����
     * @param string $where_str  ����
     * @param string $group_by   ��group by ��ʽ����
     * @param string $order_by   ����
     * @param string $limit   ѭ��
     * @param string $fields  �ֶ�
     * @return array|bool|int
     */
    public function get_info_list($b_select_count = false,$date,$type_id,$where_str = '',$group_by='',$order_by = 'id DESC', $limit = '0,10', $fields = '*')
    {
        $date = trim($date);
        $group_by = trim($group_by);
        $type_id = intval($type_id);
        $retID = $this->set_for_seller_tbl($date);
        $retID = intval($retID);
        if($retID <1) return false;//û���ܹ�ѡ����˳�
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
     * ͨ���̼�ID��ѯ�ظ���
     * @param int $seller_id �̼�ID
     * @param int $interval_time �ظ����ʱ�䣬�룬ʾ��:5���� 5*60
     * @param int $type_id  ����
     * @param int $type ���ͣ�0��ʾ��ѯȫ����1��ʾֻ��ѯ�̼��û����͵Ļظ��ʣ�2ֻ��ѯϵͳ���͵Ļظ���
     * @param string $where_str ����
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
            if($type == 1) $where_str .= "sender_id >= 100000"; //�������û����͵�
            else $where_str .= "sender_id <100000";//��ϵͳ���͵�
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
        $this->set_seven_days_tbl();//�趨��
        $sql_str = "SELECT (SUM(CASE WHEN interval_time>0&&interval_time<={$interval_time} THEN 1 ELSE 0 END)/SUM(1)) AS scale FROM {$this->_db_name}.{$this->_tbl_name} WHERE {$where_str}";
        $ret = db_simple_getdata($sql_str,TRUE,22);
        return sprintf('%.2f',$ret['scale']);
    }

}