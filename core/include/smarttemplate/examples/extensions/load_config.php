<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("load_config.html");

	$page->assign( 'SAMPLE_TEXT',  'Core Content.' );

    $page->output();

?>