<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("options.html");

	//  Define option arrays
	$props = array( "text",  "bgcolor" );
	$cols  = array( "FF0000" => "Red",  
					"00FF00" => "Green",  
					"0000FF" => "Blue" );

	//  Assign option arrays
	$page->assign( 'all_properties',  $props );
	$page->assign( 'all_colors',      $cols );

	//  Assign Post Vars to Template Array (PROPERTY + COLOR)
	$page->assign( $_POST );

    $page->output();

?>