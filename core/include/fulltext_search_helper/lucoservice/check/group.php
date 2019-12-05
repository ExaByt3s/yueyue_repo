<?
include_once("include/FastTemplate.php");
include_once("include/pagebar.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "group.htm"
	)
);
$tpl->define_block(MAIN,GROUP_LIST);

$charset="GBK";//"UTF-8";
$conn=mysql_connect("121.9.211.196","dev_java",'!A@S#D$F');
mysql_select_db("appserver",$conn);
mysql_query("SET character_set_connection=".$charset.", character_set_results=".$charset.", character_set_client=binary");

if($_GET["type"]=="up")
{
	mysql_query("UPDATE app_compose SET valid=1 WHERE id='".$_GET["uid"]."'");
	mysql_query("UPDATE app_grouplist SET valid=1 WHERE groupid='".$_GET["uid"]."' AND userid='".$_GET["userid"]."'");
}

$res = mysql_query("SELECT id,name,introduction,creatid FROM app_compose WHERE valid=0");
while(list($id,$name,$introduction,$creatid)=mysql_fetch_array($res))
{
	$tpl->assign(
		array(
			UIDS	=> $id,
			NAME	=> $name,
			INTRODUCTION	=> $introduction,
			CREATID	=> $creatid
		)
	);
	$tpl->parse_block(GROUP_LIST);
}
mysql_fetch_row($res);
mysql_close($conn);

$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
?>