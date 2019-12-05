<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="resources/js/jquery.min.js"></script>
<script type="text/javascript" src="resources/js/admin.js?x"></script>
<link rel="stylesheet" type="text/css" href="resources/css/style.css">
<title>工作监控报表后台</title>
</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
	<div class="logo">
		<a href="http://www.poco.cn" target="_blank"><img src="resources/images/admin_logo.gif" width="180"></a>
	</div>
	<div class="nav f_r" style="display:none"> 
		<a href="http://www.poco.cn" target="_blank">官方网站</a> <i>|</i>&nbsp;&nbsp;
	</div>
	<div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！
	<?php
		$nickname = get_user_nickname_by_user_id($yue_login_id);
		echo $nickname;
	?>	<i>|</i><a href="logout.php">[退出]</a> </div>
	<div class="topmenu" id="top_tag">
		<ul>
			<li><span id="top_menu_1" class="current"><a href="#this">工作监控报表</a></span></li>
			<li><span id="top_menu_2"><a href="#this">信息报表</a></span></li>
			<li><span id="top_menu_3"><a href="#this">约约报表</a></span></li>
			<li><span id="top_menu_4"><a href="#this">工作人监控报表</a></span></li>
			<li><span id="top_menu_5"><a href="#this">实时报表</a></span></li>
			<li><span id="top_menu_6"><a href="#this">摄影师排行榜</a></span></li>
			<li><span id="top_menu_7"><a href="#this">模特排行榜</a></span></li>
            <li><span id="top_menu_8"><a href="#this">订单报表</a></span></li>
		</ul>
	</div>
	<div class="header_footer"></div>
</div>
<script>
switch_tab('top_tag','top_menu_','nav_','current',8);
</script>
<!--header-->
<?php
$_POCO_STAT_YUE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>