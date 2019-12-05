<?php

/**
 * 手机校验码
 * @author Henry
 * @copyright 2014-09-11
 */
class phone_verify_code_class extends POCO_TDG
{
    # 校验码长度
    private $verify_code_length = 6;
    # 校验码更换
    private $verify_code_change = 0;
    # 限制校验次数
    private $verify_count_limit = 10;
    # 短信发送间隔，秒
    private $send_interval_seconds = 50;

    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId(false);
        $this->setDBName('verify_code_db');
        $this->set_phone_verify_code_tbl();
    }

    /**
     * 切换表
     */
    private function set_phone_verify_code_tbl()
    {
        $this->setTableName('phone_verify_code_tbl');
    }

    /**
     * 获取校验码长度
     * @return int
     */
    public function get_verify_code_length()
    {
        return $this->verify_code_length;
    }

    /**
     * 设置校验码长度
     * @param int $length 默认6
     * @return boolean
     */
    public function set_verify_code_length($length)
    {
        $length = intval($length);
        if ($length < 1) {
            return false;
        }
        $this->verify_code_length = $length;
        return true;
    }

    /**
     * 获取校验码更换
     * @return int
     */
    public function get_verify_code_change()
    {
        return $this->verify_code_change;
    }

    /**
     * 设置校验码更换
     * 0默认，有效期内不换校验码；有效期外换校验码
     * 1强制换校验码
     * 2强制不换校验码
     * @param int $change
     * @return boolean
     */
    public function set_verify_code_change($change)
    {
        $change = intval($change);
        if (!in_array($change, array(0, 1, 2))) {
            return false;
        }
        $this->verify_code_change = $change;
        return true;
    }

    /**
     * 新增
     * @param array $data
     * @return boolean
     */
    private function add_verify_code($data)
    {
        if (empty($data)) {
            trace('data empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return false;
        }
        $this->set_phone_verify_code_tbl();
        $this->insert($data);
        return true;
    }

    /**
     * 修改
     * @param array $data
     * @param string $phone
     * @param string $group_key
     * @return array
     */
    private function update_verify_code($data, $phone, $group_key)
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        if (!is_array($data) || empty($data) || strlen($phone) < 1 || strlen($group_key) < 1) {
            trace('params error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return false;
        }

        $where_str = 'phone=:x_phone AND group_key=:x_group_key';
        sqlSetParam($where_str, 'x_phone', $phone);
        sqlSetParam($where_str, 'x_group_key', $group_key);

        $this->set_phone_verify_code_tbl();
        $this->update($data, $where_str);
        return true;
    }

    /**
     * 替换
     * @param array $data
     * @return boolean
     */
    private function replace_verify_code($data)
    {
        if (empty($data)) {
            trace('data empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return false;
        }
        $this->set_phone_verify_code_tbl();
        $this->insert($data, 'REPLACE');
        return true;
    }

    /**
     * 累加
     * @param string $phone
     * @param string $group_key
     * @param int $incr
     * @return boolean
     */
    private function incr_verify_count($phone, $group_key, $incr = 1)
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        if (strlen($phone) < 1 || strlen($group_key) < 1) {
            trace('params error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return false;
        }

        $where_str = 'phone=:x_phone AND group_key=:x_group_key';
        sqlSetParam($where_str, 'x_phone', $phone);
        sqlSetParam($where_str, 'x_group_key', $group_key);

        $this->set_phone_verify_code_tbl();
        $this->incrField($where_str, 'verify_count', $incr);
        return true;
    }

    /**
     * 删除
     * @param string $phone
     * @param string $group_key
     * @return boolean
     */
    public function del_verify_code($phone, $group_key)
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        if (strlen($phone) < 1 || strlen($group_key) < 1) {
            trace('params error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return false;
        }

        $where_str = 'phone=:x_phone AND group_key=:x_group_key';
        sqlSetParam($where_str, 'x_phone', $phone);
        sqlSetParam($where_str, 'x_group_key', $group_key);

        $this->set_phone_verify_code_tbl();
        $this->delete($where_str);

        return true;
    }

    /**
     * 清除过期超过7天的
     * @return boolean
     */
    private function clean_verify_code()
    {
        $clean_time = time() - 7 * 24 * 3600;
        $where_str = "end_time<{$clean_time}";

        $this->set_phone_verify_code_tbl();
        $this->delete($where_str);

        return true;
    }

    /**
     * 获取
     * @param string $phone
     * @param string $group_key
     * @return array
     */
    public function get_verify_code_info($phone, $group_key)
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        if (strlen($phone) < 1 || strlen($group_key) < 1) {
            trace('params error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return array();
        }

        $where_str = 'phone=:x_phone AND group_key=:x_group_key';
        sqlSetParam($where_str, 'x_phone', $phone);
        sqlSetParam($where_str, 'x_group_key', $group_key);

        $this->set_phone_verify_code_tbl();
        return $this->find($where_str);
    }

    /**
     * 生成校验码
     * @param int $length
     * @return string
     */
    private function generate_verify_code($length)
    {
        $verify_code = '';
        $length = intval($length);
        if ($length < 1) {
            return $verify_code;
        }
        $pattern = '4802971653';
        for ($i = 0; $i < $length; $i++) {
            $verify_code .= substr($pattern, mt_rand(0, 9), 1);
        }
        return $verify_code;
    }

    /**
     * 创建校验码
     * @param string $phone
     * @param string $group_key
     * @param int $seconds
     * @param array $more_info
     * @return array
     */
    private function create_verify_code($phone, $group_key, $seconds = 600, $more_info = array())
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        $seconds = intval($seconds);
        if (strlen($phone) < 1) {
            trace('phone error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return array();
        }
        if (strlen($group_key) < 1) {
            trace('group_key empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return array();
        }
        if ($seconds < 1) {
            trace('seconds empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);
            return array();
        }

        $cur_time = time();

        $verify_code = '';
        $verify_code_time = 0;
        $verify_count = 0;
        $send_count = 1;
        $begin_time = $cur_time;
        $end_time = $begin_time + $seconds;
        $change_verify_code = true;

        $info = $this->get_verify_code_info($phone, $group_key);    //获取之前记录
        if (!empty($info) && $info['begin_time'] <= $cur_time && $cur_time <= $info['end_time']) {
            //有效期内
            switch ($this->verify_code_change) {
                case 1:
                    //强制换校验码
                    $change_verify_code = true;
                    break;
                case 2:
                    //强制不换校验码
                    $change_verify_code = false;
                    break;
                default:
                    if ($cur_time > $info['verify_code_time']) {
                        //无效，换校验码
                        $change_verify_code = true;
                    } else {
                        //有效，不换校验码
                        $change_verify_code = false;
                    }
                    break;
            }

            if (!$change_verify_code) {
                $verify_code = trim($info['verify_code']);
                $verify_code_time = intval($info['verify_code_time']);
                $verify_count = intval($info['verify_count']);    //保留原来的校验次数
            }
            $send_count = intval($info['send_count']) + 1;    //原来的发送次数加1
        }

        //生成校验码
        if ($change_verify_code) {
            $verify_code = $this->generate_verify_code($this->verify_code_length);
            $verify_code_time = $cur_time + 300;    //5分钟内保留同一个
        }

        $data = array(
            'phone' => $phone,
            'group_key' => $group_key,
            'seconds' => $seconds,
            'verify_code' => $verify_code,
            'verify_code_time' => $verify_code_time,
            'verify_count' => $verify_count,
            'send_count' => $send_count,
            'begin_time' => $begin_time,
            'end_time' => $end_time,
        );
        $this->replace_verify_code($data);

        return $data;
    }

    /**
     * 创建并发送校验码
     * @param string $phone 手机号码
     * @param string $group_key 分组，G_PAI_WITHDRAW用于约拍的提现校验
     * @param int $seconds 有效秒数，默认600秒
     * @param string $sms_tpl 短信内容模板（必须在SP报备），必须包含标签{verify_code}
     * @param array $more_info
     * @param int $product_type 默认0，10微网通知类，11微网验证码类，12微网营销类，16速码验证码类，18百悟验证码类
     * @return array
     * @tutorial
     * $product_type，通道11与通道16，在重复发送校验码时会自动互相切换。
     */
    public function send_verify_code($phone, $group_key, $sms_tpl, $seconds = 600, $more_info = array(), $product_type = 0)
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        $sms_tpl = trim($sms_tpl);
        $seconds = intval($seconds);
        $product_type = intval($product_type);
        if (strlen($phone) < 1 || !preg_match('/^1\d{10}$/isU', $phone)) {
            trace('phone error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -1;
            $result['message'] = '手机号码为空';
            $result['verify_code'] = '';
            return $result;
        }
        if (strlen($group_key) < 1) {
            trace('group_key empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -2;
            $result['message'] = '分组为空';
            $result['verify_code'] = '';
            return $result;
        }
        if (!preg_match('/\{\s*verify_code\s*\}/isU', $sms_tpl)) {
            trace('sms_tpl error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -3;
            $result['message'] = '短信内容模板错误';
            $result['verify_code'] = '';
            return $result;
        }
        if ($seconds < 1) {
            trace('seconds empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -4;
            $result['message'] = '有效秒数为空';
            $result['verify_code'] = '';
            return $result;
        }

        $cur_time = time();

        //检查发送频率
        $info = $this->get_verify_code_info($phone, $group_key);
        if (!empty($info) && ($info['begin_time'] + $this->send_interval_seconds) > $cur_time) {
            trace('too frequently', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -5;
            $result['message'] = '发送过于频繁';
            $result['verify_code'] = '';
            return $result;
        }

        //创建校验码
        $verify_info = $this->create_verify_code($phone, $group_key, $seconds, $more_info);
        $verify_code = trim($verify_info['verify_code']);
        if (empty($verify_info) || strlen($verify_code) < 1) {
            trace('create_verify_code fail', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -6;
            $result['message'] = '创建校验码失败';
            $result['verify_code'] = '';
            return $result;
        }
        $content = preg_replace('/\{\s*verify_code\s*\}/isU', $verify_code, $sms_tpl);

        //自动切换短信通道，仅限通道11与通道16
        if (in_array($product_type, array(11, 16), true)) {
            $send_count = intval($verify_info['send_count']);
            if ($send_count < 1) $send_count = 1;
            if ($send_count % 2 == 0) {
                $product_type = ($product_type == 11 ? 16 : 11);
            }

            //注册、修改密码验证码，30条/IP/3天
            if (strpos($group_key, 'REG_VERIFY') !== false || strpos($group_key, 'PASSWORD_VERIFY') !== false) {
                $today_count_max = 30;
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'yue_pai') !== false) {
                    $today_count_max = 300;
                }
                global $_INPUT;
                $cache_key = 'G_VERIFY_CODE_IP_SEND_VERIFY_CODE_' . trim($_INPUT['IP_ADDRESS']);
                //获取cache
                $cache_data = POCO::getCache($cache_key);
                $today_count = intval($cache_data['today_count']);
                if( $today_count >= $today_count_max ) {
                    if (defined('G_YUEYUE_ROOT_PATH')) {
                        //临时日志  http://yp.yueus.com/logs/201510/12_send_verify_code.txt
                        pai_log_class::add_log(func_get_args(), 'ip_verify_code', 'send_verify_code');
                    }
                    $result['code'] = -5;
                    $result['message'] = '发送次数超过限制';
                    $result['verify_code'] = $verify_code;
                    return $result;
                }

                //保存cache
                $today_count++;
                $life_time = strtotime('+2 days', strtotime(date('Y-m-d 23:59:59'))) - time();
                POCO::setCache($cache_key, array('today_count' => $today_count), array('life_time' => $life_time));
            }

            //百悟通道：由于最近存在恶意发送注册验证码，导致通道被投诉关闭。所以每个号码每天下发验证码限制不要超过5条 2015-03-25
            //百悟通道：建议所有通道发送的总条数，限制不要超过5条 2015-08-13
            if (strpos($group_key, 'REG_VERIFY') !== false || strpos($group_key, 'PASSWORD_VERIFY') !== false) {
                $today_count_max = 3;
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'yue_pai') !== false) {
                    $today_count_max = 5;
                }
                $cache_key = 'G_VERIFY_CODE_PHONE_SEND_VERIFY_CODE_' . $phone;
                //获取cache
                $cache_data = POCO::getCache($cache_key);
                $today_count = intval($cache_data['today_count']);
                if( $today_count >= $today_count_max ) {
                    if (defined('G_YUEYUE_ROOT_PATH')) {
                        //临时日志   http://yp.yueus.com/logs/201510/12_send_verify_code.txt
                        pai_log_class::add_log(func_get_args(), 'phone_verify_code', 'send_verify_code');
                    }
                    $result['code'] = -5;
                    $result['message'] = '发送次数超过限制';
                    $result['verify_code'] = $verify_code;
                    return $result;
                }

                //保存cache
                $today_count++;
                $life_time = strtotime(date('Y-m-d 23:59:59')) - time();
                POCO::setCache($cache_key, array('today_count' => $today_count), array('life_time' => $life_time));
            }
        }

        //发送短信
        include_once(G_YUEYUE_VERIFY_CODE_ROOT_PATH . '/../sms_service/poco_app_common.inc.php');
        $sms_obj = POCO::singleton('class_sms_v2');
        $ret = $sms_obj->save_and_send_sms($phone, $content, $product_type);
        if (!$ret) {
            trace('save_and_send_sms fail', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -7;
            $result['message'] = '发送短信失败';
            $result['verify_code'] = $verify_code;
            return $result;
        }

        $result['code'] = 1;
        $result['message'] = '成功';
        $result['verify_code'] = $verify_code;
        return $result;
    }

    /**
     * 校验
     * @param string $phone
     * @param string $group_key
     * @param string $verify_code
     * @param boolean $b_del_verify_code 校验成功，是否删除数据库中的校验码
     * @return array
     */
    public function check_verify_code($phone, $group_key, $verify_code, $b_del_verify_code = true)
    {
        $result = array();

        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);
        $verify_code = trim($verify_code);
        if (strlen($phone) < 1) {
            trace('phone empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -1;
            $result['message'] = '手机号码为空';
            return $result;
        }
        if (strlen($group_key) < 1) {
            trace('group_key empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -2;
            $result['message'] = '分组为空';
            return $result;
        }
        if (strlen($verify_code) < 1) {
            trace('verify_code empty', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -3;
            $result['message'] = '校验码为空';
            return $result;
        }

        //获取
        $info = $this->get_verify_code_info($phone, $group_key);
        if (empty($info)) {
            trace('verify_code_info not exist', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -4;
            $result['message'] = '检验码不存在';
            return $result;
        }

        //检查校验次数
        if ($this->verify_count_limit < ($info['verify_count'] + 1)) {
            trace('verify_count error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -5;
            $result['message'] = '检验次数超过' . $this->verify_count_limit;
            return $result;
        }

        //检查校验码
        if ($verify_code != $info['verify_code']) {
            //累加校验次数
            $this->incr_verify_count($phone, $group_key);

            trace('verify_code error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -6;
            $result['message'] = '检验码错误';
            return $result;
        }

        //检查有效期
        $cur_time = time();
        if ($cur_time < $info['begin_time'] || $info['end_time'] < $cur_time) {
            trace('time error', 'file=' . basename(__FILE__) . ', line=' . __LINE__ . ', method=' . __METHOD__);

            $result['code'] = -7;
            $result['message'] = '检验码过期';
            return $result;
        }

        //删除校验码，以防重复校验
        if ($b_del_verify_code) {
            $this->del_verify_code($phone, $group_key);
        }

        //清除过期的
        if (rand(1, 10) == 1) {
            $this->clean_verify_code();
        }

        $result['code'] = 1;
        $result['message'] = '成功';
        return $result;
    }

    /**
     * 获取列表
     * @param string $phone
     * @param string $group_key
     * @param bool|false $b_select_count
     * @param string $where_str
     * @param string $order_by
     * @param string $limit
     * @param string $fields
     * @return array|int
     */
    public function get_verify_code_list($phone = '', $group_key = '', $b_select_count = false, $where_str = '', $order_by = '', $limit = '0,20', $fields = '*')
    {
        //检查参数
        $phone = trim($phone);
        $group_key = trim($group_key);

        //整理查询条件
        $sql_where = '';

        if (strlen($phone) > 0) {
            if (strlen($sql_where) > 0) $sql_where .= ' AND ';
            $sql_where .= 'phone=:x_phone';
            sqlSetParam($sql_where, 'x_phone', $phone);
        }

        if (strlen($group_key) > 0) {
            if (strlen($sql_where) > 0) $sql_where .= ' AND ';
            $sql_where .= 'group_key=:x_group_key';
            sqlSetParam($sql_where, 'x_group_key', $group_key);
        }

        if (strlen($where_str) > 0) {
            if (strlen($sql_where) > 0) $sql_where .= ' AND ';
            $sql_where .= $where_str;
        }

        //查询
        $this->set_phone_verify_code_tbl();
        if ($b_select_count) {
            return $this->findCount($sql_where);
        }

        $list = $this->findAll($sql_where, $limit, $order_by, $fields);
        return $list;
    }
}
