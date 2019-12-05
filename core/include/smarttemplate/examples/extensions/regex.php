<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("regex.html");
 
	$page->assign( 'FILE',  'Example.html' );
 
    $page->output();

?>