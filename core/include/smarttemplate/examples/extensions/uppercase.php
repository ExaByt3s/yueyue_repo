<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("uppercase.html");

    $page->assign('TEXT', 'Simple Demo Text.');

    $page->output();

?>