<?php

function smarttemplate_extension_in_array($value, $arr_str)
{
	$arr = explode(",",$arr_str);
	foreach ( $arr as $k=>$v )
	{
		$arr[$k] = trim($v);
	}
	
	return in_array($value, $arr)?1:0;
}
?>