<?php
/** POCO 数据库错误(繁忙)管理后台，主体列表窗口
* @author  涂贵全
*/

include_once 'common.inc.php';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>网站sql错误(繁忙)监控</title>
</head>
<frameset cols="20%,*" border="0" framespacing="1" frameborder="no">
  <frame src="err_day_list.php" name="left_Frame" scrolling="auto" style="border-right:1px solid #DDD;">
  <frame src="" name="right_Frame" scrolling="yes">
</frameset>
<noframes>
<body>
您的浏览器不支持框架
</body>
</noframes>
</html>