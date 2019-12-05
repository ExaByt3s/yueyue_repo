<?php
include_once("../../poco_app_common.inc.php");

$phone = $_GET['phone'];

$pai_sms_obj = POCO::singleton('pai_sms_class');
$ret = $pai_sms_obj->send_phone_reg_verify_code($phone);
?>
