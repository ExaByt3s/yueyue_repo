<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("nvl.html");

	//  Typically this values would be fetched from a database
	$page->assign( 'START_DATE',  '01.01.2003' );
	$page->assign( 'END_DATE',    '' );

    $page->output();

?>