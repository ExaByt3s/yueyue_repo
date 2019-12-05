<?php
 
    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("elseif.html");
 
	$page->assign( 'usergroup',  'INTERNAL' );
 
    $page->output();
 
?>