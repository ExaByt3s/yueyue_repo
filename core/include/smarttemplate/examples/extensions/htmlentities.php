<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("htmlentities.html");

	$page->assign( 'BUTTON',  'NEXT >>' );

    $page->output();

?>