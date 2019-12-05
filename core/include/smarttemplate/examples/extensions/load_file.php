<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("load_file.html");

	$page->assign( 'TITLE',  'Welcome' );

    $page->output();

?>