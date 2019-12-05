<?php
$db_host    = '113.107.204.237';
$db_user    = 'pic_examine_user';
$db_pwd     = 'eXAmine12,d(3';
$db_name    = 'pai_log_db';

$user_id    = $_REQUEST['user_id'];
$img_url    = $_REQUEST['img_url'];
$add_time   = date('Y-m-d H:i:s');

if(!empty($user_id) && !empty($img_url))
{
    $con = mysql_connect($db_host, $db_user, $db_pwd);
    if (!$con)
    {
        die('Could not connect: ' . mysql_error());
    }
    
    $db_selected = mysql_select_db($db_name, $con);
    if (!$db_selected)
    {
      die ("Can\'t use test_db : " . mysql_error());
    }
    
    $sql_str = "INSERT INTO pic_examine_log(user_id, img_url, add_time) 
                VALUES ('{$user_id}', '{$img_url}', '{$add_time}')";
    if(!mysql_query($sql_str, $con))
    {
        die (mysql_error());
    }
    
    mysql_close($con);    
}
?>