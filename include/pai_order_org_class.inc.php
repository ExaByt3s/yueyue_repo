<?php
/**
 * 优惠券总表
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-09-23 14:48
 * @version 2.0
 */

class pai_order_org_class extends POCO_TDG
{
    /**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setDBName ( 'event_db' );
		$this->setTableName ( 'event_date_tbl' );
	}

    /**
     * @param bool $b_select_count
     * @param string $start_time   //开始时间
     * @param string $end_time     //结束时间
     * @param $where_str           //条件
     * @param string $limit        //循环条数
     * @param string $order_by     //排序
     * @param string $fields      //查询字段
     * @return array|int
     */
    public function get_user_list($b_select_count = false, $start_time = '',$end_time = '',$where_str, $limit = '0,10', $order_by = 'id DESC',  $fields = '*')
    {
        $param[] = $b_select_count;
        $param[] = $start_time;
        $param[] = $end_time;
        $param[] = $where_str;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_user_list',$param);
        return $ret;
    }
    /**
     * 获取user_id
     * @param string $where_str
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return mixed
     */
    public function get_user_id_by_where_str($where_str = '', $limit = '0,10', $order_by = 'id DESC',  $fields = '*')
	{
        $param[] = $where_str;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_user_id_by_where_str',$param);
		return $ret;
	}
    /**
     * 获取订单列表
     * @param bool $b_select_count
     * @param array $search_arr
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return mixed
     */
    public function get_order_search_list($b_select_count = false, $search_arr = array(), $limit = '0,10', $order_by = 'id DESC',  $fields = '*')
	{

        $param[] = $b_select_count;
        $param[] = $search_arr;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_order_search_list',$param);
        return $ret;
	}
    /**
     * 通过user_id获取交易详情(模特id)
     * @param $user_id
     * @param $yue_login_id
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @param bool $b_select_count
     * @return mixed
     */
    public function get_order_list_by_user_id($user_id,$yue_login_id,$limit = '0,10', $order_by ='date_time DESC', $fields= '*', $b_select_count = false)
	{
        $param[] = $user_id;
        $param[] = $yue_login_id;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $param[] = $b_select_count;
        $ret = curl_event_data('pai_order_org_class','get_order_list_by_user_id',$param);
        return $ret;
	}
    /**
     * 获取模特约拍次数
     * @param bool $b_select_count
     * @param $user_id
     * @param $type
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return mixed
     */
    public function get_order_count_by_user_id($b_select_count = false, $user_id, $type , $limit = '0,10', $order_by = 'date_id DESC', $fields = '*')
    {
        $user_id = (int)$user_id;
        $param[] = $b_select_count;
        $param[] = $user_id;
        $param[] = $type;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_order_count_by_user_id',$param);
        return $ret;
    }

    /**
     * 获取模特约拍次数
     * @param bool $b_select_count
     * @param $user_id
     * @param $type
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return mixed
     */
    public function get_order_count_by_user_id_v2($b_select_count = false, $user_id, $type , $limit = '0,99999999', $order_by = 'date_id DESC', $fields = '*')
    {
        $user_id = (int)$user_id;
        $param[] = $b_select_count;
        $param[] = $user_id;
        $param[] = $type;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_order_count_by_user_id_v2',$param);
        return $ret;
    }

    /**
     * 获取模特约拍次数
     * @param $user_id
     * @param $type
     * @return mixed
     */
    public function get_sum_price_by_user_id($user_id, $type)
    {
        $user_id = (int)$user_id;
        $param[] = $user_id;
        $param[] = $type;
        $ret = curl_event_data('pai_order_org_class','get_sum_price_by_user_id',$param);
        return $ret;
    }


    /**
     * 获取模特约拍次数
     * @param $user_id
     * @param $type
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return mixed
     */
    public function get_sum_price_by_user_id_v2($user_id, $type,$limit = '0,99999999', $order_by = 'event_id DESC', $fields = '*')
    {
        $user_id = (int)$user_id;
        $param[] = $user_id;
        $param[] = $type;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_sum_price_by_user_id_v2',$param);
        return $ret;
    }

    /**
     * 摄影师约拍次数
     * @param bool $b_select_count  [true|false个数|列表]
     * @param int $user_id              [摄影师ID]
     * @param string $type          [day|week|month]
     * @param string $limit         [循环条数]
     * @param string $order_by      [排序]
     * @param string $fields        [字段]
     * @return array|int            [返回数组|int值]
     */
    public function get_order_count_by_from_id($b_select_count = false, $user_id, $type , $limit = '0,20', $order_by = 'date_id DESC', $fields = '*')
    {
        $user_id = (int)$user_id;
        $param[] = $b_select_count;
        $param[] = $user_id;
        $param[] = $type;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_order_count_by_from_id',$param);
        return $ret;
    }
    /**
     * 摄影师花费多少钱
     * @param int $user_id
     * @param  string $type
     * @return int|mixed
     * @throws App_Exception
     */
    public function get_sum_price_by_form_id($user_id, $type)
    {
        $user_id = (int)$user_id;
        $type = trim($type);
        $param[] = $user_id;
        $param[] = $type;
        $ret = curl_event_data('pai_order_org_class','get_sum_price_by_form_id',$param);
        return $ret;
    }


    /**
     * 通过from_id获取交易详情(摄影师id)
     * @param bool $b_select_count
     * @param $user_id
     * @param string $limit
     * @param string $order_by
     * @param string $fields
     * @return array|bool
     */
    public function get_order_list_by_from_id($b_select_count = false, $user_id,$limit = '0,10', $order_by ='date_time DESC', $fields= '*')
	{

        $user_id = (int)$user_id;
        $param[] = $b_select_count;
        $param[] = $user_id;
        $param[] = $limit;
        $param[] = $order_by;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_order_list_by_from_id',$param);
		return $ret;
	}

    /**
     * 获取where 条件
     * @param $search_arr
     * @return string
     */
    public function get_order_create_where($search_arr)
	{
        $param[] = $search_arr;
        $ret = curl_event_data('pai_order_org_class','get_order_create_where',$param);
        return $ret;
    }
    /**
     *  获取总交易次数
     * @param $user_id
     * @param $org_user_id
     * @return bool|int
     */
    function get_user_total_count_by_user_id($user_id, $org_user_id)
    {
        $user_id = intval($user_id);
        $org_user_id = intval($org_user_id);
        $param[] = $user_id;
        $param[] = $org_user_id;
        $ret = curl_event_data('pai_order_org_class','get_user_total_count_by_user_id',$param);
        return $ret;
    }

    /**
     * 获取那几天的订单条数
     * @param int $sum_time 天数
     * @param $org_user_id  机构id
     * @return int
     * @throws App_Exception
     */
    public function get_order_count($sum_time = 30, $org_user_id)
    {
        $sum_time = intval($sum_time);
        $org_user_id = intval($org_user_id);
        $param[] = $sum_time;
        $param[] = $org_user_id;
        $ret = curl_event_data('pai_order_org_class','get_order_count',$param);
        return $ret;
    }

    /**
     * 获取那几天的交易金额
     * @param int $sum_time
     * @param $org_user_id
     * @return string
     * @throws App_Exception
     */
    public function get_user_count_budget_by_org_id($sum_time = 30, $org_user_id)
    {
        $sum_time = (int)$sum_time;
        $org_user_id = intval($org_user_id);
        $param[] = $sum_time;
        $param[] = $org_user_id;
        $ret = curl_event_data('pai_order_org_class','get_user_count_budget_by_org_id',$param);
        return $ret;
    }

    /**
     * 获取实际到账的交易金额
     * @param $user_id
     * @param $org_user_id
     * @return bool|string
     * @throws App_Exception
     */
    public function get_user_count_budget_by_user_id($user_id,$org_user_id)
    {
        $user_id = (int)$user_id;
        $org_user_id = intval($org_user_id);
        if($user_id <1) return false;
        $poco_id= get_relate_poco_id($user_id);
        $param[] = $poco_id;
        $param[] = $org_user_id;
        $ret = curl_event_data('pai_order_org_class','get_user_count_budget_by_user_id',$param);
        return $ret;
    }

    /**
     * 成功交易次数
     * @param $user_id
     * @param $org_user_id
     * @return bool|int
     * @throws App_Exception
     */
    public function get_user_success_count_by_user_id($user_id, $org_user_id)
    {
        $user_id = (int)$user_id;
        $org_user_id = intval($org_user_id);
        if($user_id <1) return false;
        $poco_id= get_relate_poco_id($user_id);
        $param[] = $poco_id;
        $param[] = $org_user_id;
        $ret = curl_event_data('pai_order_org_class','get_user_success_count_by_user_id',$param);
        return $ret;
    }

    /**
     * 获取总交易次数
     * @param string $where_str
     * @return int
     */
    public function get_user_total_count_by_from_where($where_str = '')
    {
        $param[] = $where_str;
        $ret = curl_event_data('pai_order_org_class','get_user_total_count_by_from_where',$param);
        return $ret;
    }

    /**
     * 获取实际到账的交易金额
     * @param $user_id
     * @param $start_time
     * @param $end_time
     * @return bool
     * @throws App_Exception
     */
    public function get_user_count_budget_by_from_id($user_id, $start_time, $end_time)
    {
    	$user_id = (int)$user_id;
        $start_time = trim($start_time);
        $end_time = trim($end_time);
        if($user_id <1) return false;
        $poco_id= get_relate_poco_id($user_id);
        $param[] = $poco_id;
        $param[] = $start_time;
        $param[] = $end_time;
        $ret = curl_event_data('pai_order_org_class','get_user_count_budget_by_from_id',$param);
        return $ret;
    }

    /**
     * 成功交易次数
     * @param $user_id
     * @param $start_time
     * @param $end_time
     * @return bool
     * @throws App_Exception
     */
    public function get_user_success_count_by_from_id($user_id, $start_time, $end_time)
    {
    	$user_id = (int)$user_id;
        if($user_id <1) return false;
        $poco_id= get_relate_poco_id($user_id);
        $param[] = $poco_id;
        $param[] = $start_time;
        $param[] = $end_time;
        $ret = curl_event_data('pai_order_org_class','get_user_count_budget_by_from_id',$param);
        return $ret;
    }

    /**
     * 获取约拍数据
     * @param string $where_str 条件
     * @param string $order_by  排序
     * @param string $limit  循环几条
     * @param string $fields  查询数据
     * @return mixed
     * @throws App_Exception
     */
    public function  get_user_id_by_yue_where($where_str = '', $order_by = 'id DESC', $limit = '0,10000', $fields = '*')
    {
        $param[] = $where_str;
        $param[] = $order_by;
        $param[] = $limit;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_user_id_by_yue_where',$param);
        return $ret;
    }

    /**
     * 获取约拍价钱
     * @param string $date 时间
     * @return mixed
     */
    public function get_yuepai_price_by_time($date = '')
    {
        $param[] = $date;
        $ret = curl_event_data('pai_order_org_class','get_yuepai_price_by_time',$param);
        return $ret;
    }

    /**
     * 获取约拍价钱
     * @param string $where_str
     * @return int
     * @throws App_Exception
     */
    public function get_yuepai_price_by_where_str($where_str = '')
    {
        $param[] = $where_str;
        $ret = curl_event_data('pai_order_org_class','get_yuepai_price_by_where_str',$param);
        return $ret;
    }

    /**
     * 获取外拍价钱
     * @param string $date 时间
     * @return int
     * @throws App_Exception
     */
    public function get_waipai_price_by_time($date = '')
    {
        $param[] = $date;
        $ret = curl_event_data('pai_order_org_class','get_waipai_price_by_time',$param);
        return $ret;
    }

    /**
     * 获取外拍价钱
     * @param string $where_str
     * @return int
     * @throws App_Exception
     */
    public function get_waipai_price_by_where_str($where_str = '')
    {
        $param[] = $where_str;
        $ret = curl_event_data('pai_order_org_class','get_waipai_price_by_where_str',$param);
        return $ret;
    }

    /**
     * 获取外拍数据
     * @param bool $b_select_count 条件
     * @param string $where_str 排序
     * @param string $order_by  循环几条
     * @param string $limit   查询数据
     * @param string $fields
     * @return array|int
     */
    public function get_waipai_enroll_list($b_select_count = false, $where_str = '', $order_by = 'enroll_id DESC', $limit = '0,20', $fields = '*' )
    {
        $param[] = $b_select_count;
        $param[] = $where_str;
        $param[] = $order_by;
        $param[] = $limit;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_waipai_enroll_list',$param);
        return $ret;
    }

    /**
     * 获取外拍数据
     * @param bool $b_select_count 条件
     * @param string $where_str  排序
     * @param string $order_by  循环几条
     * @param string $limit     查询数据
     * @param string $fields
     * @return array|int
     */
    public function get_waipai_envent_list($b_select_count = false, $where_str = '', $order_by = 'event_id DESC', $limit = '0,20', $fields = '*' )
    {
        $param[] = $b_select_count;
        $param[] = $where_str;
        $param[] = $order_by;
        $param[] = $limit;
        $param[] = $fields;
        $ret = curl_event_data('pai_order_org_class','get_waipai_envent_list',$param);
        return $ret;
    }


    /**
     * 获取交易数和交易金额 有可能退款的
     * @param string $where_str  条件
     * @param $month    年月
     * @return array    返回一个数组
     * @throws App_Exception
     */
    public function get_order_date_by_user_str($where_str = '' , $month)
    {
        $param[] = $where_str;
        $param[] = $month;
        $ret = curl_event_data('pai_order_org_class','get_order_date_by_user_str',$param);
        return $ret;
    }

    /**
     *获取交易数和交易金额
     * @param $where_in_str 条件
     * @param $month        年月
     * @return array        返回一个数组
     * @throws App_Exception
     */
    public function get_detail_by_user_str($where_in_str , $month)
    {
        $where_in_str = intval($where_in_str);
        $where_tmp_str = '';
        if(strlen($where_in_str) > 0)
        {
            $user_arr = explode(',', $where_in_str);
            if(!is_array($user_arr)) $user_arr = array();
            foreach ($user_arr as $key => $user_id)
            {
                if($key != 0) $where_tmp_str .= ',';
                $where_tmp_str .= get_relate_poco_id($user_id);
            }
        }
        $param[] = $where_in_str;
        $param[] = $month;
        $ret = curl_event_data('pai_order_org_class','get_detail_by_user_str',$param);
        if(!is_array($ret)) $ret = array();
        foreach ($ret as $retKey => $retVal)
        {
            $ret[$retKey]['user_id'] = get_relate_yue_id($retVal['user_id']);
        }
        return $ret;
    }

    /**
     * 获取交易数和交易金额
     * @param string $where_in_str
     * @param string $day
     * @return array
     */
    public function get_detail_by_user_str_day($where_in_str , $day)
    {
        $day = trim($day);
        if(strlen($day) <1) return false;
        $where_tmp_str = '';
        if(strlen($where_in_str) > 0)
        {
            $user_arr = explode(',', $where_in_str);
            if(!is_array($user_arr)) $user_arr = array();
            foreach ($user_arr as $key => $user_id)
            {
                if($key != 0) $where_tmp_str .= ',';
                $where_tmp_str .= get_relate_poco_id($user_id);
            }
        }
        $param[] = $where_tmp_str;
        $param[] = $day;
        $ret = curl_event_data('pai_order_org_class','get_detail_by_user_str_day',$param);
        if(!is_array($ret)) $ret = array();
        foreach ($ret as $retKey => $retVal)
        {
            $ret[$retKey]['user_id'] = get_relate_yue_id($retVal['user_id']);
        }
        return $ret;
    }

    /**
     * 获取被拒绝的次数
     * @param string $where_str 条件
     * @param string $month     年月
     * @return array            返回一个数组
     * @throws App_Exception
     */
    public function get_cancel_order_by_user_str($where_str ='' , $month)
    {
        $param[] = $where_str;
        $param[] = $month;
        $ret = curl_event_data('pai_order_org_class','get_cancel_order_by_user_str',$param);
        return $ret;
    }
    /**
     * 获取被拒绝的次数
     * @param  string $where_str 条件
     * @param  string $day  天
     * @return array  返回一个数组
     * @throws App_Exception
     */
    public function get_cancel_order_by_user_str_day($where_str = '' , $day)
    {
        $param[] = $where_str;
        $param[] = $day;
        $ret = curl_event_data('pai_order_org_class','get_cancel_order_by_user_str_day',$param);
        return $ret;
    }


}

