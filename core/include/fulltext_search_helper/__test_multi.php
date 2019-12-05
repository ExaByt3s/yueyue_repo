<?
include_once("fulltext_search_helper_core_class.inc.php");

$index_obj = new fulltext_search_helper_core_class();

	$wheres = "变形金刚";	//查询多个条件text=giggs,credit_point>0


$search_args_arr[] = array(
	"indexname" => 100,				//索引名
	"fields"	=> "timer,peers,link,size,name",				//默认是*全部字段，要单独字段写上该字段
	"where"		=> "text={$wheres}",//查询多个条件text=giggs,credit_point>0
	"order"		=> "peers#DESC",			//不用排序可以填空 credit_point desc
	"limit"		=> "0,2" 			//要返回全部数据填空 0,100
	);

	$search_args_arr[] = array(
	"indexname" => 113,				//索引名
	"fields"	=> "fname,type,link,size,itime,resnum",				//默认是*全部字段，要单独字段写上该字段
	"where"		=> "text={$wheres}",//查询多个条件text=giggs,credit_point>0
	"order"		=> "resnum#DESC",			//不用排序可以填空 credit_point desc
	"limit"		=> "0,2" 			//要返回全部数据填空 0,100
	);


	$index_obj = new fulltext_search_helper_core_class();
	$res_arr = $index_obj->MultiSelect($search_args_arr);


	var_dump($res_arr);
?>