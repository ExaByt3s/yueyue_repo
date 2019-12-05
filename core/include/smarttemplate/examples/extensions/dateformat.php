<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("dateformat.html");

    //  Read Last-Modified-Date of this Script File
	$last_change  =  filemtime('./dateformat.php');

    //  Assign it to the Template Variable 'TIMESTAMP'
	$page->assign("TIMESTAMP",  $last_change);

    $page->output();

?>