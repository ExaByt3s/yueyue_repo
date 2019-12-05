<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("hidemail.html");

	$page->assign( 'AUTHOR',  'philipp@criegern.de' );

    $page->output();

?>