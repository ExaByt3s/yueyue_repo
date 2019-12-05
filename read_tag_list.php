<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/3/30
 * Time: 22:07
 */
exit();
include_once "poco_app_common.inc.php";

$file = fopen('tag_list.txt', r);
while(!feof($file))
{
    $line = fgets($file);
    $array_val = explode(",", $line);
    $user_id = 0;
    $insert_val = array();
    foreach($array_val AS $key=>$val)
    {
        if($key == 0)
        {
            $user_id=$val;
        }else{
            if($val) $insert_val[] = trim($val);
        }
    }
    if($insert_val)
    {
        foreach($insert_val AS $val)
        {
            if($val)
            {
                $sql_str = "INSERT INTO pai_user_library_db.model_label(uid, label) VALUES ($user_id, '{$val}')";
                echo $sql_str . "<BR>";
                db_simple_getdata($sql_str, TRUE, 101);
            }

        }
        //exit();
    }
}
fclose($file);


?>
