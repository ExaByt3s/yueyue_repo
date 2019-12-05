<?php

    require_once "class.smarttemplate.php";

    $page  =  new SmartTemplate('DropDown_Boxes.html');

	$cols  =  array( 'Red', 'Green', 'Blue' );
    $page->assign( 'COLORS', $cols );

    $page->output();

?>