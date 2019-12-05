<?php
include_once("../../poco_app_common.inc.php");

//$sql_str = "SELECT * FROM pai_db.pai_sign_up_tbl";
//$result = db_simple_getdata($sql_str, FALSE, 101);
//print_r($result);
$tpl = new SmartTemplate("model.tpl.html");
$tpl->output();
?>