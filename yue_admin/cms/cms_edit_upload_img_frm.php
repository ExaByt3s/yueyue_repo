<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=gb2312" />
<title>poco</title>
<link href="http://www1.poco.cn/css/main.css" rel="stylesheet" type="text/css">
<link href="./css/css.css" rel="stylesheet" type="text/css">
</head>
<body style="text-align: left;background-color:transparent;margin:0;padding:0px 0px;">
<script language="javascript">
function feedback(element,msg)
{
	alert(msg);
	element.focus();
	return false;
}

function checkForm(form)
{
	if(!form.opus.value) return feedback(form.opus,"请先选择上传的图片");
	
	writeCookie('bp_upload_size', form.size.value)
	return true;
}

function writeCookie(name, value, hours)
{
  var expire = "";
  if(hours != null)
  {
    expire = new Date((new Date()).getTime() + hours * 3600000);
    expire = "; expires=" + expire.toGMTString();
  }
  document.cookie = name + "=" + escape(value) + expire;
}

function CopyInBoard(hahaUrl,note)
{
	note = note?note:'网址复制成功啦!';
	window.clipboardData.setData('text',hahaUrl)
	alert('网址复制成功啦');
}
</script>
<?php

if ($_GET["img"]) 
{
	$img_url = base64_decode($_GET["img"]);
	die("
	<span><a href='{$img_url}' target='_blank' style=color:blue;text-decoration:underline;>{$img_url}</a> <input value='复制' type=button onclick=CopyInBoard('{$img_url}');> <a href='cms_edit_upload_img_frm.php'>重新上传</a><span>
	");
}

	srand(time());
	$rand_id = rand(1000,9999);
	?>
	<form name="upload_form" method="post" onsubmit="return checkForm(this)" enctype="multipart/form-data" action="http://imgup-s.poco.cn/ultra_upload_service/yue_cms_upload_act.php?upload_count=<?php echo $upload_count ?>&rd=<?php echo $rand_id?>">
	上传图片：<input name="callback_url" type="hidden" value="<?php echo $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];?>">
	<input name="opus" type="file" size="18" /> <input name="Submit" type="submit"  value="上 传" /> 大小：<input type="text" size="2" name="size" value="<?php echo ($_REQUEST["img_size"])?$_REQUEST["img_size"]:$_COOKIE["bp_upload_size"];?>">
	</form>
</body>
</html>