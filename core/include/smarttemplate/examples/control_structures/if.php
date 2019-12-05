<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("if.html");
 
	$page->assign( 'username',   'John Doe' );
	$page->assign( 'usergroup',  'ADMIN' );
	$page->assign( 'picture',    '' );
 
    $page->output();

?>