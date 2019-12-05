<?php
/**
 *
 * @desc:   �̼ҹ�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/8/4
 * @Time:   16:16
 * version: 1.0
 */
class pai_report_operator_class extends  POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_v2_db' );
        //$this->setTableName ( 'yueyue_mall_operator_report_tbl_201508' );
    }

    /**
     * @param string $time
     * @return int
     */
    private function setTable($time ='')
    {
        $time = trim($time);
        if(strlen($time) <1) $time = date('Y-m-d',time()-24*3600);
        $time = date('Ym',strtotime($time));
        $sign_tab =  'yueyue_mall_operator_report_tbl_'.$time;
        $res = db_simple_getdata("SHOW TABLES FROM {$this->_db_name} LIKE '{$sign_tab}'", TRUE, 22);
        if(!is_array($res) || empty($res))
        {
            return 0;
        }
        $this->setTableName($sign_tab);
        return 1;
    }

    /**
     * ��ȡĳһ�µ�����
     * @param bool $b_select_count �Ƿ��ѯ����
     * @param string $month   ���ڣ�ʾ��:'2015-08'
     * @param string $operator_id ������ID,ʾ��:100293
     * @param string $where_str   ��ӣ�ʾ���� $where_str ="user_id = 100000";
     * @param string $GROUP_BY    �����ѯ,ʾ��:"GROUP BY user_id"
     * @param string $order_by    ����
     * @param string $limit       ��ѯ����,ʾ��:'0,10'
     * @param string $fields      ��ѯ�ֶ�,ʾ��:'user_id';
     * @return array|int
     */
    public function get_operator_list_by_month($b_select_count = false,$month,$operator_id,$where_str ='',$GROUP_BY ='',$order_by='add_time DESC,user_id DESC',$limit = '0,20', $fields = '*')
    {
        $month = trim($month);
        $operator_id = intval($operator_id);
        //ѡ�����ݿ�
        if(strlen($month) <1) $month = date('Y-m',time()-24*3600);
        if(date('Y-m',strtotime($month)) != $month) return false;
        $retID = $this->setTable($month);
        if($retID <1) return false;
        if($operator_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "operator_id={$operator_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($GROUP_BY) >0) $where_str .= " {$GROUP_BY}";//ƴ��GROUP BY
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ��ȡһ�������
     * @param bool $b_select_count �Ƿ��ѯ����
     * @param string $date   ���ڣ�ʾ��:'2015-08-04'
     * @param string $operator_id ������ID,ʾ��:100293
     * @param string $where_str   ��ӣ�ʾ���� $where_str ="user_id = 100000";
     * @param string $GROUP_BY    �����ѯ,ʾ��:"GROUP BY user_id"
     * @param string $order_by    ����
     * @param string $limit       ��ѯ����,ʾ��:'0,10'
     * @param string $fields      ��ѯ�ֶ�,ʾ��:'user_id';
     * @return array|int
     */
    public function get_operator_list_by_day($b_select_count = false,$date,$operator_id,$where_str ='',$GROUP_BY ='',$order_by='add_time DESC,user_id DESC',$limit = '0,20', $fields = '*')
    {
        $date = trim($date);
        $operator_id = intval($operator_id);

        //ѡ�����ݿ�
        if(strlen($date) <1) $date = date('Y-m-d',time()-24*3600);
        if(date('Y-m-d',strtotime($date)) != $date) return false;
        $retID = $this->setTable($date);
        if($retID <1) return false;

        //sql����
        if(strlen($where_str) >0) $where_str .= ' AND ';
        $where_str .= "add_time=:x_add_time";
        sqlSetParam($where_str,'x_add_time',$date);
        if($operator_id >0)
        {
            if(strlen($where_str) >0) $where_str .= ' AND ';
            $where_str .= "operator_id={$operator_id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        if(strlen($GROUP_BY) >0) $where_str .= " {$GROUP_BY}";//ƴ��GROUP BY
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }
}