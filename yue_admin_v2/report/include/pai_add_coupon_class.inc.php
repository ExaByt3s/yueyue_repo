<?php
/**
 * @desc:   �Ż�ȯ������Ϣ
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/2
 * @Time:   11:29
 * version: 1.0
 */
class pai_add_coupon_class extends POCO_TDG
{
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId( 22 );
        $this->setDBName( 'yueyue_queue_db' );
    }

    /**
     *�����Ż����״̬��
     */
    private function set_coupon_main_tbl()
    {
        $this->setTableName( 'coupon_queue_state_tbl' );
    }

    /**
     *�����Ż���Ϣ��ͳ��
     */
    private function set_coupon_queue_data_tbl()
    {
        $this->setTableName( 'coupon_queue_data_tbl' );
    }

    // +---------------------------------------------------------------------------------------------------
    // | ��������ݺͲ�ѯ�����ݵĲ���
    // +---------------------------------------------------------------------------------------------------

    /**
     * @param $coupon
     * @param $begin_time
     * @param $end_time
     * @return array
     */
    public function coupon_main_add_info($coupon,$begin_time,$end_time)
    {
        $return_data = array('code'=>0);
        $coupon = trim($coupon);
        $begin_time = (int)$begin_time;
        $end_time = (int)$end_time;
        if(strlen($coupon) <1) $return_data['err'] = '�Ż��벻��Ϊ��';
        if($begin_time <1) return $return_data['err'] = '��ʼʱ�䲻��Ϊ��';
        if($end_time <1) return $return_data['err'] = '����ʱ�䲻��Ϊ��';
        $coupon_result = $this->get_coupon_by_coupon_sn($coupon,$begin_time,$end_time);
        if(!is_array($coupon_result)) $coupon_result = array();
        if(!empty($coupon_result) && is_array($coupon_result))
        {
            $return_data['id'] = (int)$coupon_result['id'];//��ȡID
            if($coupon_result['run_status'] == 0) $return_data['err'] = '���Ż�ȯ���Ѿ����ڣ�״̬Ϊ����ʼ״̬';
            if($coupon_result['run_status'] == 1) $return_data['err'] = '���Ż�ȯ���Ѿ����ڣ�״̬Ϊ��δ����';
            if($coupon_result['run_status'] == 2) $return_data['err'] = '���Ż�ȯ���Ѿ����ڣ�״̬Ϊ���Ѿ����';
            return $return_data;
        }
        $result = $this->add_coupon_queue_states_info($coupon,$begin_time,$end_time);
        if(!is_array($result)) $result = array();
        $code = (int)$result['code'];
        if($code >0)
        {
            $return_data['code'] = $code;
            return $return_data;
        }
        $return_data['err'] = 'ϵͳ����';
        return $return_data;

    }

    /**
     * @param $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_coupon_main_info($insert_data)
    {
        if(!is_array($insert_data) || empty($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '�����鲻��Ϊ��' );
        }
        $this->set_coupon_main_tbl();
        return $this->insert($insert_data,"IGNORE");
    }

    /**
     * @param $coupon
     * @param $begin_time
     * @param $end_time
     * @return array
     * @throws App_Exception
     */
    public function add_coupon_queue_states_info($coupon,$begin_time,$end_time)
    {
        global $yue_login_id;
        $return_data = array('code'=>0);
        $coupon = trim($coupon);
        $begin_time = (int)$begin_time;
        $end_time = (int)$end_time;
        if(strlen($coupon) <1) $return_data['err'] = '�Ż��벻��Ϊ��';
        if($begin_time <1) return $return_data['err'] = '��ʼʱ�䲻��Ϊ��';
        if($end_time <1) return $return_data['err'] = '����ʱ�䲻��Ϊ��';
        $data = array();
        $data['begin_time'] = $begin_time;
        $data['end_time'] = $end_time;
        $data['coupon'] = $coupon;
        $data['user_id'] = $yue_login_id;
        $data['add_time'] = time();
        $data['run_status'] = 0;
        $result = $this->add_coupon_main_info($data);
        unset($data);
        if($result)
        {
            unset($result);
            $return_data['code'] = 1;
            return $return_data;
        }
        $return_data['err'] = '��������ʧ��';
        return $return_data;
    }

    /**
     * @param bool $b_select_count
     * @param $coupon
     * @param int $user_id
     * @param int $run_status
     * @param $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_coupon_main_list($b_select_count=false,$coupon,$user_id,$run_status,$where_str,$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_coupon_main_tbl();
        $coupon = trim($coupon);
        $user_id = (int)$user_id;
        $run_status = (int)$run_status;
        if(strlen($coupon) >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "FIND_IN_SET('{$coupon}',coupon)";
            //sqlSetParam($where_str,'x_coupon',$coupon);
        }
        if($user_id >0)
        {
            if(strlen($where_str)) $where_str .= ' AND ';
            $where_str .= "user_id = {$user_id}";
        }
        if($run_status >=0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "run_status = {$run_status}";
        }
        //��ʼ��ѯ
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * ��ȡһ��������
     * @param int $id ��ID
     * @return array|bool
     */
    public function get_coupon_by_main_id($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_coupon_main_tbl();
        return $this->find("id={$id}");
    }

    /**
     * ��ȡcoupon������
     * @param string $coupon
     * @param int $begin_time
     * @param int $end_time
     * @return array|bool
     */
    public function get_coupon_by_coupon_sn($coupon,$begin_time,$end_time)
    {
        $coupon = trim($coupon);
        $begin_time = (int)$begin_time;
        $end_time = (int)$end_time;
        if(strlen($coupon) <1 || $begin_time <1 || $end_time <1) return false;
        $this->set_coupon_main_tbl();
        $where_str = "coupon=:x_coupon";
        sqlSetParam($where_str,'x_coupon',$coupon);
        if($begin_time >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "begin_time = {$begin_time}";
        }
        if($end_time >0)
        {
            if(strlen($where_str)>0) $where_str .= ' AND ';
            $where_str .= "end_time = {$end_time}";
        }
        $ret = $this->find($where_str,"id DESC");
        return $ret;
    }

    // +---------------------------------------------------------------------------------------------------
    // | ��ѯ�����ݱ���
    // +---------------------------------------------------------------------------------------------------

    /**
     * @param bool $b_select_count
     * @param int $id
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_coupon_data_list($b_select_count=false,$id,$where_str,$order_by='id DESC', $limit='0,20', $fields='*')
    {
        $this->set_coupon_queue_data_tbl();
        $id = (int)$id;
        if($id >0)
        {
            if(strlen($where_str)) $where_str .= ' AND ';
            $where_str .= "id={$id}";
        }
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * @param $id
     * @return array|bool
     */
    public function get_data_info_by_id($id)
    {
        $id = (int)$id;
        if($id <1) return false;
        $this->set_coupon_queue_data_tbl();
        return $this->find("id={$id}");
    }
}