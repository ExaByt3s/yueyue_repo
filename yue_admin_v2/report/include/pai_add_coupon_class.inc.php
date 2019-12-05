<?php
/**
 * @desc:   优惠券数据信息
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/2
 * @Time:   11:29
 * version: 1.0
 */
class pai_add_coupon_class extends POCO_TDG
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId( 22 );
        $this->setDBName( 'yueyue_queue_db' );
    }

    /**
     *设置优惠码的状态表
     */
    private function set_coupon_main_tbl()
    {
        $this->setTableName( 'coupon_queue_state_tbl' );
    }

    /**
     *设置优惠信息的统计
     */
    private function set_coupon_queue_data_tbl()
    {
        $this->setTableName( 'coupon_queue_data_tbl' );
    }

    // +---------------------------------------------------------------------------------------------------
    // | 添加主数据和查询主数据的部分
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
        if(strlen($coupon) <1) $return_data['err'] = '优惠码不能为空';
        if($begin_time <1) return $return_data['err'] = '开始时间不能为空';
        if($end_time <1) return $return_data['err'] = '结束时间不能为空';
        $coupon_result = $this->get_coupon_by_coupon_sn($coupon,$begin_time,$end_time);
        if(!is_array($coupon_result)) $coupon_result = array();
        if(!empty($coupon_result) && is_array($coupon_result))
        {
            $return_data['id'] = (int)$coupon_result['id'];//获取ID
            if($coupon_result['run_status'] == 0) $return_data['err'] = '该优惠券码已经存在，状态为：开始状态';
            if($coupon_result['run_status'] == 1) $return_data['err'] = '该优惠券码已经存在，状态为：未进行';
            if($coupon_result['run_status'] == 2) $return_data['err'] = '该优惠券码已经存在，状态为：已经完成';
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
        $return_data['err'] = '系统出错';
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
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . '：数组不能为空' );
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
        if(strlen($coupon) <1) $return_data['err'] = '优惠码不能为空';
        if($begin_time <1) return $return_data['err'] = '开始时间不能为空';
        if($end_time <1) return $return_data['err'] = '结束时间不能为空';
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
        $return_data['err'] = '插入数据失败';
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
        //开始查询
        if($b_select_count == true)
        {
            return $this->findCount($where_str,$fields);
        }
        $ret = $this->findAll($where_str,$limit,$order_by,$fields);
        return $ret;
    }

    /**
     * 获取一条主数据
     * @param int $id 主ID
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
     * 获取coupon码数据
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
    // | 查询字数据表部分
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