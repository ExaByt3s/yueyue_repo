<?php 
	include_once 'common.inc.php';
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta http-equiv="Content-Type" content="text/html; charset=GBK">
<script type="text/javascript" src="../resources/js/jquery.min.js"></script>
<link rel="stylesheet" type="text/css" href="../resources/css/style.css">
<title>约约APP后台</title>
<style type="text/css">
	.mainnav_title{ display:none;}
	h1 { height:30px;line-height:30px;font-size:14px;padding-left:15px;background:#EEE;border-bottom:1px solid #ddd;border-right:1px solid #ddd;overflow:hidden;zoom:1;margin-bottom:10px;}
	h1 b {color:#3865B8;}
	h1 span {color:#ccc;font-size:10px;margin-left:10px;}
	#Profile{ width:48%; height:191px; float:left;margin:5px 15px 0 0;}
	#system {width:48%;float:left;margin:5px 15px 0 0;}
	.list ul{ border:1px #ddd solid;  overflow:hidden;border-bottom:none;}
	.list ul li{ border-bottom:1px #ddd solid; height:26px;  overflow:hidden;zoom:1; line-height:26px; color:#777;padding-left:5px;}
	.list ul li span{ display:block; float:left; color:#777;width:100px;}
	#sitestats {width:48%; height:191px; float:left;margin:5px 0  0 0;overflow:hidden;}
	#sitestats div {_width:99.5%;border:1px solid #ddd;overflow:hidden;zoom:1;}
	#sitestats ul {overflow:hidden;zoom:1;width:102%;padding:2px 0 0 2px;_padding:1px 0 0 1px;height:132px;}
	#sitestats ul li {float:left;height:44px; float:left; width:16.1%;_width:16.3%;text-align:center;border-right:1px solid #fff;border-bottom:none;}
	#sitestats ul li b {float:left;width:100%;height:21px;line-height:22px;  background:#EFEFEF;color:#777;font-weight:normal;}
	#sitestats ul li span {float:left;width:100%;color:#3865B8;background:#F8F8F8;height:21px;line-height:21px;overflow:hidden;zoom:1;}
	#yourphpnews {width:48%;float:left;margin:5px 15px 0 0;}
</style>
</head>
<body>
<div class="mainbox">
	<div id="Profile" class="list">
		<h1><b>个人信息</b><span>Profile&nbsp; Info</span></h1>
		<ul>
			<li><span>管理员:</span>
			<?php 
		    echo get_user_nickname_by_user_id($yue_login_id);
		    ?>
		</li>
			<li><span>时间:</span>
			<?php
			    echo date('Y-m-d H:i:s', time());
			 ?>
			 </li>
			<li><span>登陆IP:</span>
			<?php 
				echo get_client_ip();
			 ?>
			</li>		
			</ul>
	</div>
</div>
</body>
</html>