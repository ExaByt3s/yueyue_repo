<?php
class pai_tongji_class extends POCO_TDG
{
    public function __construct()
    {
        $this->setServerId ( 22 );
        $this->setDBName ( 'yueyue_log_tmp_db' );

        $table_name = 'yueyue_interface_request_log_' . date('Ym');
        $this->setTableName ($table_name);
    }

    public function add_tongji_log($img_url, $link_url, $interface_name, $location_id = 0, $user_id = 0, $remark = '')
    {
        $add_table_name = $this->_db_name . "." . $this->_tbl_name;
        $add_time = time();
        $sql_str = "INSERT INTO " . $add_table_name . "(img_url, link_url, interface_name, location_id, user_id, add_time, remark)
                    VALUES (:x_img_url, :x_link_url, :x_interface_name, :x_location_id, :x_user_id, $add_time, :x_remark)";
        sqlSetParam($sql_str, 'x_img_url', $img_url);
        sqlSetParam($sql_str, 'x_link_url', $link_url);
        sqlSetParam($sql_str, 'x_interface_name', $interface_name);
        sqlSetParam($sql_str, 'x_location_id', $location_id);
        sqlSetParam($sql_str, 'x_user_id', $user_id);
        sqlSetParam($sql_str, 'x_remark', $remark);
        db_simple_getdata($sql_str, TRUE, $this->_server_id);
    }

    public function get_db_table_name()
    {
        return $this->_db_name . "." . $this->_tbl_name;
    }
}
?>