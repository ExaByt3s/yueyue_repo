<?
if (!function_exists("microtime_float"))
{
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}
$_debug = 1;
if (!function_exists("trace"))
{
	function trace($var,$title="",$server_id="")
	{
		global $_debug;
		if ($_debug)
		{
			echo "【trace ".$title."(server_id{$server_id})：";
			var_dump($var);
			echo "】<br />\r\n";
		}
	}
}
include_once("fulltext_search_helper_core_class_v2.inc.php");

			$arg_arr[] = array(
			'indexname' => "98",
			'fields' => "act_id,item_id,act_type_id,user_id,item_title,item_tags,keywords,item_summary,item_img,add_time,hit_count,cmt_count,last_cmt_time,vote_count,is_vouch,user_credit_point,item_img_count,sort_order",
			'where' => "text:{$txt} AND (act_type_id:1 3 6 7 11 13 52) AND is_img_item:1",
			'order'	=> '',
			'limit'	=> '0,1',
			'source'	=> 0,
			'keyword'	=> $txt,
			'act_type_id'	=> '1_3_6_7_11_13_52',
			'remark'	=> '',
			);
			trace($arg_arr,'全文搜索参数');

			$__query_start_time = microtime_float();

			$fulltext_search_helper_obj = new fulltext_search_helper_core_class();
			$fulltext_search_res = $fulltext_search_helper_obj->Select($arg_arr);

			$__query_end_time = microtime_float();
			$__query_time = sprintf("%0.4f",$__query_end_time - $__query_start_time);
			trace("{$__query_time}s",'全文搜索时间');


echo "<pre>";
print_r($fulltext_search_res); 
echo "</pre>";

?>