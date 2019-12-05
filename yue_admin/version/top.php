<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/admin.js?x"></script>
<link rel="stylesheet" type="text/css" href="../css/style.css">
<title>约约APP后台</title>
</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
	<div class="logo">
		<a href="http://www.poco.cn" target="_blank"><img src="../images/admin_logo.gif" width="180"></a>
	</div>
	<div class="nav f_r" style="display:none"> 
		<a href="javascript:void(0)">更新缓存</a> <i>|</i>
		<a href="http://www.poco.cn" target="_blank">官方网站</a> <i>|</i>&nbsp;&nbsp;
	</div>
	<div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！ </div>
	<div class="topmenu" id="top_tag">
		<ul>
			<li><span id="top_menu_1" class="current"><a href="#this">版本发布系统</a></span></li>
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