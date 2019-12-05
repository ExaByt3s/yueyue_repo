<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("number.html");

	$page->assign( 'SUM',  rand(100000, 9999999999) / 10 );

    $page->output();

?>