<?php
/*
 * 用户表操作类
 */
include_once("/disk/data/htdocs232/poco/pai/include/pai_login_session_class.inc.php");

class pai_user_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId(101);
        $this->setDBName('pai_db');
        $this->setTableName('pai_user_tbl');
    }

    /**
     * 注册创建帐号
     * @param $user_info_arr
     * @param string $err_msg
     * @return int
     * @throws App_Exception
     */
    public function create_mall_account($user_info_arr, &$err_msg = "")
    {
        $pwd = $user_info_arr ['pwd'];
        $pwd_hash = md5($pwd);
        $cellphone = ( int )$user_info_arr ['cellphone'];
        $nickname = $user_info_arr ['nickname'];
        $sex = $user_info_arr ['sex'];
        $role = "cameraman";
        $birthday = $user_info_arr ['birthday'];

        if (empty ($cellphone)) {
            $err_msg = '手机号码不能为空';
            return -1;
        }

        $user_info = $this->get_user_by_phone($cellphone);
        //检查用户是否预先导入的
        if ($user_info ['pwd_hash'] == 'poco_model_db') {
            $is_model_db = true;
        }

        if (!$is_model_db) {
            //绑定了就不能再绑
            $check_cellphone = $this->check_cellphone_exist($cellphone);
            if ($check_cellphone) {
                $err_msg = '手机号码已存在';
                return -2;
            }
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "密码长度错误";
            return -3;
        }

        if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
            $err_msg = "密码HASH错误 ";
            return -4;
        }

        $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
        $location_id = $current_locate_info['location_id'];

        if (empty($location_id)) {
            $location_id = 101029001;
        }

        if (strlen($location_id) == 6) {
            $location_id .= "001";
        }

        if (empty($nickname)) {
            $nickname = "手机用户_" . substr($cellphone, -4);
        }

        //预导入用户特殊处理
        if ($is_model_db) {
            $ret = $this->update_pwd_by_phone($cellphone, $pwd);
            if (!$ret) {
                $err_msg = "注册异常";
                return -6;
            }

            $user_id = $this->get_user_id_by_phone($cellphone);

            //注册要更新来源和加入时间
            $update_data['add_time'] = time();
            $update_data['reg_from'] = $user_info_arr ['reg_from'];
            $this->update_user_by_phone($update_data, $cellphone);
        } else {
            $insert_arr ["pwd_hash"] = $pwd_hash;
            $insert_arr ["cellphone"] = $cellphone;
            $insert_arr ["nickname"] = $nickname;
            $insert_arr ["sex"] = $sex;
            $insert_arr ["role"] = $role;
            $insert_arr ["location_id"] = $location_id;
            $insert_arr ["birthday"] = $birthday;
            $insert_arr ["add_time"] = time();
            $insert_arr ["reg_from"] = $user_info_arr ['reg_from'];
            $insert_arr ["reg_ip"] = ip2long(get_client_ip());

            $user_id = $this->add_user($insert_arr);
            if ($user_id < 1) {
                $err_msg = "注册异常";
                return -6;
            }

            $relate_poco_obj = POCO::singleton('pai_relate_poco_class');
            $poco_id = $relate_poco_obj->create_relate_poco_id($user_id, $nickname, $pwd);
            if ($poco_id < 1) {
                //删除已创建的用户ID
                $this->del_user($user_id);

                $err_msg = "POCO ID异常 ";
                return -5;
            }
        }


        //触发统计
        POCO::singleton( 'pai_pa_dt_register_class' )->add_register_log_by_user_id($user_id);

        //送劵
        $pai_trigger_obj = POCO::singleton('pai_trigger_class');
        $trigger_params = array('user_id' => $user_id);
        $pai_trigger_obj->user_reg_after($trigger_params);

        return $user_id;
    }

    /**
     * 创建帐号
     * @param $user_info_arr
     * @param string $err_msg
     * @return int
     * @throws App_Exception
     */
    public function create_account($user_info_arr, &$err_msg = "")
    {
        $pwd = $user_info_arr ['pwd'];
        $pwd_hash = md5($pwd);
        $cellphone = ( int )$user_info_arr ['cellphone'];
        $nickname = $user_info_arr ['nickname'];
        $sex = $user_info_arr ['sex'];
        $role = "cameraman";
        $birthday = $user_info_arr ['birthday'];

        if (empty ($cellphone)) {
            $err_msg = '手机号码不能为空';
            return -1;
        }

        $check_cellphone = $this->check_cellphone_exist($cellphone);
        if ($check_cellphone) {
            $err_msg = '手机号码已存在';
            return -2;
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "密码长度错误";
            return -3;
        }

        if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
            $err_msg = "密码HASH错误 ";
            return -4;
        }

        $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
        $location_id = $current_locate_info['location_id'];

        if (empty($location_id)) {
            $location_id = 101029001;
        }

        if (strlen($location_id) == 6) {
            $location_id .= "001";
        }

        if (empty($nickname)) {
            if ($role == 'model') {
                $nickname = "模特_" . substr($cellphone, -4);
            } else {
                $nickname = "摄影师_" . substr($cellphone, -4);
            }
        }

        $insert_arr ["pwd_hash"] = $pwd_hash;
        $insert_arr ["cellphone"] = $cellphone;
        $insert_arr ["nickname"] = $nickname;
        $insert_arr ["sex"] = $sex;
        $insert_arr ["role"] = $role;
        $insert_arr ["location_id"] = $location_id;
        $insert_arr ["birthday"] = $birthday;
        $insert_arr ["add_time"] = time();
        $insert_arr ["reg_from"] = $user_info_arr ['reg_from'];
        $insert_arr ["reg_ip"] = ip2long(get_client_ip());

        $user_id = $this->add_user($insert_arr);

        if ($user_id) {
            if (!$nickname) {
                $nickname = "约约手机用户" . substr($cellphone, -4);
            }

            $relate_poco_obj = POCO::singleton('pai_relate_poco_class');
            $poco_id = $relate_poco_obj->create_relate_poco_id($user_id, $nickname, $pwd);
            if ($poco_id) {
                $score_obj = POCO::singleton('pai_score_class');
                $score_obj->add_operate_queue($user_id, "regedit", $operate_num = 1, $remark = '');

                $fulltext_obj = POCO::singleton('pai_fulltext_class');
                $fulltext_obj->add_fulltext_act($user_id, 'add');

                //送劵
                $pai_trigger_obj = POCO::singleton('pai_trigger_class');
                $trigger_params = array('user_id' => $user_id);
                $pai_trigger_obj->user_reg_after($trigger_params);

                return $user_id;
            } else {
                //删除已创建的用户ID
                $this->del_user($user_id);
                $err_msg = "POCO ID异常 ";
                return -5;
            }
        } else {
            $err_msg = "注册异常";
            return -6;
        }

    }
    /**
     * PC版创建YUE帐号
     * @param $user_info_arr
     * @param string $err_msg
     * @return int
     * @throws App_Exception
     */
    public function create_account_by_pc($user_info_arr, &$err_msg = "")
    {
        $pwd = $user_info_arr ['pwd'];
        if ($user_info_arr['YUE_REG_NOT_PWD_HASH']) {
            $pwd_hash = $pwd;
        } else {
            $pwd_hash = md5($pwd);
        }

        $cellphone = ( int )$user_info_arr ['cellphone'];
        $nickname = $user_info_arr ['nickname'];
        $sex = $user_info_arr ['sex'];
        $role = "cameraman";
        $poco_id = $user_info_arr ['poco_id'];

        if (empty ($cellphone)) {
            $err_msg = '手机号码不能为空';
            return -1;
        }

        if (empty ($poco_id)) {
            $err_msg = 'POCOID不能为空';
            return -2;
        }

        $check_cellphone = $this->check_cellphone_exist($cellphone);
        if ($check_cellphone) {
            $err_msg = '手机号码已存在';
            return -3;
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "密码长度错误";
            return -5;
        }

        if (!$user_info_arr['YUE_REG_NOT_PWD_HASH']) {
            if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
                $err_msg = "密码HASH错误 ";
                return -4;
            }
        }

        $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
        $location_id = $current_locate_info['location_id'];

        $insert_arr ["pwd_hash"] = $pwd_hash;
        $insert_arr ["cellphone"] = $cellphone;
        $insert_arr ["nickname"] = $nickname;
        $insert_arr ["sex"] = $sex;
        $insert_arr ["role"] = $role;
        $insert_arr ["location_id"] = $location_id;
        $insert_arr ["add_time"] = time();
        $insert_arr ["reg_from"] = 'pc';
        $insert_arr ["reg_ip"] = ip2long(get_client_ip());

        $user_id = $this->add_user($insert_arr);

        if ($user_id) {
            $bind_poco_obj = POCO::singleton('pai_bind_poco_class');
            //记录从PC绑定的POCO号
            $bind_poco_obj->bind_poco_id($user_id, $poco_id);

            $score_obj = POCO::singleton('pai_score_class');
            $score_obj->add_operate_queue($user_id, "regedit", $operate_num = 1, $remark = '');

            $fulltext_obj = POCO::singleton('pai_fulltext_class');
            $fulltext_obj->add_fulltext_act($user_id, 'add');

            $cameraman_card_obj = POCO::singleton('pai_cameraman_card_class');
            $insert_cameraman ["user_id"] = $user_id;
            $cameraman_card_obj->add_cameraman_card($insert_cameraman);

            //送劵
            $pai_trigger_obj = POCO::singleton('pai_trigger_class');
            $trigger_params = array('user_id' => $user_id);
            $pai_trigger_obj->user_reg_after($trigger_params);

            return $user_id;
        } else {
            return 0;
        }
    }
    /**
     * 用户登录验证
     * @param $cellphone
     * @param $password
     * @return bool
     * @throws App_Exception
     */
    public function user_login($cellphone, $password)
    {
        if (empty ($password)) {
            return false;
        }
        $pwd_hash = md5($password);

        $where_str = "cellphone='{$cellphone}' and pwd_hash='{$pwd_hash}'";
        $ret_arr = $this->get_user_list(false, $where_str);
        $ret = $ret_arr[0];

        if ($ret) {
            $user_id = $ret['user_id'];

            $current_locate_info = POCO::execute('common.get_ip_location_info', get_client_ip());
            $location_id = $current_locate_info['location_id'];

            $update_data['last_login_ip'] = ip2long(get_client_ip());;
            $update_data['last_login_time'] = time();
            $update_data['last_login_location'] = $location_id;
            $this->update_user($update_data, $user_id);

            $this->load_member($user_id, $b_hide_online = null);
            return $user_id;
        } else {
            return false;
        }
    }
    /**
     * 登录验证
     * @param $cellphone
     * @param $password
     * @return bool
     * @throws App_Exception
     */
    public function check_login_auth($cellphone, $password)
    {
        if (empty ($password)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':密码不能为空');
        }
        $cellphone = (int)$cellphone;
        $pwd_hash = md5($password);

        $where_str = "cellphone={$cellphone} and pwd_hash='{$pwd_hash}'";
        $ret_arr = $this->get_user_list(false, $where_str);
        $ret = $ret_arr[0];

        if ($ret) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * SETCOOKIE 保存登录信息
     * @param $member_id
     * @param null $b_hide_online
     * @return array
     */
    public function load_member($member_id, $b_hide_online = null)
    {
        $pai_login_session_obj = new pai_login_session_class;
        return $pai_login_session_obj->load_member($member_id, $b_hide_online = null);
    }
    /**
     * 登出
     * @return string
     */
    public function logout()
    {
        $pai_login_session_obj = new pai_login_session_class;
        return $pai_login_session_obj->unload_member();
    }
    /**
     * 插入用户数据
     * @param $insert_data
     * @return int
     * @throws App_Exception
     */
    public function add_user($insert_data)
    {
        if (empty ($insert_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':数组不能为空');
        }
        if (empty ($insert_data ['pwd_hash'])) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':密码不能为空');
        }

        $user_id = $this->insert($insert_data);
        //REDIS
        $chat_user_obj = POCO::singleton('pai_chat_user_info');
        $chat_user_obj->redis_get_user_info($user_id);

        return $user_id;
    }
    /**
     * 根据用户ID 更新用户数据
     * @param $update_data
     * @param $user_id
     * @return mixed
     * @throws App_Exception
     * 参数：
     * $update_data['nickname']   <b>string</b> 用户昵称
     * $update_data['sex'] 		  <b>string</b> 性别  男或女
     * $update_data['birthday']   <b>date</b> 生日  1999-01-01
     * $update_data['attendance'] <b>float</b> 出勤率 支持小数点
     * $update_data['user_level'] <b>int</b> 用户等级
     * $update_data['organizer_level'] <b>int</b> 组织者等级
     * $update_data['model_level']     <b>int</b> 模特等级
     * $update_data['cameraman_level'] <b>int</b> 摄影师等级
     * $update_data['last_login_time'] <b>int</b> 最后登录时间
     * $update_data['last_login_location'] <b>int</b> 最后登录的地区ID
     */
    public function update_user($update_data, $user_id)
    {
        $user_id = ( int )$user_id;

        if (empty ($update_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':数组不能为空');
        }
        if (empty ($user_id)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空');
        }

        $where_str = "user_id = {$user_id}";
        return $this->update($update_data, $where_str);
    }


    public function update_user_by_phone($update_data, $phone)
    {

        if (empty ($update_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':数组不能为空');
        }

        if (empty ($phone)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':手机不能为空');
        }

        $where_str = "cellphone = {$phone}";
        $this->update($update_data, $where_str);
        return 1;
    }
    /**
     * 获取用户昵称
     * @param $user_id
     * @return bool
     */
    public function get_user_nickname_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id < 1) {
            return false;
        }
        $row = $this->find("user_id={$user_id}", '', 'nickname');
        return $row['nickname'];
    }
    /**
     * 通过用户ID获取详细信息
     *
     * @param int $user_id
     * @return array
     */
    public function get_user_info($user_id)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("非法参数 user_id 只能为整数", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return false;
        }
        $row = $this->find("user_id={$user_id}");
        return $row;
    }
    /**
     * 获取用户性别
     * @param $user_id
     * @return bool
     */
    public function get_user_sex($user_id)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            return false;
        }
        $row = $this->find("user_id={$user_id}", '', 'sex');
        return $row['sex'];
    }
    /**
     * 通过手机获取用户信息
     * @param $phone
     * @return array|bool
     */
    public function get_user_by_phone($phone)
    {
        if (empty($phone)) {
            return false;
        }
        $row = $this->find("cellphone='{$phone}'");
        return $row;
    }
    /**
     * 获取用户数据
     * @param bool|false $b_select_count
     * @param string $where_str 查询条件
     * @param string $order_by 排序
     * @param string $limit
     * @param string $fields 查询字段
     * @return array|int
     */
    public function get_user_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
    {
        if ($b_select_count == true) {
            $ret = $this->findCount($where_str);
        } else {
            $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        }
        return $ret;
    }
    /**
     * 根据用户ID获取用户数据
     * @param $user_id
     * @return array
     */
    public function get_user_info_by_user_id($user_id)
    {
        $user_id = ( int )$user_id;
        $ret = $this->get_user_info($user_id);
        //别暴露密码
        unset($ret['pwd_hash']);

        $payment_obj = POCO::singleton('pai_payment_class');
        $pic_obj = POCO::singleton('pai_pic_class');
        //$user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
        //$comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');
        $account_info = $payment_obj->get_user_account_info($user_id);
        $ret ['bail_available_balance'] = $payment_obj->get_bail_available_balance($user_id);
        $ret ['purse_available_balance'] = $payment_obj->get_purse_available_balance($user_id);
        $ret ['available_balance'] = $account_info ['available_balance']; //钱包余额
        $ret ['balance'] = $account_info ['balance'];
        $ret ['payable'] = $account_info ['payable'];
        $ret ['phone'] = $this->get_phone_by_user_id($user_id);

        /*
        //获取客户端的数据
        require_once('/disk/data/htdocs232/poco/pai/protocol/yue_protocol.inc.php');
        $cp = new yue_protocol_system();
        $agent   = strtolower($_SERVER['HTTP_USER_AGENT']);
        $iphone  = (strpos($agent,'iphone')) ? true : false;
        $android = (strpos($agent,'android')) ? true : false;
        if( $android ) {
            $app_name = 'poco_yuepai_android';
        }
        elseif( $iphone ){
            $app_name = 'poco_yuepai_iphone';
        }
        else{
            $app_name = 'poco_yuepai_android';
        }
        // 获取用户的授权信息
        $access_info = $cp->get_access_info($user_id, $app_name);
        $ret['app_access_token'] = $access_info['access_token'];
        $ret['app_expire_time']  = $access_info['expire_time'];
        */
        $ret ['user_icon'] = get_user_icon($user_id, 165) . "?" . date("YmdHis");
        //$ret ['ticket_num'] = count_act_ticket($user_id);
        $ret ['city_name'] = get_poco_location_name_by_location_id($ret ['location_id']);
        //粉丝数
        //$ret ['fans'] = $user_follow_obj->get_user_be_follow_by_user_id ( $user_id, true );
        //$ret ['follow'] = $user_follow_obj->get_user_follow_by_user_id ( $user_id, true );

        /*		if ($ret ['role'] == 'model')
                {
                    $model_card_obj = POCO::singleton ( 'pai_model_card_class' );

                    $model_info = $model_card_obj->get_model_card_by_user_id ( $user_id );
                    $ret = array_merge($ret, $model_info);
                }*/

        /*		if ($ret ['role'] == 'cameraman')
                {
                    $cameraman_card_obj = POCO::singleton ( 'pai_cameraman_card_class' );
                    $cameraman_comment_log_obj = POCO::singleton ( 'pai_cameraman_comment_log_class' );
                    $ret ['comment_num'] = $cameraman_comment_log_obj->get_cameraman_comment_list ( $user_id, true ); //评价数量


                    // 出勤率评分星星
                    $attendance_has_star = intval ( floor ( floor ( $ret ['attendance'] ) / 20 ) );
                    $attendance_miss_star = 5 - $attendance_has_star;

                    for($i = 0; $i < 5; $i ++)
                    {
                        if ($attendance_has_star > 0)
                        {
                            $ret ['attendance_stars_list'] [$i] ['is_red'] = 1;
                            $attendance_has_star --;
                        } else
                        {
                            $ret ['attendance_stars_list'] [$i] ['is_red'] = 0;
                            $attendance_miss_star --;
                        }
                    }

                    $user_level_obj = POCO::singleton ( 'pai_user_level_class' );
                    $ret ['user_level'] = $user_level_obj->get_user_level($user_id);
                    $cameraman_info = $cameraman_card_obj->get_cameraman_card_info($user_id);
                    $ret = array_merge($ret, $cameraman_info);
                }*/

        $user_level_obj = POCO::singleton('pai_user_level_class');
        $ret ['user_level'] = $user_level_obj->get_user_level($user_id);

        $pic_arr = $pic_obj->get_user_pic($user_id, "0,15", "img");
        foreach ($pic_arr as $k => $val) {
            $pic_arr [$k] ['big_user_icon'] = yueyue_resize_act_img_url($val ['img']);
            $pic_arr [$k] ['user_icon'] = yueyue_resize_act_img_url($val ['img']);
        }

        $ret ['pic_arr'] = $pic_arr;

        /*		$comment_score = $comment_score_rank_obj->get_comment_score_rank($user_id);
                //没有分数的默认为3
                $comment_score = $comment_score ? $comment_score : 3;
                $ret ['comment_score'] = $comment_score*2;

                // 评价评分星星
                $comment_has_star = intval ( round ( $comment_score ) );
                $comment_miss_star = 5 - $comment_has_star;

                for($i = 0; $i < 5; $i ++)
                {
                    if ($comment_has_star > 0)
                    {
                        $ret ['comment_stars_list'] [$i] ['is_red'] = 1;
                        $comment_has_star --;
                    } else
                    {
                        $ret ['comment_stars_list'] [$i] ['is_red'] = 0;
                        $comment_miss_star --;
                    }
                }*/
        return $ret;
    }

    /**
     * 检查手机号是否已存在
     * @param $cellphone
     * @return bool
     */
    public function check_cellphone_exist($cellphone)
    {
        $ret = $this->get_user_list(true, "cellphone='{$cellphone}'");
        if ($ret) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * 检验手机号格式
     *
     * @param int $phone 手机号
     * @return bool
     */
    function check_phone_format($phone)
    {
        if (!preg_match('/^1\d{10}$/isU', $phone)) {
            return false;
        }
        return true;
    }
    /**
     * 检查用户角色
     * @param int $user_id
     * return  摄影师为cameraman  模特为model  机构为organization  空为未参加约拍
     */
    public function check_role($user_id)
    {
        $user_info = $this->get_user_info($user_id);
        $role = $user_info ['role'];
        return $role;
    }
    /**
     * 获取绑定的手机号，根据用户ID
     * @param int $user_id
     * @return string 返回手机号码，空字符串未绑定
     */
    public function get_phone_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id < 1) {
            trace("用户ID参数错误", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return false;
        }
        $user_info = $this->get_user_info($user_id);
        return $user_info['cellphone'];
    }
    /**
     * 获取用户ID，根据绑定的手机号
     * @param string $phone
     * @return int 返回用户ID，0未绑定
     */
    public function get_user_id_by_phone($phone)
    {
        if (!preg_match('/^1\d{10}$/isU', $phone)) {
            return false;
        }
        $row = $this->find("cellphone='{$phone}'");
        return $row['user_id'];
    }
    /**
     * 获取POCOID，根据绑定的手机号
     * @param string $phone
     * @return int 返回用户ID，0未绑定
     */
    public function get_poco_id_by_phone($phone)
    {
        return POCO::execute(array('member.get_user_id_by_login_mobile'), array($phone, true));
    }
    /**
     * 增加绑定手机log
     * @param $user_id
     * @param $phone
     * @param $type
     * @return array
     * @throws App_Exception
     */
    function add_bind_phone_log($user_id, $phone, $type)
    {
        $user_id = intval($user_id);
        if ($user_id < 1 || !preg_match('/^1\d{10}$/isU', $phone) || !in_array($type, array('FOR_REGISTER', 'BIND_PHONE'))) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':参数错误');
        }
        $t = time();
        $sql = " INSERT INTO pai_db.pai_bind_phone_log_tbl(user_id,phone,type,add_time)VALUES({$user_id},'{$phone}','{$type}',{$t})";
        $ret = db_simple_getdata($sql, false, 101);

        return $ret;
    }
    /**
     * 根据手机号码更新用户密码
     * @param $phone 手机号码
     * @param $pwd 用户密码
     * @return bool|int
     * @throws App_Exception
     */
    function update_pwd_by_phone($phone, $pwd)
    {
        $phone = (int)$phone;
        if ($phone < 1) {
            trace("非法参数 phone 只能为整数", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return -1;
        }
        $pwd_len = strlen($pwd);
        if ($pwd_len < 6 || $pwd_len >= 32) {
            trace("非法参数pwd 必须是6位以上  32位以下", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return -2;
        }
        //$log_arr['phone'] = $phone;
        //pai_log_class::add_log($log_arr, 'update_pwd_by_phone', 'reset_pwd');

        $update_data['pwd_hash'] = md5($pwd);
        $update_data['reset_pwd_time'] = time();
        $ret = $this->update_user_by_phone($update_data, $phone);
        return true;

    }
    /**
     * 根据用户ID更新用户密码
     * @param $user_id 用户名
     * @param $pwd 用户密码
     * @return bool|int
     * @throws App_Exception
     */
    function update_pwd_by_user_id($user_id, $pwd)
    {
        $user_id = (int)$user_id;
        if ($user_id < 1) {
            trace("非法参数 user_id 只能为整数", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return -1;
        }
        $pwd_len = strlen($pwd);
        if ($pwd_len < 6 || $pwd_len >= 32) {
            trace("非法参数pwd 必须是6位以上  32位以下", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return -2;
        }

        //$log_arr['user_id'] = $user_id;
        //pai_log_class::add_log($log_arr, 'update_pwd_by_user_id', 'reset_pwd');

        $update_data['pwd_hash'] = md5($pwd);
        $update_data['reset_pwd_time'] = time();
        $ret = $this->update_user($update_data, $user_id);
        return true;

    }
    /**
     * 根据用户ID更新用户密码
     *
     * @param int $user_id
     * @param string $password
     * @return bool
     */
    public function check_pwd($user_id, $password)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("非法参数 user_id 只能为整数", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return false;
        }
        if (empty($password)) {
            trace("非法参数 password", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return false;
        }
        $user_info = $this->get_user_info($user_id);
        if (!empty($user_info) && $user_info['pwd_hash'] == md5($password)) {
            return true;
        }
        return false;
    }
    /**
     * 根据用户ID更新用户密码
     * @param $user_id
     * @param $phone
     * @return bool|mixed
     * @throws App_Exception
     */
    public function bind_phone($user_id, $phone)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("非法参数 user_id 只能为整数", basename(__FILE__) . " 行:" . __LINE__ . " 方法:" . __METHOD__);
            return false;
        }
        if (!preg_match('/^1\d{10}$/isU', $phone)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':参数错误');
        }
        $update_data = array('cellphone' => $phone);
        $ret = $this->update_user($update_data, $user_id);

        return $ret;
    }
    /**
     * 获取热门模特
     * @param bool|false $b_select_count
     * @param $location_id
     * @param string $limit
     * @return array|int
     */
    public function get_hot_model($b_select_count = false, $location_id, $limit = '0,6')
    {
        $where_str = "1";
        if ($location_id) {
            $where_str .= " AND location_id LIKE '{$location_id}%'";
        }
        $where_str .= " AND role='model'";
        //只显示有更换头像的用户
        /*$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
        $user_icon_arr = $user_icon_obj->get_user_icon_list(false, '', 'user_id DESC', '', 'user_id');
        foreach($user_icon_arr as $val)
        {
            $__user_icon[] = $val['user_id'];
        }
        $user_icon_list = implode(",",$__user_icon);
        if($user_icon_list)
        {
            $where_str .= " AND user_id IN ($user_icon_list)";
        }*/

        $ret = $this->get_user_list($b_select_count, $where_str, 'user_id desc', $limit, 'user_id,nickname');
        foreach ($ret as $k => $val) {
            $ret [$k] ['user_icon'] = get_user_icon($val ['user_id'], 165);
        }
        return $ret;
    }

    /**
     * 根据用户ID删除用户
     * @param $user_id
     * @return bool
     * @throws App_Exception
     */
    public function del_user($user_id)
    {
        $user_id = ( int )$user_id;
        if (empty ($user_id)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':user_id不能为空');
        }
        $where_str = "user_id = {$user_id}";
        return $this->delete($where_str);
    }

    /**
     * 更改昵称
     * @param $user_id
     * @param string $nickname
     * @return bool|mixed
     * @throws App_Exception
     */
    public function update_nickname($user_id, $nickname = '')
    {
        $user_id = ( int )$user_id;
        if (empty ($user_id)) {
            return false;
        }

        $update_data ['nickname'] = $nickname;
        $where_str = "user_id = {$user_id}";
        return $this->update($update_data, $where_str);
    }
    /**
     * 更新模特库初始密码
     * @param $user_id
     * @return mixed
     * @throws App_Exception
     */
    public function update_model_db_pwd($user_id)
    {
        $user_id = (int)$user_id;
        $where_str = "user_id = {$user_id}";
        $update_data['pwd_hash'] = "poco_model_db";
        return $this->update($update_data, $where_str);
    }
    /**
     * 根据昵称查用户ID
     * @param string $nickname
     * @return array
     */
    public function get_user_id_by_nickname($nickname = '')
    {
        $ret = $this->findAll("nickname like '%{$nickname}%'", $limit = null, $sort = null, $fields = 'user_id');
        return $ret;
    }
    /**
     * 更新商城用户信息
     * @param $update_data
     * @param $user_id
     * @return bool
     * @throws App_Exception
     */
    public function update_mall_user_info($update_data, $user_id)
    {
        $user_id = (int)$user_id;
        if (empty($user_id) || empty($update_data)) {
            $result['result'] = -1;
            $result['message'] = "用户ID或更新数组不能为空";
            return $result;
        }
        $user_info = $this->get_user_info($user_id);

        $pic_obj = POCO::singleton('pai_pic_class');
        $text_examine_obj = POCO::singleton('pai_text_examine_class');

        if ($update_data['pic_arr']) {
            $pic_update_data['pic_arr'] = $update_data['pic_arr'];
            $pic_obj->add_pic($user_id, $pic_update_data['pic_arr']);
        } else {
            $pic_obj->del_pic($user_id);
        }

        if ($update_data['nickname']) {
            $task_seller_obj = POCO::singleton('pai_mall_seller_class');
            $task_seller_obj->change_seller_user_name($user_id, $update_data['nickname']);
            //加审核
            $text_examine_obj->audit_text($user_id, "nickname_text", $user_info['nickname'], $update_data['nickname']);
            $user_update_data['nickname'] = $update_data['nickname'];
        }

        if ($update_data['location_id']) {
            $user_update_data['location_id'] = (int)$update_data['location_id'];
        }

        //加审核
        $text_examine_obj->audit_text($user_id, "customer_remark", $user_info['remark'], $update_data['remark']);
        $user_update_data['remark'] = $update_data['remark'];
        $user_update_data['is_display_record'] = (int)$update_data['is_display_record'];

        $this->update_user($user_update_data, $user_id);
        return true;
    }

    /**
     * 消费者版分享文案
     * @param $user_id
     * @return mixed
     */
    public function get_share_text($user_id)
    {
        $user_id = (int)$user_id;
        $pai_user_obj = POCO::singleton('pai_user_class');
        $user_icon_obj = POCO::singleton('pai_user_icon_class');

        $nickname = $pai_user_obj->get_user_nickname_by_user_id($user_id);

        $title = "我是{$nickname}，一起来约约吧 | 约约";

        $content = '我在这里找到好多有意思的！快来一起玩才够味！';

        $sina_content = "我是{$nickname}，{$content}";
        $share_url = 'http://www.yueus.com/user_info/' . $user_id;
        $share_img = $user_icon_obj->get_user_icon($user_id, 165);

        $url = "http://www.yueus.com/output_img.php?img=" . $share_img;

        $share_text['title'] = $title;
        $share_text['content'] = $content;
        $share_text['sina_content'] = $sina_content . ' ' . $share_url;
        $share_text['remark'] = '';
        $share_text['url'] = $share_url;
        $share_text['img'] = $url;
        $share_text['user_id'] = $user_id;
        $share_text['qrcodeurl'] = $share_url;

        return $share_text;
    }

    public function get_poco_excute($type, $param)
    {
        return POCO::execute(array($type), $param);
    }
}