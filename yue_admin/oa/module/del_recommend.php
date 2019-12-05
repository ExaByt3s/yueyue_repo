<?php

include_once '../common.inc.php';

$oa_model_list = POCO::singleton ( 'pai_model_oa_model_list_class' );

$id = (int)$_INPUT['id'];

$ret = $oa_model_list->del_model($id);

if($ret)
{
	echo 1;
}
else
{
	echo 0;
}

?>