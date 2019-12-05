<?php
ob_start();
include_once 'common.inc.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/admin.js?x"></script>
<link rel="stylesheet" type="text/css" href="css/style.css">
<title>供应商审核后台</title>

</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
	<div class="logo">
		<a href="http://www.yueus.com" target="_blank"><img src="images/admin_logo.gif" width="180"></a>
	</div>
	
	<div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！<?php echo get_user_nickname_by_user_id($yue_login_id);?> &nbsp;&nbsp;( <?=$yue_login_id?> )&nbsp;&nbsp;<a href='logout.php'>退出系统</a></div>
	<div class="topmenu" id="top_tag">
		<ul>
			<li><span id="top_menu_4" role-data="4"><a href="#this">用户管理</a></span></li>
            <li><span id="top_menu_1" role-data="1"><a href="#this">商家管理</a></span></li>
            <li><span id="top_menu_2" role-data="2" class="current"><a href="#this">商品管理</a></span></li>
			<li><span id="top_menu_3" role-data="3"><a href="#this">订单管理</a></span></li>

		</ul>
	</div>
	<div class="header_footer"></div>
</div>
<script>
	switch_tab2('top_tag','top_menu_','nav_','current',4);
	$(function() {
		$(".nav_info").find("a").click(function () {
			$(".nav_info div").removeClass('on');
			$(".nav_info span").removeClass('on');
			$(this).parent().addClass('on');
		});
	});
</script>
<!--header-->
<?php
$_POCO_STAT_YUE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>