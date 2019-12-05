<?php
 
    require_once "class.smarttemplate.php";
    $page = new SmartTemplate("Alternating_Colors.html");
    $users = array(
               array( 'NAME' => 'John Doe',   'GROUP' => 'ADMIN' ),
               array( 'NAME' => 'Jack Doe',   'GROUP' => 'SUPPORT' ),
               array( 'NAME' => 'James Doe',  'GROUP' => 'GUEST' ),
               array( 'NAME' => 'Jane Doe',   'GROUP' => 'GUEST' ),
             );
    $page->assign( 'users',  $users );
    $page->output();
 
?>
