<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("vardump.html");
 
 	$arr =  array('Red','Green','Blue');
	$page->assign( 'COLORS',  $arr );
 
    $page->output();

?>