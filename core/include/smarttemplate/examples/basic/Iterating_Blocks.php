<?php

	require_once "class.smarttemplate.php";

    $users  =  array(
                   array(
                       'FIRSTNAME' => 'John',
                       'LASTNAME'  => 'Doe',
                   ),
                   array(
                       'FIRSTNAME' => 'Roger',
                       'LASTNAME'  => 'Rabbit',
                   ),
               );

	$page = new SmartTemplate("Iterating_Blocks.html");
	$page->assign('users',  $users );
	$page->output();

?>