<?php

/*
 * �û���ע��
 */

class pai_user_follow_class extends POCO_TDG
{

    private $fans_cache_key = "YUEYUE_INTERFACE_USER_FANS_";

    /**
     * ���캯��
     * �趨���ݿ������ ���ݿ� �� ����
     */
    public function __construct()
    {
        $this->setServerId(101);
        $this->setDBName('pai_db');
        $this->setTableName('pai_user_follow_tbl');
    }

    /**
     * ��ӹ�ע
     * @param $follow_user_id ��ע���û�ID
     * @param $be_follow_user_id ����ע���û�ID
     * @return bool|int
     */
    public function add_user_follow($follow_user_id, $be_follow_user_id)
    {
        $follow_user_id = ( int )$follow_user_id;
        $be_follow_user_id = ( int )$be_follow_user_id;

        if (empty ($follow_user_id)) {
            return false;
        }

        if (empty ($be_follow_user_id)) {
            return false;
        }

        $insert_data ['follow_user_id'] = $follow_user_id;
        $insert_data ['be_follow_user_id'] = $be_follow_user_id;
        $insert_data ['add_time'] = time();
        /**
         * $content = "������1����˿ ����ҳ�Ľ���Ӵ";
         * send_message_for_10002($be_follow_user_id, $content, $to_url);
         **/
        $cache_key = $this->get_fans_cache_key($be_follow_user_id);
        POCO::deleteCache($cache_key); //�建��

        return $this->insert($insert_data, "IGNORE");
    }

    /**
     * ��ȡ��ע����
     * @param bool|false $b_select_count
     * @param string $where_str ��ѯ����
     * @param string $order_by ����
     * @param string $limit
     * @param string $fields ��ѯ�ֶ�
     * @return array|int
     */
    public function get_user_follow_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
    {

        if ($b_select_count == true) {
            $ret = $this->findCount($where_str);
        } else {
            $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        }
        return $ret;
    }

    /**
     * �Ƿ��ѹ�ע���û�
     * @param $follow_user_id  ��ע���û�ID
     * @param $be_follow_user_id  ����ע���û�ID
     * @return bool
     */
    public function check_user_follow($follow_user_id, $be_follow_user_id)
    {
        $follow_user_id = ( int )$follow_user_id;
        $be_follow_user_id = ( int )$be_follow_user_id;

        if (empty ($follow_user_id)) {
            return false;
        }
        if (empty ($be_follow_user_id)) {
            return false;
        }
        $where_str = "follow_user_id={$follow_user_id} and be_follow_user_id={$be_follow_user_id}";
        $ret = $this->get_user_follow_list(true, $where_str);

        if ($ret) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * ȡ����ע
     * @param $follow_user_id    ��ע���û�ID
     * @param $be_follow_user_id  ����ע���û�ID
     * @return bool
     * @throws App_Exception
     */
    public function cancel_follow($follow_user_id, $be_follow_user_id)
    {
        $follow_user_id = ( int )$follow_user_id;
        $be_follow_user_id = ( int )$be_follow_user_id;

        if (empty ($follow_user_id)) {
            return false;
        }
        if (empty ($be_follow_user_id)) {
            return false;
        }
        $where_str = "follow_user_id={$follow_user_id} and be_follow_user_id={$be_follow_user_id}";
        $cache_key = $this->get_fans_cache_key($be_follow_user_id);
        POCO::deleteCache($cache_key); //�建��

        return $this->delete($where_str);

    }

    /**
     * �����û�ID��ȡ��ע����
     * @param $user_id
     * @param bool|false $b_select_count
     * @param string $limit
     * @return array|int
     */
    function get_user_follow_by_user_id($user_id, $b_select_count = false, $limit = '0,10')
    {
        $user_obj = POCO::singleton('pai_user_class');
        $user_id = ( int )$user_id;
        $where_str = "follow_user_id={$user_id}";
        $ret = $this->get_user_follow_list($b_select_count, $where_str, 'add_time DESC', $limit, 'be_follow_user_id');
        foreach ($ret as $k => $val) {
            $ret [$k] ['nickname'] = get_user_nickname_by_user_id($val ['be_follow_user_id']);
            $ret [$k] ['user_icon'] = get_user_icon($val ['be_follow_user_id'], 165);
            $ret [$k] ['role'] = $user_obj->check_role($val ['be_follow_user_id']);
        }
        return $ret;
    }

    /**
     * �����û�ID��ȡ��˿����
     * @param $user_id
     * @param bool|false $b_select_count
     * @param string $limit
     * @return array|int|mixed
     */
    function get_user_be_follow_by_user_id($user_id, $b_select_count = false, $limit = '0,10')
    {
        $user_obj = POCO::singleton('pai_user_class');

        //������Ա�ʺ�
        $test_user_id = TEST_PAI_USER_ID;
        $user_id = ( int )$user_id;
        $where_str = "be_follow_user_id={$user_id} AND follow_user_id NOT IN ($test_user_id)";

        if ($b_select_count) {
            $cache_key = $this->get_fans_cache_key($user_id);
            $cache_ret = POCO::getCache($cache_key);
            if (!$cache_ret) {
                $ret = $this->get_user_follow_list($b_select_count, $where_str, 'add_time DESC', $limit, 'follow_user_id');
                $cache_time = 3600;
                POCO::setCache($cache_key, $ret, array('life_time' => $cache_time));
            } else {
                $ret = $cache_ret;
            }
        } else {
            $ret = $this->get_user_follow_list($b_select_count, $where_str, 'add_time DESC', $limit, 'follow_user_id');
            foreach ($ret as $k => $val) {
                $ret [$k] ['nickname'] = get_user_nickname_by_user_id($val ['follow_user_id']);
                $ret [$k] ['user_icon'] = get_user_icon($val ['follow_user_id'], 165);
                $ret [$k] ['role'] = $user_obj->check_role($val ['follow_user_id']);
            }
        }

        return $ret;
    }

    private function get_fans_cache_key($user_id)
    {
        return $this->fans_cache_key . $user_id;
    }

    /**
     * ��ȡ��ע��
     * @param bool|true $b_select_count
     * @param $user_id
     * @param $type
     * @return array|bool|int
     */
    public function get_fans_by_user_id($b_select_count = true, $user_id, $type)
    {
        $user_id = (int)$user_id;
        if (empty($user_id)) {
            return false;
        }
        $where_str = "1 AND be_follow_user_id = {$user_id}";
        if ($type == 'day') {
            $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
            $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
            $where_str .= " AND add_time BETWEEN {$beginToday} AND {$endToday}";
        } elseif ($type == 'week') {
            $date = date('Y-m-d');
            $first = 1; //$first =1 ��ʾÿ������һΪ��ʼ���� 0��ʾÿ����Ϊ��ʼ����
            $w = date('w', strtotime($date));  //��ȡ��ǰ�ܵĵڼ��� ������ 0 ��һ�������� 1 - 6
            $now_start = strtotime("$date -" . ($w ? $w - $first : 6) . ' days'); //��ȡ���ܿ�ʼ���ڣ����$w��0�����ʾ���գ���ȥ 6 ��
            $tmp_now_start = date('Y-m-d 23:59:59', $now_start);
            $now_end = strtotime("$tmp_now_start +6 days");  //���ܽ�������
            $where_str .= " AND add_time BETWEEN {$now_start} AND {$now_end}";
        } elseif ($type == 'month') {
            $beginThismonth = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $endThismonth = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
            $where_str .= " AND add_time BETWEEN {$beginThismonth} AND {$endThismonth}";
        }

        if ($b_select_count == true) {
            $ret = $this->findCount($where_str);
        } else {
            $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        }
        return $ret;
    }
}