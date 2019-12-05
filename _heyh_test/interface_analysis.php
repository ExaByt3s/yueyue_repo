<?php
/**
 * Created by PhpStorm.
 * User: heyaohua
 * Date: 2015/11/16
 * Time: 18:15
 */
ignore_user_abort(true);
set_time_limit(36000);
include_once "../poco_app_common.inc.php";

$file_android_str   = "http://yp.yueus.com/protocol/log/1511_0be0ec06_2/2015-11-01_yuepai_android.log";
$file_iphone_str    = "http://yp.yueus.com/protocol/log/1511_0be0ec06_2/2015-11-01_yuepai_iphone.log";

for($i=1;$i<16;$i++)
{
    $file_android_str   = "http://yp.yueus.com/protocol/log/1511_0be0ec06_2/2015-11-" . sprintf('%02d', $i)  . "_yuepai_android.log";
    $handle = fopen($file_android_str, 'r');
    while(!feof($handle))
    {
        $line =  fgets($handle, 1024);
        $line_array = explode("^$^", $line);
        var_dump($line_array);
        $result = unserialize($line_array[1]);
        $param = serialize($result['param']);


        $sql_str = "INSERT INTO yueyue_interface_db.interface_analysis_log_tbl_201511(log_time, version, os_type, ctime, app_name, is_enc, sign_code, param, unique_sign, uri)
                VALUES ('{$line_array[0]}', '{$result[version]}', '{$result[os_type]}', '{$result[ctime]}', '{$result[app_name]}', '{$result[is_enc]}', '{$result[sign_code]}', '{$param}', '{$result[unique_sign]}', '{$result[uri]}')";
        echo $sql_str . "<BR>";
        db_simple_getdata($sql_str, TRUE, 22);
    }
    fclose($handle);

    $file_iphone_str   = "http://yp.yueus.com/protocol/log/1511_0be0ec06_2/2015-11-" . sprintf('%02d', $i)  . "_yuepai_iphone.log";
    echo $file_iphone_str;
    $handle = fopen($file_iphone_str, 'r');
    while(!feof($handle))
    {
        $line =  fgets($handle, 1024);
        $line_array = explode("^$^", $line);
        var_dump($line_array);
        $result = unserialize($line_array[1]);
        $param = serialize($result['param']);


        $sql_str = "INSERT INTO yueyue_interface_db.interface_analysis_log_tbl_201511(log_time, version, os_type, ctime, app_name, is_enc, sign_code, param, unique_sign, uri)
                VALUES ('{$line_array[0]}', '{$result[version]}', '{$result[os_type]}', '{$result[ctime]}', '{$result[app_name]}', '{$result[is_enc]}', '{$result[sign_code]}', '{$param}', '{$result[unique_sign]}', '{$result[uri]}')";
        echo $sql_str . "<BR>";
        //exit();
        db_simple_getdata($sql_str, TRUE, 22);
        //exit();
    }
    fclose($handle);
}
