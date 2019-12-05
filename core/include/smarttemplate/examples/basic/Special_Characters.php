<?php

    require_once "class.smarttemplate.php";

    $page  =  new SmartTemplate('Special_Characters.html');

    $page->assign( 'LINK',  '<< ZURÜCK' );

    $page->output();

?>