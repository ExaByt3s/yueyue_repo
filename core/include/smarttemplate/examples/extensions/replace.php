<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("replace.html");
 
 	$path = 'Examples/Templating/Extensions/replace';
	$page->assign( 'PATH',  $path );
 
    $page->output();

?>