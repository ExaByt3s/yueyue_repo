<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("textbutton.html");
 
	$page->assign( 'HEADLINE',  'Sample Page Headline' );
	$page->assign( 'CONTENT',   'Sample Content' );
 
    $page->output();

?>