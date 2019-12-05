<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("substr.html");
 
	$page->assign( 'HEADLINE',  'My Title' );
 
    $page->output();

?>