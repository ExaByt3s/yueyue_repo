<?php
ob_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="resources/js/jquery.min.js"></script>
<script type="text/javascript" src="resources/js/admin.js?x"></script>
<link rel="stylesheet" type="text/css" href="resources/css/style.css">
<title>约约APP后台</title>
</head>
<body style="background:#E2E9EA;overflow:hidden;">
<!--header-->
<div id="header" class="header">
	<div class="logo">
		<a href="http://www.poco.cn" target="_blank"><img src="resources/images/admin_logo.gif" width="180"></a>
	</div>
	<div class="nav f_r" style="display:none"> 
		<a href="javascript:void(0)">更新缓存</a> <i>|</i>
		<a href="http://www.poco.cn" target="_blank">官方网站</a> <i>|</i>&nbsp;&nbsp;
	</div>
	<div class="nav">&nbsp;&nbsp;&nbsp;&nbsp;欢迎你！
		<?php 
		echo get_user_nickname_by_user_id($yue_login_id);
		?>
		<i>|</i><a href="logout.php">[退出]</a> </div>
	<div class="topmenu" id="top_tag">
		<ul>
			<li <?php if (!in_array('pic_examine', $list_authority)) 
				{ echo "style='display:none;'";} ?>><span id="top_menu_1"><a href="http://yp.yueus.com/yue_admin_v2/audit/index.php">图片审核</a></span></li>
			<li <?php if (!in_array('text_examine', $list_authority)) 
				{ echo "style='display:none;'";} ?>><span id="top_menu_2"><a href="http://yp.yueus.com/yue_admin_v2/audit/index.php">文字审核</a></span></li>
			<li <?php if (!in_array('model', $list_authority)) 
				{ echo "style='display:none;'";} ?>><span id="top_menu_3"><a href="http://yp.yueus.com/yue_admin/model_audit/index.php">模特模块</a></span></li>
			<li <?php if (!in_array('cameraman', $list_authority)) 
				{ echo "style='display:none;'";} ?>><span id="top_menu_4"><a href="http://yp.yueus.com/yue_admin/cameraman_audit/">摄影师模块</a></span></li>
			<li <?php if (!in_array('model_audit', $list_authority)) 
				{ echo "style='display:none;'";} ?>><span id="top_menu_5"><a href="http://yp.yueus.com/yue_admin/model_audit/index.php">模特审核模块</a></span></li>
		</ul>
	</div>
	<div class="header_footer"></div>
</div>
<script>
switch_tab('top_tag','top_menu_','nav_','current',5);
</script>
<!--header-->
<?php
$_POCO_STAT_YUE_ADMIN_REPORT_HEADER = ob_get_contents();
ob_end_clean();
?>