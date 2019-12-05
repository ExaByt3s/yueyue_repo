<?php

    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("lowercase.html");

    $page->assign('TEXT', 'Simple Demo Text.');

    $page->output();

?>