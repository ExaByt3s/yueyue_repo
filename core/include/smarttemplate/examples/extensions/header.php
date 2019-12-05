<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("header.html");

	$page->assign( 'TITLE',  'SVG Template Demo:' );

    $page->output();

?>