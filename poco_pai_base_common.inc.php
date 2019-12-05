<?php

/**
 * ads common
 */
if (!defined("G_POCO_ADS_PATH"))
{
	define("G_POCO_PAI_BASE_PATH",realpath(dirname(__FILE__))."/");
}

/**
 * POCO相关include
 */
//if (!$DB)
//{
//
//}

//define("G_SITE_STAT_CLIENT","poco_ads");//流量统计
        include('/disk/data/htdocs232/poco/php_common/sources/Drivers/mySQL.php');
        $DB = new db_driver; 

/**
 * POCO的数据库instance
 */
//global $DB;

/**
* 分离变量依赖
*/
global $ibforums;
$_INPUT=$ibforums->input;

$_MEMBER=$ibforums->member;

/**
 * 登录用户
 */
global $login_id,$login_nickname;
$login_id = $_MEMBER['id'];
$login_nickname = $_MEMBER['nickname'];


error_reporting(E_ERROR | E_PARSE);//禁止部分错误信息输出

/**
 * 调试输出如果
 */
$_debug = $_INPUT["_debug"];
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

/**
 * 替换sql中参数，例子：
 * $sql="SELECT * FROM a WHERE field1=:x_field1";
 * sqlSetParam($sql,"x_field1","值");
 * $sql将会替换成"SELECT * FROM a WHERE field1='值'"
 */
if (!function_exists("sqlSetParam"))
{
	function sqlSetParam(&$sql,$paramName,$paramValue)
	{
		$tmp=&$sql;

		if (get_magic_quotes_gpc())
		{
			$paramValue = stripslashes($paramValue);
		}

        $tmp=str_replace(":".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
		$tmp=str_replace("@".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
	}
}

if (!function_exists("microtime_float"))
{
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

/**
* 主数据库操作函数
* 返回格式是二维数组 $ret[$i]["fieldname"]
* 参数$bsingle_record为真时只取一行并且返回一维数组 $ret["fieldname"]
* 参数$server_id为查询数据库的服务器:
* 1 = $DB->query($sql,0,0);
* 2 = $DB->query($sql,0,2);
*/
if (!function_exists("db_simple_getdata"))
{
	function db_simple_getdata($sql,$bsingle_record=false,$server_id=false,$this_query_use_cache=false)
	{
		$this_query_use_cache=$this_query_use_cache*1;
		$_cache_key = __CLASS__."::".__FUNCTION__.serialize(func_get_args());

		$__query_start_time = microtime_float();

		//使用全局db实例
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();


		$server_id=$server_id*1;


		if (defined("G_DB_GET_REALTIME_DATA"))
		{
			if ($server_id*1==0) $server_id=1;
		}
		else if ($this_query_use_cache || defined("G_DB_USE_FULL_QUERYCACHE")) //开全查询cache
		{
			$poco_cache_obj = new poco_cache_class();
			$cache_data = $poco_cache_obj->get_cache($_cache_key);
			if (!empty($cache_data))
			{
				return $cache_data;
			}
		}


		if ($server_id===true || $server_id*1==1)
		{
			$DB->query($sql,0,0);
		}
		else if ($server_id>0) //用制定机
		{
			$DB->query($sql,0,$server_id);
		}
		else
		{
			$DB->query($sql);
		}

		$__query_end_time = microtime_float();
		$__query_time = sprintf("%0.4f",$__query_end_time - $__query_start_time);


		if ($DB->query_count>0)
		{
			while ($row=$DB->fetch_row())
			{
				$__db_simple_getdata_result_ret_arr[]=$row;
			}
		}

		if (count($__db_simple_getdata_result_ret_arr)==1 && $bsingle_record==true)
		{
			$__db_simple_getdata_result_ret_arr=$__db_simple_getdata_result_ret_arr[0];
		}

		//释放内存
		$DB->free_result();

		trace($sql,"{$__query_time}s",$server_id);

		if (defined("G_DB_USE_FULL_QUERYCACHE") || $this_query_use_cache) //开全查询cache
		{
			$_cache_time = max($this_query_use_cache, G_DB_USE_FULL_QUERYCACHE*1);

			if ($_cache_time<2)
			{
				$_cache_time = 1800; //默认30分钟
			}
			$poco_cache_obj = new poco_cache_class();
			$poco_cache_obj->save_cache($_cache_key,$__db_simple_getdata_result_ret_arr,$_cache_time);
		}

		return $__db_simple_getdata_result_ret_arr;
	}
}

/**
 * db_simple_get_insert_id  取最后的insert_id，需要紧跟查询后调用
 * 例子： $log_id = db_simple_get_insert_id();
 * @return int
 */
if (!function_exists("db_simple_get_insert_id"))
{
	function db_simple_get_insert_id()
	{
		global $DB;
		return $DB->get_insert_id();
	}
}

/**
 * db_simple_get_affected_rows  取操作记录数
 * @return int
 */
if (!function_exists("db_simple_get_affected_rows"))
{
	function db_simple_get_affected_rows()
	{
		global $DB;
		return $DB->get_affected_rows();
	}
}

/**
 * check_limit_str  检查limit 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_limit_str("0,20");
 */
if (!function_exists("check_limit_str"))
{
	function check_limit_str($limitstr,$b_die=true)
	{
		$limitstr=trim($limitstr);
		$limitstr = str_replace(" ","",$limitstr);

		$c = preg_match("/^\d+,\d+$/i",$limitstr);

		if (!$c && $b_die) die(__FUNCTION__." limitstr error: $limitstr");

		return $c;
	}
}

/**
 * check_order_by 检查order by 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_order_by("hit_count DESC");
 */
if (!function_exists("check_order_by"))
{
	function check_order_by($orderby,$b_die=true)
	{
		$orderby=trim($orderby);

		$c = preg_match("/^(ORDER BY )?(([ ,A-Za-z0-9_])*( DESC)?)+$/i",$orderby);

		if (!$c && $b_die) die(__FUNCTION__."orderby error: $orderby");

		return $c;
	}
}

/**
 * 数组转换成update_sql字符串
 * 数组：array("FIELD1"=>"val1","FIELD2"="val2"') 
 * 转换成字符串：FIELD1='val1', FIELD2='val2'
 */
if (!function_exists("db_arr_to_update_str"))
{
	function db_arr_to_update_str($array)
	{
		$sign = "";
		$sql_str = "";
		foreach ($array as $k=>$v)
		{
			$sql_str.= $sign."{$k}=:x_{$k}";
			sqlSetParam($sql_str, "x_{$k}", $v);
			$sign = ",";
		}
		return $sql_str;
	}
}

/**
* 排序多维数组
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

/**
* 输出rss的个xml头
*/
if (!function_exists("echo_xml_header"))
{
	function echo_xml_header($encoding="UTF-8")
	{
		echo "<?xml version=\"1.0\" encoding=\"$encoding\" ?>\n";  //xml??????
	}
}

/**
* 输出一个xml node
* 
* @param    boolean    $parse   是否过滤保留字
*/
if (!function_exists("echo_rss_node"))
{
	function echo_rss_node($key,$value,$parse=true,$encoding="UTF-8",$attr_arr)
	{
		$value=trim($value);

		if ( $parse==true ) $value=xml_cleanup($value);

		if ( $encoding=="UTF-8" ) $value=iconv("GBK","UTF-8",$value);

		$attr_str = "";
		if (is_array($attr_arr) && count($attr_arr)>0)
		{
			foreach ($attr_arr as $k=>$v)
			{
				if ( $parse==true ) $v=xml_cleanup($v);

				if ( $encoding=="UTF-8" ) $v=iconv("GBK","UTF-8",$v);
				$attr_str.= " $k=\"".$v."\"";
			}

		}

		$value = empty($value) ? "" : "<![CDATA[$value]]>";
		echo "<{$key}{$attr_str}>{$value}</$key>\n";
	}
}

/**
 * 错误信息提示，并退出程序
 * 
 * @param string $msg 错误信息
 */
if (!function_exists("js_pop_msg"))
{
	function js_pop_msg($msg,$b_reload=false,$url=NULL)
	{
		echo "<script language='javascript'>alert('{$msg}');";
		if($url)
		{
			echo "if(window.name=='mainFrame')
			{
				window.location = '{$url}';
			}
			else
			{
			window.parent.location = '{$url}';
			}";
		}
		if($b_reload) echo "window.parent.location.reload();";
		echo "</script>";
		exit;
	}
}
/**
 *  改进版广告系统提示框
 *  author Manson  2010.11.25
 *
 * @param string $msg	提示信息
 * @param string $tip_type	提示级别  "complete":成功;"notice":提醒;"warn":错误
 * @param int $time  自动关闭时间  默认false 既不自动关
 * @param bool $click_close  是否能点击提示层关闭
 * @param bool $esc_close  是否能按键盘esc关闭提示层
 * @param bool $close_win  提示窗关闭后是否关闭当前页面
 * @param string $url  提示窗关闭后是否跳转到url,默认false不跳转
 * @param bool $b_reload  提示完后是否刷新
 * @param bool $scroll 是否随屏滚
 * @param int $top  上边距，默认false垂直居中
 * @param int $left  左边距，默认false水平居中
 */
if (!function_exists("ads_tips_box_show"))
{
	function ads_tips_box_show($msg,$tip_type="complete",$time=false,$click_close=false,$esc_close=false,$close_win=false,$url=false,$b_reload=false,$scroll=true,$top=false,$left=false)
	{
		
		$echo_js_str = "<script language='javascript'>parent.poco_ads_tips_box({msg:'{$msg}',tip_type:'{$tip_type}',scroll:'{$scroll}',time:'{$time}',click_close: '{$click_close}',esc_close:'{$esc_close}'";
		if($top)
		{
			$echo_js_str.="top:'{$top}',";
		}
		if($left)
		{
			$echo_js_str.="left:'{$left}',";
		}
		if($close_win)
		{
			$echo_js_str.=",onBoxClose:function(){parent.close_ads_tips_box();parent.window.opener = null;parent.window.open('', '_self');parent.window.close();}";
		}
		if(($url || $b_reload) && !$close_win)
		{
			$echo_js_str.=",onBoxClose:function(){";
			if($url)
			{
				$echo_js_str.="if(window.name=='mainFrame'){close_ads_tips_box();window.location = '{$url}';}else{window.parent.location = '{$url}';}";
			}
			if($b_reload)
			{
				$echo_js_str.="window.parent.location.reload();";
			}
			$echo_js_str.="}";
		}
		$echo_js_str.="});</script>";
		
		echo $echo_js_str;
		exit;
	}
}

if (!function_exists("mybase64_encode"))
{
	function  mybase64_encode($src)
	{
		//static const char	base64_encode_table[] =	"WXGHYZKLMNOABCDEFIJPQRSTUV" 
		//"nopqrsfghijkabcdelmxyztuvw" 
		//"8934560127+/";
		$normal_b64 = base64_encode($src);	
		$g_base64_encode_php_table = "WXGHYZKLMNOABCDEFIJPQRSTUVnopqrsfghijkabcdelmxyztuvw8934560127+/";
		$normal_b64_len = strlen($normal_b64);
		$chr="";
		$ret_str="";
		$i = 0;	
		for (;$i<$normal_b64_len; $i++){	
			$chr = substr($normal_b64, $i, 1);				
			if(ord($chr) >= ord('A') && ord($chr) <= ord('Z'))
			{	
				$ret_str = $ret_str.$g_base64_encode_php_table[ord($chr) - ord('A')];
			}
			else if(ord($chr) >= ord('a') && ord($chr) <= ord('z'))
			{
				$ret_str = $ret_str.$g_base64_encode_php_table[ord($chr) - ord('a') + 26];
			}
			else if(ord($chr) >= ord('0') && ord($chr) <= ord('9'))
			{
				$ret_str = $ret_str.$g_base64_encode_php_table[ord($chr) - ord('0') + 52];
			}else{
				$ret_str .= $chr;
			}
		}
		return $ret_str;
	}
}

if (!function_exists("mybase64_decode"))
{
	function mybase64_decode($src)
	{	
		$g_base64_decode_php_table = "LMNOPQCDRSGHIJKTUVWXYZABEFmnopqghijklrsabcdefwxyztuv6782345901+/";
		$src_len = strlen($src);	
		$i = 0;
		$chr="";
		$normal_b64 = "";
		for(;$i<$src_len;$i++)
		{
			$chr = substr($src, $i, 1);		
			//echo $chr."\n";		
			if(ord($chr) >= ord('A') && ord($chr) <= ord('Z'))
			{	
				$normal_b64 .= $g_base64_decode_php_table[ord($chr) - ord('A')];
			}
			else if(ord($chr) >= ord('a') && ord($chr) <= ord('z'))
			{
				$normal_b64 .= $g_base64_decode_php_table[ord($chr) - ord('a') + 26];
			}
			else if(ord($chr) >= ord('0') && ord($chr) <= ord('9'))
			{
				$normal_b64 .= $g_base64_decode_php_table[ord($chr) - ord('0') + 52];
			}else{
				$normal_b64 .= $chr;
			}
		}
		//echo $normal_b64."\n";
		return base64_decode($normal_b64);
	}
}
?>