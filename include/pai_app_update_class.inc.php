<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/7/28
 * Time: 18:43
 */
class pai_app_update_class extends POCO_TDG
{
    public function __construct()
    {
        $this->setServerId ( 1 );
        $this->setDBName ( 'pai_db' );
        $this->setTableName ( 'event_date_tbl' );
    }

    public function check_user_is_update($user_id)
    {
        $user_id = (int)$user_id;

        if(empty($user_id))                                 return FALSE;
        if(!$this->check_role_by_user_id($user_id))          return FALSE;
        if($this->check_date_tbl_by_user_id($user_id))       return FALSE;
        if($this->check_event_tbl_by_user_id($user_id))      return FALSE;
        if($this->check_pay_tbl_by_user_id($user_id))        return FALSE;

        return TRUE;
    }

    public function get_poco_id_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if($user_id)
        {

        }
    }

    public function check_pay_tbl_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if($user_id)
        {
            $sql_str = "SELECT COUNT(*) AS C FROM `ecpay_db`.`pai_date_tbl` WHERE user_id=$user_id AND status NOT IN (7,8);";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if($result['C'] > 0) return TRUE;

            $sql_str = "SELECT COUNT(*) AS C FROM `ecpay_db`.`pai_trade_tbl` WHERE user_id=$user_id AND channel_module='yuepai' AND trade_type='out' AND status NOT IN (7,8);";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if($result['C'] > 0) return TRUE;
        }
        return FALSE;
    }

    public function check_date_tbl_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if($user_id)
        {
            $sql_str = "SELECT COUNT(*) AS C FROM event_db.event_date_tbl WHERE from_date_id = $user_id AND date_status = 'wait' AND pay_status=1";
            $result = db_simple_getdata($sql_str, FALSE, 1);
            if($result['C'] > 0) return TRUE;
        }

        return FALSE;
    }

    public function check_event_tbl_by_user_id($user_id)
    {
        $user_id = (int)$user_id;
        if($user_id)
        {
            $sql_str = "SELECT L.event_id
                        FROM event_db.event_date_tbl AS L, event_db.event_details_tbl AS R
                        WHERE L.event_id=R.event_id AND L.from_date_id = $user_id AND R.event_status = '0'";
            $result = db_simple_getdata($sql_str, FALSE, 1);
            if(count($result) > 0) return TRUE;
        }

        return FALSE;
    }

    public function check_role_by_user_id($user_id)
    {
        $return = FALSE;
        if((int)$user_id)
        {
            $sql_str = "SELECT role FROM `pai_db`.`pai_user_tbl` WHERE user_id = $user_id";
            $result = db_simple_getdata($sql_str, TRUE, 101);
            if($result['role'] == 'cameraman') $return = TRUE;
        }

        return $return;

    }
}