<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("stringformat.html");
 
	$page->assign( 'SUM',  25 );
 
    $page->output();

?>