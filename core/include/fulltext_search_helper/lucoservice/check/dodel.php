<?
/*
include_once("include/FastTemplate.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "dodel.htm"
	)
);
$tpl->define_block(MAIN,MSG_LIST);
*/
$text=$_REQUEST["text_msg"];
$page=$_REQUEST["text_page"];
$aa=$_REQUEST["Senduser"];
print_r($aa);
$bbb = split("_-",$aa);
$len=count($bbb)-1;
for ($i = 0; $i < $len; $i++)
{

}
//for ($i = 0; $i < $len; $i++) {
//	print_r($aa[$i]); 
//	$tpl->assign(
//		GOURL	=> urlencoude($bbb[$i])
//	);
//	$tpl->parse_block(MSG_LIST);
//}
//echo '</form>';
//echo '<script language="javascript">';
//echo 'function gosubmit()
//{
//	document.form.submit();
//}
//';
//echo '</script>';
//echo "«Â¿ÌÕÍ±œ";
//header("Location: http://my.poco.cn/act/admin/act_admin_fulltextsearch_del_notifier.php?url_arr=urlencoude('.$bbb.')");
//echo '<a href="http://my.poco.cn/act/admin/act_admin_fulltextsearch_del_notifier.php?url_arr=urlencoude('.$bbb.')" target="_bank">';
//echo '<script language="javascript">window.location="main.php?action=checkdel&text_msg='.$text.'&text_page='.$page.'";</script>';
?>