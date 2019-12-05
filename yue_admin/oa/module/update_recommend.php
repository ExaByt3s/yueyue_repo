<?php

include_once '../common.inc.php';

$oa_model_list = POCO::singleton ( 'pai_model_oa_model_list_class' );

$id = $_INPUT['id'];

$status = (int)$_INPUT['status'];


if(empty($id))
{
	exit;
}



$ret = $oa_model_list->update_model(array("status"=>$status), $id);

if($ret)
{
	echo 1;
}
else
{
	echo 0;
}

?>