<?php
include_once ('/disk/data/htdocs232/poco/pai/mobile/poco_pai_common.inc.php');



if(!in_array($yue_login_id,array(100000,100038)))
{
	die('��Ȩ��');
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>�̼�</title>
<script type="text/javascript" src="http://www.yueus.com/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="http://www.yueus.com/js/jquery.md5.js"></script>
</head>

<body>
<form enctype="multipart/form-data" action="http://sendmedia-w.yueus.com:8078/icon.cgi" method="post" target="_blank">
<input name="opus" type="file" id="opus" value="" />

<br /><br />
�̼��û�ID: <input type="text" name="user_id" id="user_id" value="" /> <input type="submit" name="button" id="button" value="�ύ" />
<br /><br />
��ȷ���û�ID����һ���ύ�޷��ָ�
<input type="hidden" name="params" id="params" value='' />



</form>

<script language="javascript">
$(document).ready(function(){
	$("form").submit(function(e){
		var user_id = $("#user_id").val();
		var opus = $("#opus").val();
		
		if(!opus)
		{
			alert('��ѡ��ͼƬ');
			return false;	
		}
		
		if(!user_id)
		{
			alert('�û�ID����Ϊ��');
			return false;	
		}
		
		var hash = $.md5(user_id+"YUE_PAI_POCO!@#456");
		$("#params").val('{"role":"yueseller","poco_id":"'+user_id+'","hash":"'+hash+'"}');
	});
});
</script>
</body>
</html>
