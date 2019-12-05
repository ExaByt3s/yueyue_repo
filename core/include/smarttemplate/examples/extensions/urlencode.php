<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("urlencode.html");
 
	$page->assign( 'PARAM',  'Delete User!' );
 
    $page->output();

?>