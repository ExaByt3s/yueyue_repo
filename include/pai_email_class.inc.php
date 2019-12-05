<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/4/21
 * Time: 20:30
 */
class pai_email_class  extends POCO_TDG
{
    /**
     * ¹¹Ôìº¯Êý
     *
     */
    public function __construct()
    {
        $this->setServerId ( 101 );
        $this->setDBName ( 'pai_db' );
        $this->setTableName ( 'pai_dmid_for_tbl' );
    }

    public function send_email($email_add, $email_title, $email_html)
    {
        $sql_str = "INSERT INTO pai_email_db.email_send_queue_tbl(email_add, email_title, email_html)
                    VALUES (:x_email_add, :x_emial_title, :x_email_html)";
        sqlSetParam($sql_str, 'x_email_add', $email_add);
        sqlSetParam($sql_str, 'x_emial_title', $email_title);
        sqlSetParam($sql_str, 'x_email_html', $email_html);
        db_simple_getdata($sql_str, TRUE, 101);
    }
}