<?php
include_once 'common.inc.php';
$list = array ('1', '2', '3' ,'7');
$auth = yueyue_admin_check ( 'expand_category', $list, 1 );
print_r($auth);
?>