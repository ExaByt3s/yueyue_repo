<?php
ob_start();
include_once 'common.inc.php';
if($oa_role=='operate')
{
	$top_role = '运营';
}
elseif($oa_role=='expand')
{
	$top_role = '拓展';
}
elseif($oa_role=='financial')
{
	$top_role = '财务';
}
elseif($oa_role=='admin')
{
	$top_role = '管理员';
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/admin.js?x"></script>
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>约约OA系统</title>
</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
	<div class="logo">
		<a href="http://www.poco.cn" target="_blank"><img src="images/admin_logo.gif" width="180"></a>
	</div>
	
	<div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！<?php echo get_user_nickname_by_user_id($yue_login_id);?>	<i>|</i> [<?php echo $top_role;?>] &nbsp;&nbsp;&nbsp;&nbsp;<a href='logout.php'>退出系统</a></div>
	<div class="topmenu" id="top_tag">
		<ul>
			<li><span id="top_menu_1" class="current"><a href="#this">OA</a></span></li>
		</ul>
	</div>
	<div class="header_footer"></div>
</div>
<script>
switch_tab('top_tag','top_menu_','nav_','current',4);
</script>
<!--header-->
<?php
$_POCO_STAT_YUE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>