<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "wrongful.htm"
	)
);
$tpl->define_block(MAIN,MSG_LIST);

if($_POST["addtype"]=="add")
{
	mysql_query("INSERT INTO black_word_tbl (black_word,input_time) VALUES ('".$_POST["addwrongful"]."',Now())",$conn);
	echo "<script>alert('增加成功！');</script>";
}
if($_GET["gotype"]=="del")
{
	mysql_query("DELETE FROM black_word_tbl WHERE id='".$_GET["delid"]."'",$conn);
	echo "<script>alert('删除成功！');</script>";
}

$pagesize=50;

$conn = mysql_connect("121.9.211.159","yy-poco","poco,.site-2004");
mysql_select_db("java_black_leach_db",$conn);

$result = mysql_query("select count(*) from black_word_tbl",$conn);
list($totalsum)=mysql_fetch_row($result);
$pagebar = new PageBar($totalsum,$pagesize,"text_msg");
$offset = $pagebar->offset();
$res = mysql_query("select id,black_word,input_time from black_word_tbl order by input_time desc limit ".$offset.",".$pagesize,$conn);
while(list($id,$black_word,$input_time)=mysql_fetch_row($res))
{
	$tpl->assign(
		array(
			DELID			=> $id,
			BLACK_WORD		=> $black_word,
			TITLENAME		=> $input_time
		)
	);
	$tpl->parse_block(MSG_LIST);
}

mysql_close($conn);

$top_bar = $pagebar->pre_group();
$up_bar = $pagebar->pre_page();
$num_bar = $pagebar->num_bar();
$next_bar = $pagebar->next_page();
$end_bar = $pagebar->next_group();


$tpl->assign("TOP_BAR",$top_bar);
$tpl->assign("UP_BAR",$up_bar);
$tpl->assign("NUM_BAR",$num_bar);
$tpl->assign("NEXT_BAR",$next_bar);
$tpl->assign("END_BAR",$end_bar);

$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>