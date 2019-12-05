<?php
/**
 * @file pai_risk_class.inc.php
 * @synopsis 风险控制类
 * @author wuhy@yueus.com
 * @version null
 * @date 2015-10-20
 */

class pai_risk_class extends POCO_TDG
{
    /**
     * 反刷单规则 ，一条规则对应一个方法，对象为某一商家
     */
    public $rules_list = array(
        // A.同一消费者 同一商家
        'A1' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同ID）一天2次消费同商品',//全品类规则描述
                '31' => '相同消费者（同ID）一天3次消费同商品',//31模特服务规则描述
            ), 
        ),
        'A2' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同支付宝账户）一天2次消费同商品',
                '31' => '相同消费者（同支付宝账户）一天3次消费同商品',
            ), 
        ),
        'A3' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同微信账户）一天2次消费同商品',
                '31' => '相同消费者（同微信账户）一天3次消费同商品',
            ), 
        ),
        'A4' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同ID）一天3次消费不同商品',
                '31' => '相同消费者（同ID）一天大于3次消费不同商品',
            ), 
        ),
        'A5' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同支付宝账户）一天3次消费不同商品',
                '31' => '相同消费者（同支付宝账户）一天大于3次消费不同商品',
            ), 
        ),
        'A6' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '相同消费者（同微信账户）一天3次消费不同商品',
                '31' => '相同消费者（同微信账户）一天大于3次消费不同商品',
            ), 
        ),
        'A7' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '同一号段（手机号码前7位相同）一天2次消费同商品',
                '31' => '同一号段（手机号码前7位相同）一天3次消费同商品',
            ), 
        ),
        'A8' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '同一号段（手机号码前7位相同）一天2次消费不同商品',
                '31' => '同一号段（手机号码前7位相同）一天大于3次消费不同商品',
            ), 
        ),
        // B.签到时间
        'B1' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '频繁签到 － 半小时内出现2次签到，忽略自动签到',
                '31' => '频繁支付 － 半小时内出现2次支付', 
            ), 
        ),
        'B2' => array(
            'rule_score' => 0,
            'status' => 1,
            'remark' => array(
                0 => '非常规时间签到 － PM10:00~AM6:00，忽略自动签到',
            ), 
        ),
    );

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId(101);
        $this->setDBName('pai_risk_db');
    }

    /**
     * 指定表，刷单嫌疑商家记录表
     */
    private function set_scalping_check_log_tbl()
    {
        $this->setTableName('risk_scalping_check_log_tbl');
    }

    /**
     * 指定表，刷单订单数据表
     */
    private function set_scalping_order_tbl()
    {
        $this->setTableName('risk_scalping_order_tbl');
    }

    /**
     * 指定表，刷单订单数据表
     */
    private function set_scalping_seller_tbl()
    {
        $this->setTableName('risk_scalping_seller_tbl');
    }

    /**
     * 指定表，刷单白名单表
     */
    private function set_scalping_white_list_tbl()
    {
        $this->setTableName('risk_scalping_white_list_tbl');
    }

    /**
     * @synopsis 添加订单
     *
     * @param arrray $data 数据
     * $data = array(
     *    'order_id' => '',
     *    'order_sn' => '',
     *    'goods_id' => '',
     *    'seller_user_id' => '',
     *    'buyer_user_id' => '',
     *    'buyer_location_id' => '',
     *    'buyer_reg_ip' => '',
     *    'buyer_reg_time' => '',
     *    'buyer_cellphone' => '',
     *    'buyer_alipay_account' => '',
     *    'buyer_weixin_openid' => '',
     *    'discount_amount' => '',
     *    'pay_time' => '',
     *    'is_auto_sign' => '',
     *    'sign_time' => '',
     *    'add_time' => '',
     *    'add_date' => '',
     *)
     *
     * @returns int 数据库主键ID
     */
    private function add_order($data)
    {
        if( !is_array($data) || empty($data) )
        {
            return 0;
        }

        $this->set_scalping_order_tbl();
        return $this->insert($data, 'REPLACE');
    }

    /**
     * @synopsis 同步订单
     *
     * @param int $min_time 开始时间
     * @param int $max_time 结束时间
     *
     * @returns array $result = array('result'=>1, 'message'=>'成功同步', 'quantity'=>102);
     */
    public function sync_order($min_time, $max_time)
    {
        $result = array('result'=>0, 'message'=>'', 'quantity'=>0);

        //检查参数
        $min_time = intval($min_time);
        $max_time = intval($max_time);
        if( $min_time<1 || $max_time<1 || $max_time<$min_time )
        {
            $result['result'] = -1;
            $result['message'] = '参数错误';
            return $result;
        }

        include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
        $payment_obj = POCO::singleton('pai_payment_class');
        $mall_order_obj = POCO::singleton('pai_mall_order_class');
        $pai_user_obj = POCO::singleton('pai_user_class');

        $where_str = "discount_amount>0 AND sign_time>={$min_time} AND sign_time<={$max_time}";
        $order_list = $mall_order_obj->get_order_list(-1, 8, false, $where_str, '', '0,99999999');

        $quantity = 0;
        $cur_date = date('Y-m-d');
        $data = array();
        foreach($order_list as $order_info)
        {
            $order_detil_list = $mall_order_obj->get_detail_list_all($order_info['order_id']);
            $order_info['goods_id'] = $order_detil_list[0]['goods_id'];
            //补全用户基本信息
            $user_info = $pai_user_obj->get_user_info($order_info['buyer_user_id']);
            $order_info['buyer_location_id'] = $user_info['location_id'];
            $order_info['buyer_reg_ip'] = $user_info['reg_ip'];
            $order_info['buyer_reg_time'] = $user_info['add_time'];
            $order_info['buyer_cellphone'] = $user_info['cellphone'];
            //补全支付账户信息
            $payment_info = $payment_obj->get_payment_info($order_info['payment_no']);
            $order_info['buyer_alipay_account'] = $payment_info['third_buyer'];
            $order_info['buyer_weixin_openid'] = $payment_info['openid'];
            //入库日期
            $order_info['add_date'] = $cur_date;

            $data = array(
                'order_id' => $order_info['order_id'],
                'order_sn' => $order_info['order_sn'],
                'goods_id' => $order_info['goods_id'],
                'type_id' => $order_info['type_id'],
                'seller_user_id' => $order_info['seller_user_id'],
                'buyer_user_id' => $order_info['buyer_user_id'],
                'buyer_location_id' => $order_info['buyer_location_id'],
                'buyer_reg_ip' => $order_info['buyer_reg_ip'],
                'buyer_reg_time' => $order_info['buyer_reg_time'],
                'buyer_cellphone' => $order_info['buyer_cellphone'],
                'buyer_alipay_account' => $order_info['buyer_alipay_account'],
                'buyer_weixin_openid' => $order_info['buyer_weixin_openid'],
                'discount_amount' => $order_info['discount_amount'],
                'pay_time' => $order_info['pay_time'],
                'is_auto_sign' => $order_info['is_auto_sign'],
                'sign_by' => $order_info['sign_by'],
                'sign_time' => $order_info['sign_time'],
                'add_time' => $order_info['add_time'],
                'add_date' => $order_info['add_date'],
            );
            $this->add_order($data);
            $quantity++;
        }

        $result['result'] = 1;
        $result['message'] = '成功';
        $result['quantity'] = $quantity;
        return $result;
    }

    /**
        * @synopsis 添加白名单商家
        *
        * @param $user_id int 商家用户ID
        * @param $data array 其他信息
        *
        * @returns int 大于0成功
     */
    public function add_scalping_white_list_seller($user_id, $data)
    {
        $user_id = intval($user_id);
        if ($user_id<1 || !is_array($data) || count($data)<1) {
            return 0;
        }

        $cur_time = time();
        $data['user_id'] = $user_id; 
        $data['add_time'] = $cur_time; 

        $this->set_scalping_white_list_tbl();
        $this->insert($data, "REPLACE");
        return $this->get_affected_rows();
    }

    /**
        * @synopsis 删除白名单商家
        *
        * @param $user_id int 商家用户ID
        *
        * @returns int 大于0成功
     */
    public function remove_scalping_white_list_seller($user_id)
    {
        $user_id = intval($user_id);
        if ($user_id<1) {
            return 0;
        }

        $this->set_scalping_white_list_tbl();
        return $this->delete("user_id={$user_id}");
    }

    /**
        * @synopsis 取得白名单列表
        *
        * @param bool $b_select_count 是否返回计数
        * @param string $where_str 附加的条件语句
        * @param string $order_by 排序语句
        * @param string $limit 分页语句
        * @param string $fields 字段
        *
        * @returns array 商家列表
     */
    public function get_scalping_white_list($b_select_count=false, $where_str='', $order_by='', $limit='0,99999999', $fields = '*')
    {
        $sql_where = '1 ';
        if( strlen($where_str)>0 )
        {
            $sql_where .= ' AND '.$where_str;
        }

        $this->set_scalping_white_list_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }
        $seller_list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $seller_list;
    }

    /**
     * @synopsis 获取订单列表
     *
     * @param int $seller_user_id  商家用户ID
     * @param bool $b_select_count 是否返回计数
     * @param string $where_str 附加的条件语句
     * @param string $order_by 排序语句
     * @param string $limit 分页语句
     * @param string $fields 字段
     *
     * @returns array 二维的订单列表
     */
    public function get_order_list($seller_user_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
    {
        //检查参数
        $seller_user_id = intval($seller_user_id);

        //整理查询条件
        $sql_where = '1 ';

        if( $seller_user_id>0 )
        {
            $sql_where .= ' AND seller_user_id=:x_seller_user_id';
            sqlSetParam($sql_where, 'x_seller_user_id', $seller_user_id);
        }

        if (strlen($where_str) > 0)
        {
            $sql_where .= ' AND '.$where_str;
        }

        //查询
        $this->set_scalping_order_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }

        return $this->findAll($sql_where, $limit, $order_by, $fields);
    }

    /**
     * @synopsis 取得待检查商家列表
     *
     * @param bool $b_select_count 是否返回计数
     * @param string $where_str 附加的条件语句
     * @param string $order_by 排序语句
     * @param string $limit 分页语句
     *
     * @returns array 商家ID数组
     */
    public function get_seller_list_for_check($b_select_count=false, $where_str='', $order_by='type_id,id', $limit='0,20', $fields = 'seller_user_id,type_id')
    {
        $sql_where = '1 ';
        if( strlen($where_str)>0 )
        {
            $sql_where .= ' AND '.$where_str;
        }

        $this->set_scalping_order_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }
        $sql = "
            SELECT 
                {$fields}
            FROM
                {$this->_db_name}.{$this->_tbl_name} 
            WHERE {$sql_where} 
            GROUP BY seller_user_id,type_id 
            ORDER BY {$order_by}
            LIMIT {$limit}
        ";
        $seller_list =  $this->findBysql($sql);

        $white_list = $this->get_scalping_white_list(false, '', '', '0,99999999', 'user_id');
        $white_user_id_arr = array_map('array_pop', $white_list);
        foreach( $seller_list as $key => $seller)
        {
            if( in_array($seller['seller_user_id'], $white_user_id_arr) )
            {
                unset($seller_list[$key]);
            }
        }
        return array_values($seller_list);
    }

    /**
     * @synopsis 添加刷单商家记录，检查结果
     *
     * @param int $seller_user_id 商家ID
     * @param string $rule_code
     * @param array $more_info = array(
     *       'rule_value' => ''//规则分值
     *       'check_date' => ''//检查日期
     *       'more_info' => array(),
     *       'add_time' => ''//添加时间
     *)
     * @returns int 数据库主键ID
     */
    public function add_scalping_check_log($seller_user_id, $type_id, $rule_code_m, $start_time, $end_time)
    {
        $seller_user_id = intval($seller_user_id);
        $rule_code_m = trim($rule_code_m);
        $type_id = intval($type_id);

        if( $seller_user_id<1 || strlen($rule_code_m)<1 )
        {
            return 0;
        }
        //必填值

        $sum_score = 0;
        foreach( explode(',', $rule_code_m) as $rule_code  )
        {
            $sum_score += $this->rules_list[$rule_code]['rule_score'];
        }

        $mall_seller_obj = POCO::singleton('pai_mall_seller_class');
        $seller_info =  $mall_seller_obj->get_seller_info($seller_user_id,2);

        $data = array(
            'seller_user_id' => $seller_user_id,
            'seller_type_id' => $seller_info['seller_data']['profile'][0]['type_id'],
            'rule_type_id' => $type_id,
            'rule_code_m' => $rule_code_m,
            'rule_score_s' => $sum_score,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'add_time' => time(),
        );

        $this->set_scalping_check_log_tbl();
        return $this->insert($data, 'REPLACE');
    }

    /**
     * @synopsis 获取检查记录
     *
     * @param int $seller_user_id 商家ID
     * @param bool $b_select_count 是否返回计数
     * @param string $where_str 附加的条件语句
     * @param string $order_by 排序语句
     * @param string $limit 分页语句
     * @param string $fields 字段
     *
     * @returns array 记录列表
     */
    public function get_scalping_check_log_list($seller_user_id = 0, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
    {
        $seller_user_id = intval($seller_user_id);
        $sql_where = '1 ';
        if( $seller_user_id>0 )
        {
            $sql_where .= " AND seller_user_id={$seller_user_id}";
        }
        if( strlen($where_str)>0 )
        {
            $sql_where .= ' AND '.$where_str;
        }

        $this->set_scalping_check_log_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }
        return $this->findAll($sql_where, $limit, $order_by, $fields);
    }

    /**
     * @synopsis 添加商家到刷单列表，自动
     *
     * @param $user_id int 用户ID
     * @param $data array 用户数据，eg. $data = array(    'remark' => 'test',
     *                                                    )
     *
     * @returns 影响行数 0失败，>0成功
     */
    public function add_scalping_seller_auto($user_id, $data)
    {
        $user_id = intval($user_id);
        if ($user_id<1 || !is_array($data) || count($data)<1) {
            return 0;
        }

        $cur_time = time();

        $seller_info = $this->get_scalping_seller_info($user_id, '');

        if (count($seller_info)>0 && is_array($seller_info)) {
            $data['status'] = 0;
            $data['status_time'] = 0;
            $data['add_by'] = 'system';
            return $this->edit_scalping_seller($user_id, $data);
        }

        $data['user_id'] = $user_id;
        $data['color'] = '#BF8F00';//自动检查命中的商家黄色标志
        $data['status'] = 0;
        $data['status_time'] = 0;
        $data['add_by'] = 'system';
        $data['change_time'] = $cur_time;
        $data['add_time'] = $cur_time;

        $this->set_scalping_seller_tbl();
        return $this->insert($data, "IGNORE");
    }

    /**
     * @synopsis 添加商家到刷单列表，手动
     *
     * @param $user_id int 用户ID
     * @param $data array 用户数据，eg. $data = array(    'remark' => 'test',
     *                                                    'color' => '#FFF900',
     *                                                    )
     *
     * @returns 影响行数 0失败，>0成功, -1 参数错误
     */
    public function add_scalping_seller($user_id, $data)
    {
        $user_id = intval($user_id);
        if ($user_id<1 || !is_array($data) || count($data)<1) {
            return -1;
        }

        $cur_time = time();
        $seller_info = $this->get_scalping_seller_info($user_id, '');
        if ( count($seller_info)>0 && is_array($seller_info) ) {
            $data['add_by'] = 'manual';
            return $this->edit_scalping_seller($user_id, $data);
        }

        $data['user_id'] = $user_id;
        $data['add_by'] = 'manual';
        $data['change_time'] = $cur_time;
        $data['add_time'] = $cur_time;
        if ($status==8) {
            $data['color'] = '#3A9001';//添加已确认的刷单商家为绿色
        }
        if( $status==0) {
            $data['color'] = '#FF0000';//手动添加待确认的为红色
        }

        $this->set_scalping_seller_tbl();
        return $this->insert($data, "IGNORE");
    }

    /**
     * @synopsis 改变商家标志
     *
     * @param $user_id int 用户ID
     *
     * @returns 影响行数 0失败，>0成功
     */
    public function edit_scalping_seller($user_id, $data)
    {
        $user_id = intval($user_id);
        $status = intval($data['status']);
        $add_by = trim($data['add_by']);
        if( $user_id<1 ||
            !is_array($data) ||
            count($data)<1 ||
            !in_array($status, array(0, 7, 8))||
            !in_array($add_by, array('system', 'manual')) )
        {
            return 0;
        }


        $seller_info = $this->get_scalping_seller_info($user_id, '');
        if (count($seller_info)>0 && is_array($seller_info)) {
            $remark = "{$seller_info['remark']}\r\n{$data['remark']}";//追加remark
        }
        $color = '#BF8F00';//自动检查命中的商家黄色标志
        if( $add_by=='manual' ) {
            $color = '#FF0000';//手动添加待确认的为红色
        }
        if ($status==7) {
            $color = '#000000';
        }
        if ($status==8) {
            $color = '#3A9001';//添加已确认的刷单商家为绿色
        }

        unset($data['user_id']);
        $data['change_time'] = time();
        $data['remark'] = $remark;
        $data['color'] = $color;

        $where = "user_id={$user_id}";
        $this->set_scalping_seller_tbl();
        return $this->update($data, $where);
    }

    /**
     * @synopsis 获取刷单的商家列表
     *
     * @param bool $b_select_count 是否返回计数
     * @param string $where_str 附加的条件语句
     * @param string $order_by 排序语句
     * @param string $limit 分页语句
     * @param string $fields 字段
     *
     * @returns array 商家ID数组
     */
    public function get_scalping_seller_list($b_select_count=false, $where_str='', $order_by='', $limit='0,99999999', $fields = '*')
    {
        $sql_where = 'status in (0,8) ';
        if( strlen($where_str)>0 )
        {
            $sql_where .= ' AND '.$where_str;
        }

        $this->set_scalping_seller_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }
        $seller_list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $seller_list;
    }

    /**
     * @synopsis 获取商家的刷单详情
     *
     * @param int $user_id 用户ID
     *
     * @returns array 记录详情
     */
    public function get_scalping_seller_info($user_id, $where = 'status IN (0,8)')
    {
        $user_id = intval($user_id);
        if ($user_id<1) {
            return array();
        }

        $this->set_scalping_seller_tbl();
        $where_str = "user_id={$user_id}";
        if( strlen($where)>1 )
        {
            $where_str  .= " AND {$where}";
        }
        return $this->find($where_str);
    }

    /**
     * @synopsis 获取刷单记录历史, 不包括待处理的商家
     *
     * @param int $seller_user_id 商家ID
     * @param bool $b_select_count 是否返回计数
     * @param string $where_str 附加的条件语句
     * @param string $order_by 排序语句
     * @param string $limit 分页语句
     * @param string $fields 字段
     *
     * @returns array 记录列表
     */
    public function get_scalping_seller_history($seller_user_id, $b_select_count=false, $where_str='', $order_by='', $limit='0,20', $fields = '*')
    {
        $seller_user_id = intval($seller_user_id);
        $sql_where = 'status = 8 ';
        if( $seller_user_id>0 )
        {
            $sql_where .= " AND user_id={$seller_user_id}";
        }
        if( strlen($where_str)>0 )
        {
            $sql_where .= ' AND '.$where_str;
        }

        $this->set_scalping_seller_tbl();
        if( $b_select_count )
        {
            return $this->findCount($sql_where);
        }
        return $this->findAll($sql_where, $limit, $order_by, $fields);
    }

    /**
     * @synopsis 检查规则
     *
     * @param string $rule_code 规则代码
     * @param int $seller_user_id 商家ID
     * @param int $start_time 开始时间
     * @param int $end_time 结束时间
     *
     * @returns array 带提示的结果
     */
    public function check_rule($rule_code, $seller_user_id, $type_id, $start_time, $end_time)
    {
        $rule_code = trim($rule_code);
        $seller_user_id = intval($seller_user_id);
        $type_id = intval($type_id);
        $start_time = intval($start_time);
        $end_time = intval($end_time);
        $rst = array(
            'code'=>0,
            'message'=>'',
            'rule_type_id' => -1, 
            'rule_code' => $rule_code,
            'is_scalping' => false,
            'more_info' => array(),
        );
        if( strlen($rule_code)<1||$seller_user_id<1||$start_time<1||$end_time<1||$end_time<$start_time )
        {
            $rst['code'] = -1;
            $rst['message'] = '参数错误';
            return $rst;
        }
        if( !array_key_exists($rule_code, $this->rules_list) )
        {
            $rst['code'] = -2;
            $rst['message'] = '规则不存在';
            return $rst;
        }

        $rule_rst = call_user_func(array($this, 'rule_'.$rule_code), $seller_user_id, $type_id, $start_time, $end_time);
        $rst['is_scalping'] = $rule_rst['is_scalping'];
        $rst['more_info'] = $rule_rst['more_info'];
        $rst['rule_type_id'] = $rule_rst['rule_type_id'];

        $rst['code'] = 1;
        $rst['message'] = '检查完成';
        return $rst;
    }

    /**
     * @synopsis 检查商家商家是否在时段内刷单，所有规则
     *
     * @param int $seller_user_id 商家ID
     * @param int $start_time 检查时段开始
     * @param int $end_time 检查时段结束
     *
     * @returns array 结果
     */
    public function check_all_rule($seller_user_id, $start_time, $end_time, $type_id)
    {
        $cur_time = time();

        $seller_user_id = intval($seller_user_id);
        $start_time = intval($start_time);
        $end_time = intval($end_time);
        $type_id = intval($type_id);
        $rst = array(
            'code'=>0,
            'message'=>'',
            'rule_type_id'=>-1,
            'seller_user_id' => $seller_user_id,
            'is_scalping' => false, 
            'rule_code_m' => '', 
        );
        if( $seller_user_id<1||$start_time<1||$end_time<1||$end_time<$start_time )
        {
            $rst['code'] = -1;
            $rst['message'] = '参数错误';
            return $rst;
        }

        $rules_code_arr = array_keys($this->rules_list);
        $check_rst_arr = array();
        foreach($this->rules_list as $key => $rule)
        {
            if($rule['status']==1)
            {
                $check_rst = $this->check_rule($key, $seller_user_id, $type_id, $start_time, $end_time);
                if( $check_rst['is_scalping'] )
                {
                    $check_rst_arr[] = $key;
                    $rst['rule_type_id'] = $check_rst['rule_type_id'];
                }
            }
        }

        if( count($check_rst_arr)>0 )//添加刷单检查记录
        {
            $rule_code_m = implode(',',$check_rst_arr);
            $rst['is_scalping'] = true;
            $rst['rule_code_m'] = $rule_code_m;
        }

        $rst['code'] = 1;
        $rst['message'] = '检查完成';
        return $rst;
    }



    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A1($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_user_id, goods_id分组计数，存在数量大于2分组时，说明存在刷单
            $sql = "
                SELECT `buyer_user_id`, `goods_id`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY `buyer_user_id`, `goods_id`
                HAVING COUNT(1) > 2
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_user_id`={$count_info['buyer_user_id']} AND `goods_id`={$count_info['goods_id']} AND type_id={$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        //按seller_user_id, buyer_user_id, goods_id分组计数，存在数量大于1分组时，说明存在刷单
        $result['rule_type_id'] = 0;
        $sql = "
            SELECT `buyer_user_id`, `goods_id`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_user_id`, `goods_id`
            HAVING COUNT(1) > 1
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_user_id`={$count_info['buyer_user_id']} AND `goods_id`={$count_info['goods_id']}";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A2($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_alipay_account, goods_id分组计数，存在数量大于2分组时，说明存在刷单
            $sql = "
                SELECT `buyer_alipay_account`, `goods_id`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `buyer_alipay_account`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY `buyer_alipay_account`, `goods_id`
                HAVING COUNT(1) > 2
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_alipay_account`='{$count_info['buyer_alipay_account']}' AND `goods_id`={$count_info['goods_id']} AND type_id = {$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_alipay_account,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_alipay_account, goods_id分组计数，存在数量大于1分组时，说明存在刷单
        $sql = "
            SELECT `buyer_alipay_account`, `goods_id`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `buyer_alipay_account`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_alipay_account`, `goods_id`
            HAVING COUNT(1) > 1
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_alipay_account`='{$count_info['buyer_alipay_account']}' AND `goods_id`={$count_info['goods_id']}";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_alipay_account,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A3($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_weixin_openid, goods_id分组计数，存在数量大于2分组时，说明存在刷单
            $sql = "
                SELECT `buyer_weixin_openid`, `goods_id`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE  `buyer_weixin_openid`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY `buyer_weixin_openid`, `goods_id`
                HAVING COUNT(1) > 2
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_weixin_openid`='{$count_info['buyer_weixin_openid']}' AND `goods_id`={$count_info['goods_id']} AND type_id = {$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_weixin_openid,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_weixin_openid, goods_id分组计数，存在数量大于1分组时，说明存在刷单
        $sql = "
            SELECT `buyer_weixin_openid`, `goods_id`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE  `buyer_weixin_openid`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_weixin_openid`, `goods_id`
            HAVING COUNT(1) > 1
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_weixin_openid`='{$count_info['buyer_weixin_openid']}' AND `goods_id`={$count_info['goods_id']}";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_weixin_openid,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A4($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_user_id分组计数，存在数量大于3分组时，说明存在刷单
            $sql = "
                SELECT `buyer_user_id`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY `buyer_user_id`
                HAVING COUNT(1) > 3
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_user_id`={$count_info['buyer_user_id']} AND type_id = {$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_user_id分组计数，存在数量大于2分组时，说明存在刷单
        $sql = "
            SELECT `buyer_user_id`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_user_id`
            HAVING COUNT(1) > 2
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_user_id`={$count_info['buyer_user_id']}";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A5($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_alipay_account分组计数，存在数量大于3分组时，说明存在刷单
            $sql = "
                SELECT `buyer_alipay_account`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `buyer_alipay_account`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id={$type_id}
                GROUP BY `buyer_alipay_account`
                HAVING COUNT(1) > 3
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_alipay_account`='{$count_info['buyer_alipay_account']}' AND type_id={$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_alipay_account,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_alipay_account分组计数，存在数量大于2分组时，说明存在刷单
        $sql = "
            SELECT `buyer_alipay_account`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `buyer_alipay_account`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_alipay_account`
            HAVING COUNT(1) > 2
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_alipay_account`='{$count_info['buyer_alipay_account']}'";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_alipay_account,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A6($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_weixin_openid分组计数，存在数量大于3分组时，说明存在刷单
            $sql = "
                SELECT `buyer_weixin_openid`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `buyer_weixin_openid`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY `buyer_weixin_openid`
                HAVING COUNT(1) > 3
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_weixin_openid`='{$count_info['buyer_weixin_openid']}' AND type_id={$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_weixin_openid,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_weixin_openid分组计数，存在数量大于2分组时，说明存在刷单
        $sql = "
            SELECT `buyer_weixin_openid`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `buyer_weixin_openid`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY `buyer_weixin_openid`
            HAVING COUNT(1) > 2
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND `buyer_weixin_openid`='{$count_info['buyer_weixin_openid']}'";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_weixin_openid,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A7($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_cellphone的前7位，goods_id分组计数，存在数量大于2分组时，说明存在刷单
            $sql = "
                SELECT left(`buyer_cellphone`, 7) as buyer_cellphone_part, `goods_id`, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `buyer_cellphone`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY left(`buyer_cellphone`, 7), goods_id
                HAVING COUNT(1) > 2
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND left(`buyer_cellphone`,7)='{$count_info['buyer_cellphone_part']}' AND `goods_id`={$count_info['goods_id']} AND type_id={$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_cellphone,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;

        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_cellphone的前7位，goods_id分组计数，存在数量大于1分组时，说明存在刷单
        $sql = "
            SELECT left(`buyer_cellphone`, 7) as buyer_cellphone_part, `goods_id`, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `buyer_cellphone`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY left(`buyer_cellphone`, 7), goods_id
            HAVING COUNT(1) > 1
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND left(`buyer_cellphone`,7)='{$count_info['buyer_cellphone_part']}' AND `goods_id`={$count_info['goods_id']}";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_cellphone,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_A8($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );
        $this->set_scalping_order_tbl();
        if($type_id==31)
        {
            //按seller_user_id, buyer_cellphone的前7位分组计数，存在数量大于3分组时，说明存在刷单
            $sql = "
                SELECT left(`buyer_cellphone`, 7) as buyer_cellphone_part, count(1) as c
                FROM {$this->getDBName()}.`{$this->getTableName()}`
                WHERE `buyer_cellphone`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND type_id = {$type_id}
                GROUP BY left(`buyer_cellphone`, 7)
                HAVING COUNT(1) > 3
                ";
            $count_list = db_simple_getdata($sql, false, $this->getServerId());

            if( count($count_list)>0 )
            {
                $result['is_scalping'] = true;

                $order_list = array();
                foreach( $count_list as $count_info )
                {
                    $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND left(`buyer_cellphone`,7)='{$count_info['buyer_cellphone_part']}' AND type_id={$type_id}";
                    $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_cellphone,goods_id', '0,99999999');
                    $order_list = array_merge($order_list,$order_list_temp);
                }
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        //按seller_user_id, buyer_cellphone的前7位分组计数，存在数量大于2分组时，说明存在刷单
        $sql = "
            SELECT left(`buyer_cellphone`, 7) as buyer_cellphone_part, count(1) as c
            FROM {$this->getDBName()}.`{$this->getTableName()}`
            WHERE `buyer_cellphone`<>'' AND `seller_user_id` = {$seller_user_id} AND `sign_time` >= {$start_time} AND `sign_time` <= {$end_time}
            GROUP BY left(`buyer_cellphone`, 7)
            HAVING COUNT(1) > 2
            ";
        $count_list = db_simple_getdata($sql, false, $this->getServerId());

        if( count($count_list)>0 )
        {
            $result['is_scalping'] = true;

            $order_list = array();
            foreach( $count_list as $count_info )
            {
                $where_str = "`sign_time` >= {$start_time} AND `sign_time` <= {$end_time} AND left(`buyer_cellphone`,7)='{$count_info['buyer_cellphone_part']}'";
                $order_list_temp = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_cellphone,goods_id', '0,99999999');
                $order_list = array_merge($order_list,$order_list_temp);
            }
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_B1($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );

        if($type_id==31)
        {
            $start_time = $start_time-30*60;//前移半小时
            $end_time = $end_time+30*60;//后移半小时
            $where_str = "pay_time >= {$start_time} AND pay_time <= {$end_time}";

            $order_list = $this->get_order_list($seller_user_id, false, $where_str, 'pay_time ASC', '0,99999999');

            $quantity = 0;
            $multi_order_id = '';
            foreach( $order_list as $key => $order_info)
            {
                if( $key+1>=count($order_list) )
                {
                    break;
                }

                $order_info_next = $order_list[$key+1];
                $time_separate = $order_info_next['pay_time'] - $order_info['pay_time'];
                if( $time_separate <= 30*60 )//时间间隔小于半小时
                {
                    $multi_order_id .= "{$order_info['order_id']},{$order_info_next['order_id']},";
                    $quantity++;
                }
            }

            if( $quantity>0 )//存在时间间隔小于半小时的订单
            {
                $result['is_scalping'] = true;

                $multi_order_id = rtrim($multi_order_id, ',');
                $where_str = " `order_id` IN ({$multi_order_id})";
                $order_list = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
                $result['more_info'] = $order_list;
            }
            return $result;
        }
        $result['rule_type_id'] = 0;
        $start_time = $start_time-30*60;//前移半小时
        $end_time = $end_time+30*60;//后移半小时
        $where_str = "sign_by<>'sys' AND sign_time >= {$start_time} AND sign_time <= {$end_time}";

        $order_list = $this->get_order_list($seller_user_id, false, $where_str, 'sign_time ASC', '0,99999999');

        $quantity = 0;
        $multi_order_id = '';
        foreach( $order_list as $key => $order_info)
        {
            if( $key+1>=count($order_list) )
            {
                break;
            }

            $order_info_next = $order_list[$key+1];
            $time_separate = $order_info_next['sign_time'] - $order_info['sign_time'];
            if( $time_separate <= 30*60 )//时间间隔小于半小时
            {
                $multi_order_id .= "{$order_info['order_id']},{$order_info_next['order_id']},";
                $quantity++;
            }
        }

        if( $quantity>0 )//存在时间间隔小于半小时的订单
        {
            $result['is_scalping'] = true;

            $multi_order_id = rtrim($multi_order_id, ',');
            $where_str = " `order_id` IN ({$multi_order_id})";
            $order_list = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
            $result['more_info'] = $order_list;
        }
        return $result;
    }

    /**
     * @synopsis 规则，见顶部配置
     *
     * @param int $seller_user_id
     * @param int $start_time
     * @param int $end_time
     *
     * @returns array $result = array(
     * 'seller_user_id'=>$seller_user_id,
     * 'is_scalping'=>false,
     * 'more_info'=>array(),
     * );
     */
    private function rule_B2($seller_user_id, $type_id, $start_time, $end_time)
    {
        $type_id = intval($type_id); 
        $result = array(
            'seller_user_id'=>$seller_user_id,
            'rule_type_id' => $type_id, 
            'is_scalping'=>false,
            'more_info'=>array(),
        );

        if($type_id==31)
        {
            return $result;
        }
        $result['rule_type_id'] = 0;
        $where_str = "sign_by<>'sys' AND sign_time >= {$start_time} AND sign_time <= {$end_time}";
        $order_list = $this->get_order_list($seller_user_id, false, $where_str, '', '0,99999999');

        $quantity = 0;
        $multi_order_id = '';
        foreach( $order_list as $order_info)
        {
            $hour = date('H', $order_info['sign_time']);
            if( $hour<6||$hour>=22 )//不在6到22之间
            {
                $multi_order_id .= $order_info['order_id'].",";
                $quantity++;
            }
        }

        if( $quantity>0 )
        {
            $result['is_scalping'] = true;

            $multi_order_id = rtrim($multi_order_id, ',');
            $where_str = " `order_id` IN ({$multi_order_id})";
            $order_list = $this->get_order_list($seller_user_id, false, $where_str, 'buyer_user_id,goods_id', '0,99999999');
            $result['more_info'] = $order_list;
        }
        return $result;
    }
}
