<?php
ini_set('memory_limit','512M');

include_once 'common.inc.php';


switch($action)
{
	case "export":
        exit('export');
	break;
	default:
        $type_id = (int)$_INPUT['type_id'];
        if(empty($type_id))
        {
            $type_id = 31;
        }
		$property_for_search_config = pai_mall_load_config('property_for_search_test');
        dump($property_for_search_config);
        include_once (TASK_TEMPLATES_ROOT."property_for_search_config_tpl.php");
	break;
}

?>