<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$fulltext_obj = POCO::singleton('pai_fulltext_class');

$sql_str = "SELECT * FROM pai_db.pai_fulltext_act_tbl WHERE `status` =0";
$result = db_simple_getdata($sql_str, FALSE, 101);

foreach($result AS $key=>$val)
{
    if($fulltext_obj->fulltext_check_id($val['user_id']) == 1)
    {
        $fulltext_obj->cp_data_by_user_id($val['user_id']);
    
        $sql_str = "UPDATE pai_db.pai_fulltext_act_tbl SET status =1 WHERE id=$val[id]";
        db_simple_getdata($sql_str, TRUE, 101);
    }else{
         $fulltext_obj->cp_data_by_user_id($val['user_id']);
        $sql_str = "UPDATE pai_db.pai_fulltext_act_tbl SET status =2 WHERE id=$val[id]";
        db_simple_getdata($sql_str, TRUE, 101);
    }
    
}

if(date('H:i') == '03:01')
{
    $fulltext_obj->cp_data();
}

?>