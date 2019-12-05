<?php

	require_once "class.smarttemplate.php";

	$page = new SmartTemplate("Hello_World.html");
	$page->assign('TITLE', 'Hello World!');
	$page->output();

?>
