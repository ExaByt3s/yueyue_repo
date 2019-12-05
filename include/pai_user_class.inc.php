<?php
/*
 * �û��������
 */
include_once("/disk/data/htdocs232/poco/pai/include/pai_login_session_class.inc.php");

class pai_user_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId(101);
        $this->setDBName('pai_db');
        $this->setTableName('pai_user_tbl');
    }

    /**
     * ע�ᴴ���ʺ�
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
            $err_msg = '�ֻ����벻��Ϊ��';
            return -1;
        }

        $user_info = $this->get_user_by_phone($cellphone);
        //����û��Ƿ�Ԥ�ȵ����
        if ($user_info ['pwd_hash'] == 'poco_model_db') {
            $is_model_db = true;
        }

        if (!$is_model_db) {
            //���˾Ͳ����ٰ�
            $check_cellphone = $this->check_cellphone_exist($cellphone);
            if ($check_cellphone) {
                $err_msg = '�ֻ������Ѵ���';
                return -2;
            }
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "���볤�ȴ���";
            return -3;
        }

        if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
            $err_msg = "����HASH���� ";
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
            $nickname = "�ֻ��û�_" . substr($cellphone, -4);
        }

        //Ԥ�����û����⴦��
        if ($is_model_db) {
            $ret = $this->update_pwd_by_phone($cellphone, $pwd);
            if (!$ret) {
                $err_msg = "ע���쳣";
                return -6;
            }

            $user_id = $this->get_user_id_by_phone($cellphone);

            //ע��Ҫ������Դ�ͼ���ʱ��
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
                $err_msg = "ע���쳣";
                return -6;
            }

            $relate_poco_obj = POCO::singleton('pai_relate_poco_class');
            $poco_id = $relate_poco_obj->create_relate_poco_id($user_id, $nickname, $pwd);
            if ($poco_id < 1) {
                //ɾ���Ѵ������û�ID
                $this->del_user($user_id);

                $err_msg = "POCO ID�쳣 ";
                return -5;
            }
        }


        //����ͳ��
        POCO::singleton( 'pai_pa_dt_register_class' )->add_register_log_by_user_id($user_id);

        //�̈́�
        $pai_trigger_obj = POCO::singleton('pai_trigger_class');
        $trigger_params = array('user_id' => $user_id);
        $pai_trigger_obj->user_reg_after($trigger_params);

        return $user_id;
    }

    /**
     * �����ʺ�
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
            $err_msg = '�ֻ����벻��Ϊ��';
            return -1;
        }

        $check_cellphone = $this->check_cellphone_exist($cellphone);
        if ($check_cellphone) {
            $err_msg = '�ֻ������Ѵ���';
            return -2;
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "���볤�ȴ���";
            return -3;
        }

        if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
            $err_msg = "����HASH���� ";
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
                $nickname = "ģ��_" . substr($cellphone, -4);
            } else {
                $nickname = "��Ӱʦ_" . substr($cellphone, -4);
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
                $nickname = "ԼԼ�ֻ��û�" . substr($cellphone, -4);
            }

            $relate_poco_obj = POCO::singleton('pai_relate_poco_class');
            $poco_id = $relate_poco_obj->create_relate_poco_id($user_id, $nickname, $pwd);
            if ($poco_id) {
                $score_obj = POCO::singleton('pai_score_class');
                $score_obj->add_operate_queue($user_id, "regedit", $operate_num = 1, $remark = '');

                $fulltext_obj = POCO::singleton('pai_fulltext_class');
                $fulltext_obj->add_fulltext_act($user_id, 'add');

                //�̈́�
                $pai_trigger_obj = POCO::singleton('pai_trigger_class');
                $trigger_params = array('user_id' => $user_id);
                $pai_trigger_obj->user_reg_after($trigger_params);

                return $user_id;
            } else {
                //ɾ���Ѵ������û�ID
                $this->del_user($user_id);
                $err_msg = "POCO ID�쳣 ";
                return -5;
            }
        } else {
            $err_msg = "ע���쳣";
            return -6;
        }

    }
    /**
     * PC�洴��YUE�ʺ�
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
            $err_msg = '�ֻ����벻��Ϊ��';
            return -1;
        }

        if (empty ($poco_id)) {
            $err_msg = 'POCOID����Ϊ��';
            return -2;
        }

        $check_cellphone = $this->check_cellphone_exist($cellphone);
        if ($check_cellphone) {
            $err_msg = '�ֻ������Ѵ���';
            return -3;
        }

        if (!preg_match("/^.{6,31}$/", $pwd)) {
            $err_msg = "���볤�ȴ���";
            return -5;
        }

        if (!$user_info_arr['YUE_REG_NOT_PWD_HASH']) {
            if (!preg_match("/^[a-z0-9]{32}$/", $pwd_hash)) {
                $err_msg = "����HASH���� ";
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
            //��¼��PC�󶨵�POCO��
            $bind_poco_obj->bind_poco_id($user_id, $poco_id);

            $score_obj = POCO::singleton('pai_score_class');
            $score_obj->add_operate_queue($user_id, "regedit", $operate_num = 1, $remark = '');

            $fulltext_obj = POCO::singleton('pai_fulltext_class');
            $fulltext_obj->add_fulltext_act($user_id, 'add');

            $cameraman_card_obj = POCO::singleton('pai_cameraman_card_class');
            $insert_cameraman ["user_id"] = $user_id;
            $cameraman_card_obj->add_cameraman_card($insert_cameraman);

            //�̈́�
            $pai_trigger_obj = POCO::singleton('pai_trigger_class');
            $trigger_params = array('user_id' => $user_id);
            $pai_trigger_obj->user_reg_after($trigger_params);

            return $user_id;
        } else {
            return 0;
        }
    }
    /**
     * �û���¼��֤
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
     * ��¼��֤
     * @param $cellphone
     * @param $password
     * @return bool
     * @throws App_Exception
     */
    public function check_login_auth($cellphone, $password)
    {
        if (empty ($password)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���벻��Ϊ��');
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
     * SETCOOKIE �����¼��Ϣ
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
     * �ǳ�
     * @return string
     */
    public function logout()
    {
        $pai_login_session_obj = new pai_login_session_class;
        return $pai_login_session_obj->unload_member();
    }
    /**
     * �����û�����
     * @param $insert_data
     * @return int
     * @throws App_Exception
     */
    public function add_user($insert_data)
    {
        if (empty ($insert_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��');
        }
        if (empty ($insert_data ['pwd_hash'])) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���벻��Ϊ��');
        }

        $user_id = $this->insert($insert_data);
        //REDIS
        $chat_user_obj = POCO::singleton('pai_chat_user_info');
        $chat_user_obj->redis_get_user_info($user_id);

        return $user_id;
    }
    /**
     * �����û�ID �����û�����
     * @param $update_data
     * @param $user_id
     * @return mixed
     * @throws App_Exception
     * ������
     * $update_data['nickname']   <b>string</b> �û��ǳ�
     * $update_data['sex'] 		  <b>string</b> �Ա�  �л�Ů
     * $update_data['birthday']   <b>date</b> ����  1999-01-01
     * $update_data['attendance'] <b>float</b> ������ ֧��С����
     * $update_data['user_level'] <b>int</b> �û��ȼ�
     * $update_data['organizer_level'] <b>int</b> ��֯�ߵȼ�
     * $update_data['model_level']     <b>int</b> ģ�صȼ�
     * $update_data['cameraman_level'] <b>int</b> ��Ӱʦ�ȼ�
     * $update_data['last_login_time'] <b>int</b> ����¼ʱ��
     * $update_data['last_login_location'] <b>int</b> ����¼�ĵ���ID
     */
    public function update_user($update_data, $user_id)
    {
        $user_id = ( int )$user_id;

        if (empty ($update_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��');
        }
        if (empty ($user_id)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':�û�ID����Ϊ��');
        }

        $where_str = "user_id = {$user_id}";
        return $this->update($update_data, $where_str);
    }


    public function update_user_by_phone($update_data, $phone)
    {

        if (empty ($update_data)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��');
        }

        if (empty ($phone)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':�ֻ�����Ϊ��');
        }

        $where_str = "cellphone = {$phone}";
        $this->update($update_data, $where_str);
        return 1;
    }
    /**
     * ��ȡ�û��ǳ�
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
     * ͨ���û�ID��ȡ��ϸ��Ϣ
     *
     * @param int $user_id
     * @return array
     */
    public function get_user_info($user_id)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("�Ƿ����� user_id ֻ��Ϊ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return false;
        }
        $row = $this->find("user_id={$user_id}");
        return $row;
    }
    /**
     * ��ȡ�û��Ա�
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
     * ͨ���ֻ���ȡ�û���Ϣ
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
     * ��ȡ�û�����
     * @param bool|false $b_select_count
     * @param string $where_str ��ѯ����
     * @param string $order_by ����
     * @param string $limit
     * @param string $fields ��ѯ�ֶ�
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
     * �����û�ID��ȡ�û�����
     * @param $user_id
     * @return array
     */
    public function get_user_info_by_user_id($user_id)
    {
        $user_id = ( int )$user_id;
        $ret = $this->get_user_info($user_id);
        //��¶����
        unset($ret['pwd_hash']);

        $payment_obj = POCO::singleton('pai_payment_class');
        $pic_obj = POCO::singleton('pai_pic_class');
        //$user_follow_obj = POCO::singleton ( 'pai_user_follow_class' );
        //$comment_score_rank_obj = POCO::singleton('pai_comment_score_rank_class');
        $account_info = $payment_obj->get_user_account_info($user_id);
        $ret ['bail_available_balance'] = $payment_obj->get_bail_available_balance($user_id);
        $ret ['purse_available_balance'] = $payment_obj->get_purse_available_balance($user_id);
        $ret ['available_balance'] = $account_info ['available_balance']; //Ǯ�����
        $ret ['balance'] = $account_info ['balance'];
        $ret ['payable'] = $account_info ['payable'];
        $ret ['phone'] = $this->get_phone_by_user_id($user_id);

        /*
        //��ȡ�ͻ��˵�����
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
        // ��ȡ�û�����Ȩ��Ϣ
        $access_info = $cp->get_access_info($user_id, $app_name);
        $ret['app_access_token'] = $access_info['access_token'];
        $ret['app_expire_time']  = $access_info['expire_time'];
        */
        $ret ['user_icon'] = get_user_icon($user_id, 165) . "?" . date("YmdHis");
        //$ret ['ticket_num'] = count_act_ticket($user_id);
        $ret ['city_name'] = get_poco_location_name_by_location_id($ret ['location_id']);
        //��˿��
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
                    $ret ['comment_num'] = $cameraman_comment_log_obj->get_cameraman_comment_list ( $user_id, true ); //��������


                    // ��������������
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
                //û�з�����Ĭ��Ϊ3
                $comment_score = $comment_score ? $comment_score : 3;
                $ret ['comment_score'] = $comment_score*2;

                // ������������
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
     * ����ֻ����Ƿ��Ѵ���
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
     * �����ֻ��Ÿ�ʽ
     *
     * @param int $phone �ֻ���
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
     * ����û���ɫ
     * @param int $user_id
     * return  ��ӰʦΪcameraman  ģ��Ϊmodel  ����Ϊorganization  ��Ϊδ�μ�Լ��
     */
    public function check_role($user_id)
    {
        $user_info = $this->get_user_info($user_id);
        $role = $user_info ['role'];
        return $role;
    }
    /**
     * ��ȡ�󶨵��ֻ��ţ������û�ID
     * @param int $user_id
     * @return string �����ֻ����룬���ַ���δ��
     */
    public function get_phone_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id < 1) {
            trace("�û�ID��������", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return false;
        }
        $user_info = $this->get_user_info($user_id);
        return $user_info['cellphone'];
    }
    /**
     * ��ȡ�û�ID�����ݰ󶨵��ֻ���
     * @param string $phone
     * @return int �����û�ID��0δ��
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
     * ��ȡPOCOID�����ݰ󶨵��ֻ���
     * @param string $phone
     * @return int �����û�ID��0δ��
     */
    public function get_poco_id_by_phone($phone)
    {
        return POCO::execute(array('member.get_user_id_by_login_mobile'), array($phone, true));
    }
    /**
     * ���Ӱ��ֻ�log
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
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':��������');
        }
        $t = time();
        $sql = " INSERT INTO pai_db.pai_bind_phone_log_tbl(user_id,phone,type,add_time)VALUES({$user_id},'{$phone}','{$type}',{$t})";
        $ret = db_simple_getdata($sql, false, 101);

        return $ret;
    }
    /**
     * �����ֻ���������û�����
     * @param $phone �ֻ�����
     * @param $pwd �û�����
     * @return bool|int
     * @throws App_Exception
     */
    function update_pwd_by_phone($phone, $pwd)
    {
        $phone = (int)$phone;
        if ($phone < 1) {
            trace("�Ƿ����� phone ֻ��Ϊ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return -1;
        }
        $pwd_len = strlen($pwd);
        if ($pwd_len < 6 || $pwd_len >= 32) {
            trace("�Ƿ�����pwd ������6λ����  32λ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
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
     * �����û�ID�����û�����
     * @param $user_id �û���
     * @param $pwd �û�����
     * @return bool|int
     * @throws App_Exception
     */
    function update_pwd_by_user_id($user_id, $pwd)
    {
        $user_id = (int)$user_id;
        if ($user_id < 1) {
            trace("�Ƿ����� user_id ֻ��Ϊ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return -1;
        }
        $pwd_len = strlen($pwd);
        if ($pwd_len < 6 || $pwd_len >= 32) {
            trace("�Ƿ�����pwd ������6λ����  32λ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
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
     * �����û�ID�����û�����
     *
     * @param int $user_id
     * @param string $password
     * @return bool
     */
    public function check_pwd($user_id, $password)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("�Ƿ����� user_id ֻ��Ϊ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return false;
        }
        if (empty($password)) {
            trace("�Ƿ����� password", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return false;
        }
        $user_info = $this->get_user_info($user_id);
        if (!empty($user_info) && $user_info['pwd_hash'] == md5($password)) {
            return true;
        }
        return false;
    }
    /**
     * �����û�ID�����û�����
     * @param $user_id
     * @param $phone
     * @return bool|mixed
     * @throws App_Exception
     */
    public function bind_phone($user_id, $phone)
    {
        $user_id = ( int )$user_id;
        if ($user_id < 1) {
            trace("�Ƿ����� user_id ֻ��Ϊ����", basename(__FILE__) . " ��:" . __LINE__ . " ����:" . __METHOD__);
            return false;
        }
        if (!preg_match('/^1\d{10}$/isU', $phone)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':��������');
        }
        $update_data = array('cellphone' => $phone);
        $ret = $this->update_user($update_data, $user_id);

        return $ret;
    }
    /**
     * ��ȡ����ģ��
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
        //ֻ��ʾ�и���ͷ����û�
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
     * �����û�IDɾ���û�
     * @param $user_id
     * @return bool
     * @throws App_Exception
     */
    public function del_user($user_id)
    {
        $user_id = ( int )$user_id;
        if (empty ($user_id)) {
            throw new App_Exception (__CLASS__ . '::' . __FUNCTION__ . ':user_id����Ϊ��');
        }
        $where_str = "user_id = {$user_id}";
        return $this->delete($where_str);
    }

    /**
     * �����ǳ�
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
     * ����ģ�ؿ��ʼ����
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
     * �����ǳƲ��û�ID
     * @param string $nickname
     * @return array
     */
    public function get_user_id_by_nickname($nickname = '')
    {
        $ret = $this->findAll("nickname like '%{$nickname}%'", $limit = null, $sort = null, $fields = 'user_id');
        return $ret;
    }
    /**
     * �����̳��û���Ϣ
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
            $result['message'] = "�û�ID��������鲻��Ϊ��";
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
            //�����
            $text_examine_obj->audit_text($user_id, "nickname_text", $user_info['nickname'], $update_data['nickname']);
            $user_update_data['nickname'] = $update_data['nickname'];
        }

        if ($update_data['location_id']) {
            $user_update_data['location_id'] = (int)$update_data['location_id'];
        }

        //�����
        $text_examine_obj->audit_text($user_id, "customer_remark", $user_info['remark'], $update_data['remark']);
        $user_update_data['remark'] = $update_data['remark'];
        $user_update_data['is_display_record'] = (int)$update_data['is_display_record'];

        $this->update_user($user_update_data, $user_id);
        return true;
    }

    /**
     * �����߰�����İ�
     * @param $user_id
     * @return mixed
     */
    public function get_share_text($user_id)
    {
        $user_id = (int)$user_id;
        $pai_user_obj = POCO::singleton('pai_user_class');
        $user_icon_obj = POCO::singleton('pai_user_icon_class');

        $nickname = $pai_user_obj->get_user_nickname_by_user_id($user_id);

        $title = "����{$nickname}��һ����ԼԼ�� | ԼԼ";

        $content = '���������ҵ��ö�����˼�ģ�����һ����Ź�ζ��';

        $sina_content = "����{$nickname}��{$content}";
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