<?php 
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');

$sql = "update pai_db.pai_activity_code_tbl set is_checked=1 where user_id=113740";
db_simple_getdata($sql,false,101);

echo "全部扫码成功";

?>