<?


include_once("fulltext_search_helper_core_class_v2.inc.php");

$select_param_arr[] = array(
"indexname" => 777,
"fields" => "res_name",
"where" => "res_name:{$text}",
"order" => "",
"limit" => '0,10000',
"source" => "0",
"keyword" => "",
"act_type_id" => "",
"remark" => "",
);

//var_dump($select_param_arr);

//$select_param_arr[] = array(
//"indexname" => 1,
//"fields" => "*",
//"where" => "text:�ܲ�",
//"order" => "",
//"limit" => "0,1",
//"source" => "0",
//"remark" => "",
//);
$fulltext_search_helper_obj = new fulltext_search_helper_core_class('121.9.211.192');
$fulltext_search_res = $fulltext_search_helper_obj->Select($select_param_arr);

//$fulltext_search_res = $fulltext_search_helper_obj->select_sort(98, '����,is_img_item=1', "item_id", '', '0,10', 'title_sort','1_3_5_6_7_11_13_41_42','www','');


//$fulltext_search_res = $fulltext_search_helper_obj->select_1(88 , 'text=���»� ����· ����·', "*", 'credit_point#desc', '0,10');

//$wheres = "��Ӱ";
//
//$search_args_arr[] = array(
//"indexname" => 100,				//������
//"fields"	=> "timer,peers,link,size,name",				//Ĭ����*ȫ���ֶΣ�Ҫ�����ֶ�д�ϸ��ֶ�
//"where"		=> "text={$wheres}",//��ѯ�������text=giggs,credit_point>0
//"order"		=> "peers#desc",			//�������������� credit_point desc
//"limit"		=> "0,10" 			//Ҫ����ȫ��������� 0,100
//);
//
//$search_args_arr[] = array(
//"indexname" => 113,				//������
//"fields"	=> "fname,type,link,size,itime,resnum",				//Ĭ����*ȫ���ֶΣ�Ҫ�����ֶ�д�ϸ��ֶ�
//"where"		=> "text={$wheres}",//��ѯ�������text=giggs,credit_point>0
//"order"		=> "resnum#desc",			//�������������� credit_point desc
//"limit"		=> "0,10" 			//Ҫ����ȫ��������� 0,100
//);
//
//
//$fulltext_search_res = $fulltext_search_helper_obj->MultiSelect($search_args_arr);
//
$ret = array();
foreach ($fulltext_search_res['result'] as $key =>$res)
{
	$ret[$key]['res_name'] = $res['res_name'];
	
	$ret[$key]['order']  = strpos($res['res_name'],$text);
}

error_reporting(E_ERROR | E_PARSE);
/**
* �����ά����
*/
if (!function_exists("aasort"))
{
	function aasort(&$array, $args)
	{
		$sort_rule="";
		foreach($args as $arg)
		{
			$order_field = substr($arg, 1, strlen($arg));
			foreach($array as $array_row)
			{
				$sort_array[$order_field][] = $array_row[$order_field];
			}
			$sort_rule .= '$sort_array['.$order_field.'], '.($arg[0] == "+" ? SORT_ASC : SORT_DESC).',';
		}
		eval ("array_multisort($sort_rule".' &$array);');
	}
}

aasort($ret,array('+order'));

echo "<pre>";
print_r($ret); 
echo "</pre>";
//$values[0] = array('text'=>'��������������������������������kk_daf','forum_id'=>13,'user_id'=>13354967,'tid'=>9999881);
//$tmp = $fulltext_search_helper_obj->insert_1(101, $values);
//var_dump($tmp);
?>