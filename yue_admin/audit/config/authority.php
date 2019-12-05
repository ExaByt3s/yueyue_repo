<?php
//有访问权限的POCOID
include_once('/disk/data/htdocs232/poco/pai/yue_admin/yue_access_control.inc.php');
$list_authority = array('pic_examine', 'text_examine', 'model', 'cameraman', 'model_audit', 'organization');
$list_authority = yueyue_admin_check('audit', $list_authority, 1);
//exit;
	function check_authority($arr)
	{
		$list_check = yueyue_admin_check('audit', $arr, 1);
		if(empty($list_check) || !is_array($list_check))
		{
			echo "没有权限";
			exit;
		}
	}
?>