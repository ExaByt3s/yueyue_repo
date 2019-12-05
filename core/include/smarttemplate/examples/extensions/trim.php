<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("trim.html");
 
	$page->assign( 'LINK',  ' Click Here ' );
 
    $page->output();

?>