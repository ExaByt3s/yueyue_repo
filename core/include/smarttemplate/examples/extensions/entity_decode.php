<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("entity_decode.html");

	$page->assign("MESSAGE",  "Nicht M&ouml;glich!");

    $page->output();

?>