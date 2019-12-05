<?php

var_dump($_SERVER);
exit;


include_once("fulltext_search_helper_core_class_v2.inc.php");

$fulltext_search_helper_obj = new fulltext_search_helper_core_class();

$i = 0;

$user_id_arr=array();

while ($fulltext_search_res["result_total"]>0)
{
	$count = $i*10;
	$select_param_arr[] = array(
	"indexname" => 98,
	"fields" => "user_id",
	"where" => "text:»éÉ´ AND act_type_id:3",
	"order" => "title_sort",
	"limit" => "{$count},10",
	"source" => "0",
	"keyword" => "",
	);

	$fulltext_search_res = $fulltext_search_helper_obj->Select($select_param_arr);
	$i++;
	
	for ($j = 0; $j < $fulltext_search_res["result_total"]; $j++)
	{
		$sql ="SELECT userid FROM photo_professional";
		$sql.=" WHERE userid=:x_user_id AND pass=1 AND genre=1";
		sqlSetParam($sql,"x_user_id",$fulltext_search_res['result'][$j]["user_id"]*1);
		$row = db_simple_getdata($sql,true);
		
		if ($row['userid']*1>0) 
		{
			$user_id_arr[] = $fulltext_search_res['result'][$j]["user_id"];
		}
	}
}

$user_id_arr = array_unique($user_id_arr);

echo "<table>";
for ($i = 0; $i < count($user_id_arr); $i++) 
{
	echo "<tr>";
	echo "<td>{$user_id_arr[$i]}</td>";
	echo "</tr>";
}
echo "</table>";
?>