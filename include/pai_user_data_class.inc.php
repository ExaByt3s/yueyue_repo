<?php

class pai_user_data_class extends POCO_TDG
{
    /**
     * 构造函数
     *
     */
    public function __construct()
    {
        $this->setServerId(101);
        $this->setDBName('pai_db');
        $this->setTableName('pai_user_data_tbl');
    }

    /**
     * 累加交易次数
     * @param int $user_id
     * @return bool
     */
    public function add_deal_times($user_id)
    {
        $user_id = (int)$user_id;
        if (empty($user_id)) {
            return false;
        }
        $this->insert(array("user_id" => $user_id), "IGNORE");
        $sql_str = "UPDATE pai_db.pai_user_data_tbl SET deal_times=deal_times+1 WHERE user_id={$user_id}";
        db_simple_getdata($sql_str, TRUE, 101);

        return true;
    }

    /**
     * 累加消费金额
     * @param int $user_id
     * @param float $amount
     * @return bool
     */
    public function add_consume_ammount($user_id, $amount)
    {
        $user_id = (int)$user_id;
        $amount = number_format($amount * 1, 2, '.', '') * 1;
        if ($user_id < 1 || $amount <= 0) {
            return false;
        }
        $this->insert(array("user_id" => $user_id), "IGNORE");
        $sql_str = "UPDATE pai_db.pai_user_data_tbl SET consume_ammount=consume_ammount+{$amount} WHERE user_id={$user_id}";
        db_simple_getdata($sql_str, TRUE, 101);

        return true;

    }

    /**
     * 更新总消费金额，总交易次数
     * @param int $user_id
     * @param float $ammount 
     * @param int $deal_times
     * @return bool 
     */
    public function update_ammount_and_deal_times($user_id, $ammount, $deal_times)
    {
        $user_id = (int)$user_id;
        if (empty($user_id)) {
            return false;
        }

        $this->insert(array("user_id" => $user_id), "IGNORE");
        $sql_str = "UPDATE pai_db.pai_user_data_tbl SET consume_ammount={$ammount},deal_times={$deal_times} WHERE user_id={$user_id}";
        db_simple_getdata($sql_str, TRUE, 101);

        return true;
    }

    /**
     * 更新评价平均分
     * @param int $user_id
     * @return bool   
     */
    public function update_comment_score($user_id)
    {
        $user_id = (int)$user_id;
        if (empty($user_id)) {
            return false;
        }
        $this->insert(array("user_id" => $user_id), "IGNORE");

        $sql_str = "select sum(overall_score)/count(*) as c from mall_db.mall_comment_buyer_tbl where to_user_id={$user_id};";
        $sum_comment = db_simple_getdata($sql_str, TRUE, 101);
        $comment_score = (float)$sum_comment['c'];

        $sql_str = "UPDATE pai_db.pai_user_data_tbl SET comment_score={$comment_score} WHERE user_id={$user_id}";
        db_simple_getdata($sql_str, TRUE, 101);

        return true;
    }

    /**
     * 获取用户信息
     * @param $user_id
     * @return array
     */
    public function get_user_data_info($user_id)
    {
        $user_id = (int)$user_id;
        $ret = $this->find("user_id={$user_id}");
        if ($ret) {
            $ret['deal_times'] = $ret['deal_times'] + $ret['old_deal_times'];
            $ret['consume_ammount'] = $this->change_format_number($ret['consume_ammount'] + $ret['old_consume_ammount']);
            $ret['comment_score'] = $ret['comment_score'] == 0 ? $ret['comment_score'] = 5.0 : ceil($ret['comment_score'] * 2) / 2;
        } else {
            //没数据时初始化数据
            $ret['user_id'] = $user_id;
            $ret['deal_times'] = 0;
            $ret['consume_ammount'] = 0;
            $ret['comment_score'] = 5.0;
        }
        return $ret;
    }

    private function change_format_number($number)
    {
        if ($number >= 1000) {
            return number_format($number / 1000, 1) . "k";
        } else {
            return round($number);
        }
    }
}