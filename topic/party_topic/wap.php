<?php
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj        = new cms_system_class();

//1元随心拍
$one_pai = $cms_obj->get_record_list_by_issue_id(false, 279, "0,4","place_number ASC", null, "");


//大外拍（室内创造）
$waipai1 = $cms_obj->get_record_list_by_issue_id(false, 280, "0,4","place_number ASC", null, "");


//大外拍（小清新）
$waipai2 = $cms_obj->get_record_list_by_issue_id(false, 281, "0,4","place_number ASC", null, "");



?>