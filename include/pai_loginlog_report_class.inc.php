<?php

/**
 *用户登录次数类
 * @authors xiao xiao (xiaojm@yueus.com)
 * @date    2015-03-10 16:06:04
 * @version 1
 */
class pai_loginlog_report_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId(22);
        $this->setDBName('yueyue_log_tmp_db');
        $this->setTableName('yueyue_loginlog_tbl_201503');
    }

    /**
     * @param $tablename [表名]
     * @param bool|false $b_select_count [是否查询总数]
     * @param string $where_str [条件]
     * @param string $order_by [排序]
     * @param string $limit [循环条数]
     * @param string $fields [返回字段]
     * @return array|int [返回值]
     */
    public function get_login_list($tablename, $b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,30', $fields = '*')
    {
        //当表等于空
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
     * @param $month [年月份]
     * @return bool|string [返回年月或者false]
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
     * 获取用户登录次数
     * @param string $where_str [查询条件]
     * @param $month [输入的年月]
     * @return array|bool [返回值]
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
     * 根据时间 - 获取用户登录次数
     * @param string $where_str [查询条件]
     * @param $day [天]
     * @return array|bool [返回值]
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