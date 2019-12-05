<?php
/**
 * Created by PhpStorm.
 * User: yaohua_he
 * Date: 2015/5/26
 * Time: 10:15
 */

include_once ("../site_stat_common.inc.php");

$yueyue_report_obj = new yueyue_mobile_reprt_class();
$yueyue_report_obj->get_mobile_list(122);

?>