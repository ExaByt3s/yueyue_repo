<?php

    require_once "class.smarttemplate.php";

    $page  =  new SmartTemplate('Special_Characters.html');

    $page->assign( 'LINK',  '<< ZUR�CK' );

    $page->output();

?>