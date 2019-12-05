<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("truncate.html");
 
	$teaser = 'PHP 4.3.0RC1 has been released. This is the first release candidate';
	$page->assign( 'TEASER',  $teaser );
 
    $page->output();

?>