<?php
include_once('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$cms_obj        = new cms_system_class();

//1Ԫ������
$one_pai = $cms_obj->get_record_list_by_issue_id(false, 279, "0,4","place_number ASC", null, "");


//�����ģ����ڴ��죩
$waipai1 = $cms_obj->get_record_list_by_issue_id(false, 280, "0,4","place_number ASC", null, "");


//�����ģ�С���£�
$waipai2 = $cms_obj->get_record_list_by_issue_id(false, 281, "0,4","place_number ASC", null, "");



?>