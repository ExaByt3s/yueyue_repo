<?php

/**
 *�û���¼������
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-10 16:06:04
 * @version 1
 */
class pai_loginlog_report_class extends POCO_TDG
{
    /**
     * ���캯��
     *
     */
    public function __construct()
    {
        $this->setServerId(22);
        $this->setDBName('yueyue_log_tmp_db');
        $this->setTableName('yueyue_loginlog_tbl_201503');
    }

    /**
     * @param $tablename [����]
     * @param bool|false $b_select_count [�Ƿ��ѯ����]
     * @param string $where_str [����]
     * @param string $order_by [����]
     * @param string $limit [ѭ������]
     * @param string $fields [�����ֶ�]
     * @return array|int [����ֵ]
     */
    public function get_login_list($tablename, $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
    {
        //������ڿ�
        if (empty($tablename)) {
            $tablename = "yueyue_loginlog_tbl_201503";
        }
        $this->setTableName($tablename);
        if ($b_select_count == true) {
            $ret = $this->findCount($where_str, $fields);
        } else {
            $ret = $this->findAll($where_str, $limit, $order_by, $fields);
        }
        return $ret;
    }

    /**
     * @param $month [���·�]
     * @return bool|string [�������»���false]
     * @throws App_Exception
     */
    public function get_tablename_by_month($month)
    {
        $month = $month;
        if (empty($month)) {
            $month = date('Y-m', time());
        }
        $month = date('Ym', strtotime($month));
        $tablename = 'yueyue_loginlog_tbl_' . $month;
        $res = $this->query("SHOW TABLES FROM `yueyue_log_tmp_db` LIKE '{$tablename}'");
        if (empty($res) || !is_array($res)) {
            return false;
        }
        return $tablename;
    }

    /**
     * ��ȡ�û���¼����
     * @param string $where_str [��ѯ����]
     * @param $month [���������]
     * @return array|bool [����ֵ]
     * @throws App_Exception
     */
    public function get_user_login_count_by_user_str($where_str = '', $month)
    {
        if (empty($month)) {
            return false;
        }
        $tablename = $this->get_tablename_by_month($month);
        if (empty($tablename)) return false;
        $begin_month = beginMonth($month, true);
        $end_month = endMonth($month, true);
        if (strlen($where_str) > 0) $where_str .= " AND ";
        $where_str .= "date_time >= '{$begin_month}' AND date_time <= '{$end_month}'";
        $sql = "SELECT count(date_time) AS login_count,user_id FROM yueyue_log_tmp_db.{$tablename} WHERE {$where_str} GROUP BY user_id";
        $ret = $this->findBySql($sql);
        return $ret;
    }

    /**
     * ����ʱ�� - ��ȡ�û���¼����
     * @param string $where_str [��ѯ����]
     * @param $day [��]
     * @return array|bool [����ֵ]
     * @throws App_Exception
     */
    public function get_user_login_count_by_user_str_day($where_str = '', $day)
    {
        if (empty($day)) {
            return false;
        }
        $tablename = $this->get_tablename_by_month($day);
        $sql = "SELECT count(date_time) AS login_count,user_id FROM yueyue_log_tmp_db.{$tablename} GROUP BY user_id";
        $ret = $this->findBySql($sql);
        return $ret;
    }
}