<?php
//�з���Ȩ�޵�POCOID
include_once('/disk/data/htdocs232/poco/pai/yue_admin/yue_access_control.inc.php');
$list_authority = array('organization');
check_authority($list_authority);
//exit;
	function check_authority($arr)
	{
		$list_check = yueyue_admin_check('audit', $arr, 1);
		if(empty($list_check) || !is_array($list_check))
		{
			/*echo "<script>window.alert('û��Ȩ��');location.href='../index.php'</script>";
			exit;*/
			die("û��Ȩ��");
		}
	}
?>