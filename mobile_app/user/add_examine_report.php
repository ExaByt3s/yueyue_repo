<?php
include_once('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/MobileAPP/key.inc.php');

$to_informer = $_INPUT['to_informer'];
$by_informer = $_INPUT['by_informer'];
$data        = iconv('UTF-8', 'gbk', json_decode($_INPUT['data']));
$cause       = iconv('UTF-8', 'gbk', json_decode($_INPUT['cause']));
$input_str   = iconv('UTF-8', 'gbk', serialize($_INPUT));
$add_time    = date('Y-m-d H:i:s');

$sql_str = "INSERT INTO pai_log_db.pai_examine_inform_tbl(to_informer, by_informer, data_str, cause_str, input_str, add_time)
            VALUES ('{$to_informer}', '{$by_informer}', '{$data}', '{$cause}', '{$input_str}', '{$add_time}')";

db_simple_getdata($sql_str, TRUE, 101);

$code = 0;
$msg  = 'Õý³£';

json_msg($code, $msg);
?>