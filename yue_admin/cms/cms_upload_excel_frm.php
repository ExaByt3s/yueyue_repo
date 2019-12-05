<?php

if (empty($_REQUEST["issue_id"])) 
{
	$issue_id = $_REQUEST["issue_id"];
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>poco</title>
<link href="http://www1.poco.cn/css/main.css" rel="stylesheet" type="text/css">
<link href="./css/css.css" rel="stylesheet" type="text/css">
<style type="text/css">
	
</style>
<script language="javascript">
function feedback(element,msg)
{
	alert(msg);
	element.focus();
	return false;
}

function checkForm2(form)
{
	if(!form.inputExcel.value) return feedback(form.inputExcel,"请先选择上传文件");
	
	writeCookie2('bp_upload_size', form.size.value)
	return true;
}

function writeCookie2(name, value, hours)
{
  var expire = "";
  if(hours != null)
  {
    expire = new Date((new Date()).getTime() + hours * 3600000);
    expire = "; expires=" + expire.toGMTString();
  }
  document.cookie = name + "=" + escape(value) + expire;
}
</script>
</head>
<body style="text-align: left;background-color:transparent;margin:0;padding:0px 0px;">
	<?php
	srand(time());
	$rand_id = rand(1000,9999);
	?>
	<form name="upload_form" method="post" onsubmit="return checkForm2(this)" enctype="multipart/form-data" action="issue_import.php" id="myform">
	<input name="inputExcel" type="file" style="width:176px"/>
	<input type="hidden" name="act" value="import" />
	<input type="hidden" name="issue_id" value="<?php echo $issue_id;?>" />
	<input name="Submit" type="submit"  value="导入" />(数据不要超出100条)
	</form>
</body>
</html>