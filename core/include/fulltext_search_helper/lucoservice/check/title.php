<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "title.htm"
	)
);
$tpl->define_block(MAIN,TITLE_LIST);

$charset="GBK";//"UTF-8";
$conn=mysql_connect("121.9.211.196","dev_java",'!A@S#D$F');
mysql_select_db("appserver",$conn);
mysql_query("SET character_set_connection=".$charset.", character_set_results=".$charset.", character_set_client=binary");

if($_GET["type"]=="close")
{
	mysql_query("UPDATE app_topic_title SET valid=0 WHERE id='".$_GET["uid"]."'");
}
if($_GET["type"]=="open")
{
	mysql_query("UPDATE app_topic_title SET valid=1 WHERE id='".$_GET["uid"]."'");
}

$res1 = mysql_query("SELECT count(*) FROM app_topic_title");
list($totalsum)=mysql_fetch_array($res1);
mysql_fetch_row($res1);
$pagesize=30;
$page=ceil($totalsum/$pagesize);
$pagebar = new PageBar($totalsum,$pagesize);
$offset=$pagebar->offset();

$res = mysql_query("SELECT id,title,valid FROM app_topic_title order by id desc limit ".$offset.",".$pagesize);
while(list($id,$title,$valid)=mysql_fetch_array($res))
{
	if($valid==1)
	{
		$tpl->assign(
			array(
				UIDS	=> $id,
				TITLE	=> $title,
				GOURL	=> "<a href='main.php?action=title&type=close&uid=".$id."'><font color='red'>ÆÁ±Î</font></a>"
			)
		);
	}
	else
	{
		$tpl->assign(
			array(
				UIDS	=> $id,
				TITLE	=> $title,
				GOURL	=> "<a href='main.php?action=title&type=open&uid=".$id."'><font color='bule'>»Ö¸´</font></a>"
			)
		);
	}
	$tpl->parse_block(TITLE_LIST);
}
mysql_fetch_row($res);
mysql_close($conn);

$top_bar = $pagebar->pre_group();
$up_bar = $pagebar->pre_page();
$num_bar = $pagebar->num_bar();
$next_bar = $pagebar->next_page();
$end_bar = $pagebar->next_group();

$tpl->assign("TOTAL_SUM",$totalsum);
$tpl->assign("PAGE",$page);
$tpl->assign("TOP_BAR",$top_bar);
$tpl->assign("UP_BAR",$up_bar);
$tpl->assign("NUM_BAR",$num_bar);
$tpl->assign("NEXT_BAR",$next_bar);
$tpl->assign("END_BAR",$end_bar);

$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>