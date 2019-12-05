<?
echo '<meta http-equiv="Content-Type" content="text/html; charset=gb2312">';
if(!$_GET["action"]){
	include_once("include/FastTemplate.php");
	$tpl = new FastTemplate("templates");
	$tpl->define(
		array(
			MAIN => "main.htm"
		)
	);
	$tpl->assign("MAIN","");
	$tpl->parse(tpl_main,"MAIN");
	$tpl->FastPrint();
}
else if($_GET["action"]=="checkdel"){
	require("checkdel.php");
}
else if($_GET["action"]=="bbscheck"){
	require("bbscheck.php");
}
else if($_GET["action"]=="gongancheck"){
	require("gongancheck.php");
}
else if($_GET["action"]=="dodel"){
	require("dodel.php");
}
else if($_GET["action"]=="group"){
	require("group.php");
}else if($_GET["action"]=="title"){
	require("title.php");
}
else if($_GET["action"]=="msgcontent"){
	require("msgcontent.php");
}
else if($_GET["action"]=="wrongful"){
	require("wrongful.php");
}
?>