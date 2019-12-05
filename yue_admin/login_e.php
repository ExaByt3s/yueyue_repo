<?php
    include_once 'common.inc.php';

    $tpl = new SmartTemplate("login.tpl.htm");
    if($_GET['referer_url'])
    {
        $tpl->assign('referer_url', $_GET['referer_url']);
    }
    $tpl->output();

 ?>