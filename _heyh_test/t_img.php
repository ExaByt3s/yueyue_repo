<?php
include_once('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');
$sql_str = "SELECT 
  `user_id`
FROM
  `pai_db`.`pai_user_tbl` 
  WHERE role = 'model' AND nickname NOT LIKE '%手机用户%' AND nickname <> ''";
  
$result = db_simple_getdata($sql_str, FALSE, 101);
foreach($result AS $key=>$val)
{
    echo $val[user_id] . ":" . "<img src=http://yue-icon.yueus.com//10/" . $val[user_id] . "_100.jpg />" . "<BR>";
}
?>