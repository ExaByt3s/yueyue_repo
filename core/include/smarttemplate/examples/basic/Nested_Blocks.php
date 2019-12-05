<?php

	require_once "class.smarttemplate.php";

    for ($row = 0;  $row < 3;  $row++) {
        for ($col = 0;  $col < 6;  $col++) {
			$text  =  "Cell $col/$row";
            $table["row"][$row]["column"][$col]["CELL"] = $text;
        }
    }

	$page = new SmartTemplate("Nested_Blocks.html");
	$page->assign($table);
	$page->output();

?>