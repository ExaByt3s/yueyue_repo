<?
include_once("include/FastTemplate.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MAIN => "index.htm",
		MENU => "menu.htm"
	)
);

$tpl->assign("TO_GO","");
$tpl->parse("MENU","MENU");
$tpl->parse(tpl_main,"MAIN");
$tpl->FastPrint();
exit;
?>