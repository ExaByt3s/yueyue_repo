<?
include_once("include/FastTemplate.php");
$tpl = new FastTemplate("templates");
$tpl->define(
	array(
		MENU => "menu.htm"
	)
);

$tpl->parse(tpl_main,"MENU");
$tpl->FastPrint();
exit;
?>