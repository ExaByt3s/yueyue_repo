<?php

/**
 * 接口公共类
 *
 * @author extory
 * @date 2012-03-28
 * 
 * @m1:edit by extory 2012-09-18 添加不过滤标签的参数
 */

class poco_services_api_class
{
	//传入的参数
	var $params = array();
	//用户ID
	var $user_id_int;
	//是否记录日志
	var $log_info_bol = false;
	//当前时间，微秒
	var $mtime_str = '';
	//实例化的对象
	var $instance_obj;
	var $log_obj;
	var $string_obj;
	//是否立即输出
	var $output_bol = true;

	/**
	 * 构造函数
	 *
	 */
	public function __construct( $output_bol = true , $user_id = 0 )
	{
		$this -> output_bol = $output_bol;
		global $_INPUT;
		$this -> log_obj	 = POCO::singleton('poco_app_log_class');
		//转码
		$this -> string_obj  = POCO::singleton('string_handle_class');
		//转换编码(如果是utf8)
		$this -> params		 = $this -> string_obj -> utf8_encode_to_gbk_array( $_INPUT );
		$this -> user_id_int = $user_id;
		$this -> mtime_str   = $this -> getmicrotime();
		if( $_INPUT['_log'] == 1 )
		{
			$this -> log_info_bol = true;
		}
	}

	/**
	 * 析构函数
	 * 
	 */
	public function __destruct()
	{
		unset( $this -> log_obj );
		unset( $this -> string_obj );
	}

	/**
	 * 输出函数
	 * @param array $array 要输出的数组
	 * @param string $format 输出的格式
	 * @param boolean $convert 是否转换成utf-8
	 * @param string $callback
	 * @param $params 参数
	 * @return $string
	 */
	public function output( $array , $format = 'xml' , $convert = true ,$callback = '', $params = array() )
	{	
		//如果jsonp的callback为空，则返回json格式
		if($format == 'jsonp' && $callback == '')
		{
			$format = 'json';
		}
		
		if( $this -> log_info_bol )
		{
			$trace_info_arr = debug_backtrace();
			//记录日志
			$this -> log_obj -> _log( print_r($this -> params,true) , 
									  "export_type:{$format}\nexport_content:".print_r($array,true),
									  $this -> getmicrotime() - $this -> mtime_str,
									  0,
				                      $this -> user_id_int,
									  print_r($trace_info_arr,true)
			);

			$array['_app_log']['run_time']  = $this -> getmicrotime() - $this -> mtime_str;
			$array['_app_log']['trace']		= $this -> get_trace_info($trace_info_arr);
		}
		$array = $this -> format( $array , $convert, $params );
		global $login_id;

		$return_data_str = '';
		//根据类型输出
		switch( $format )
		{
			case 'xml':
				@header("Content-Type: text/xml");
				$return_data_str = $this -> array2xml( $array);
				break;
			case 'json':
				$return_data_str = json_encode($array);
				break;
			case 'jsonp':
				$return_data_str = $callback . '(' . json_encode($array) . ')';
				break;
			default:
				@header("Content-Type: text/xml");
				$return_data_str = $this -> array2xml( $array );
		}

		if( $this -> output_bol )
		{
			echo($return_data_str);
		}
		else
		{
			return $return_data_str;
		}
	}

	/**
	 * 获取调试信息
	 * @param $array 要输出的数组
	 * @param $format 输出的格式
	 * @return $string
	 */
	function get_trace_info( $trace_info )
	{
		$tmp_array = array();
		for( $i = 0 ; $i < count( $trace_info ) ; $i ++ )
		{
			$tmp_array[]['trace_info'] = array('file' => $trace_info[$i]['file'], 'line' => $trace_info[$i]['line'],'function' => $trace_info[$i]['function'],'class' => $trace_info[$i]['class'] );
		}
		return $tmp_array;
	}


	/**
	 * 调用提醒接口
	 * @param $errcode 错误代码
	 * @param $message 错误代码
	 * @param $out_type $输出代码
	 * @param $callback JSONP调用
	 * @param $params 参数
	 * @param $return_prarms 返回参数
	 * @param $rst_name 返回的result key
	 * @param $msg_name 返回的message key
	 * @return $string
	 */
	public function send_notify($errcode, $message , $out_type = 'xml', $callback = '', $params = array(), $return_params = array(),$rst_name = 'result',$msg_name = 'message',$convert = false)
	{
		$msg_data = array();
		if( !empty( $rst_name ) )
		{
			$msg_data[$rst_name] = $errcode;
		}
		if( !empty( $msg_name ) )
		{
			$msg_data[$msg_name] = $message;
		}
		return $this -> output( $msg_data + $return_params , $out_type , $convert, $callback , $params );
	}

	/**
	 * 格式化输出
	 * @param $array 要输出的数组
	 * @param $covert 是否转换成utf-8
	 * @param array $params 其他参数
	 * @return $string
	 */
	public function format( $array , $convert = true, $params = array() )
	{
		//m1:
		!isset($params['entity']) && $params['entity'] = true;
		!isset($params['strip_tags']) && $params['strip_tags'] = true;
		foreach( $array as $key => $val )
		{
			if( is_array( $val ) )
			{
				$array[$key] = $this -> format( $val, $convert, $params );
			}
			else
			{
				if( is_string( $val ) )
				{


					if( $convert )
					{
						$val = $this -> string_obj -> gbk_to_utf8( $val );
					}
					$val = $this -> string_obj -> filter_string( $val, $params['entity'],$params['strip_tags'],$params['special_handle'] );

					if (defined("G_SERVICES_API_PARSE_CDN_IMG_LINK") && G_SERVICES_API_PARSE_CDN_IMG_LINK) 
					{
						$val = POCO::execute('common.content_output_cdn_parser', $val);
					}

					$array[$key] = $val;
				}	

			}
		}
		
		return $array;
	}

	/**
	 * 输出xml字符
	 * @param $array 要输出的数组
	 * @param $type xml名称
	 * @param $tag 顶部的子节点名称
	 * @param $format 输出的格式
	 * @return $string
	 */
	public function array2xml($var, $type = 'xml', $tag = '') {  
	   $ret = '';  
	   if (!is_int($type)) 
	   {  
		   if( $tag )  
		   {
			   return $this -> array2xml(array($tag => $var), 0, $type); 
		   }
		   else 
		   {  
			   $tag  .= $type;  
			   $type = 0;  
		   }  
	   }  
	   $level = $type;  
	   $indent = str_repeat("\t", $level);  
	   if (!is_array($var)) 
	   {  
		   $ret .= $indent . '<' . $tag;  
		   $var = strval($var);  
		   if ($var == '') 
		   {  
			   $ret .= ' />';  
		   } 
		   else if (!preg_match('/[^0-9a-zA-Z@\._:\/-]/', $var)) 
		   {  
			   $ret .= '>' . $var . '</' . $this -> skip_tag_space($tag) . '>';  
		   } 
		   else 
		   {  
			   $ret .= "><![CDATA[{$var}]]></" . $this -> skip_tag_space($tag) . ">";
		   }  
		   $ret .= "\n";  
	   } 
	   else if (!is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1)) && !empty($var)) 
	   {  
		   foreach ($var as $tmp)
		   {
			   $ret .= $this -> array2xml($tmp, $level, $tag);  
		   }
			  
	   } else {  
		   if( !is_numeric($tag) )$ret .= $indent . '<' . $tag;  
		   if ($level == 0) $ret .= '';   
		   if( !is_numeric($tag) )$ret .= ">\n";  
		   foreach ($var as $key => $val) 
		   {  
			   $ret .= $this -> array2xml($val, $level + 1, $key);  
		   }  
		   if( !is_numeric($tag) )$ret .= "{$indent}</" . $this -> skip_tag_space($tag) . ">\n";  
	   }  
	   return $ret;  
	}
	
	/**
	 * 去标签的属性
	 * @return 当前时间精确到微秒
	 */
	public function skip_tag_space( $tag )
	{
		$tags_arr = explode(' ',$tag);
		return $tags_arr[0];
	}
	
/*
	public function array2xml($var, $type = 'xml', $tag = '') {  
	   $ret = '';  
	   if (!is_int($type)) 
	   {  
		   if( $tag )  
		   {
			   return $this -> array2xml(array($tag => $var), 0, $type); 
		   }
		   else 
		   {  
			   $tag  .= $type;  
			   $type = 0;  
		   }  
	   }  
	   $level = $type;  
	   $indent = str_repeat("\t", $level);  
	   if (!is_array($var)) 
	   {  
		   $ret .= $indent . '<' . $tag;  
		   $var = strval($var);  
		   if ($var == '') 
		   {  
			   $ret .= ' />';  
		   } 
		   else if (!preg_match('/[^0-9a-zA-Z@\._:\/-]/', $var)) 
		   {  
			   $ret .= '>' . $var . '</' . $tag . '>';  
		   } 
		   else 
		   {  
			   $ret .= "><![CDATA[{$var}]]></{$tag}>";
		   }  
		   $ret .= "\n";  
	   } 
	   else if (!is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1)) && !empty($var)) 
	   {  
		   foreach ($var as $tmp)
		   {
			   $ret .= $this -> array2xml($tmp, $level, $tag);  
		   }
			  
	   } else {  
		   if( !is_numeric($tag) )$ret .= $indent . '<' . $tag;  
		   if ($level == 0) $ret .= '';   
		   if( !is_numeric($tag) )$ret .= ">\n";  
		   foreach ($var as $key => $val) 
		   {  
			   $ret .= $this -> array2xml($val, $level + 1, $key);  
		   }  
		   if( !is_numeric($tag) )$ret .= "{$indent}</{$tag}>\n";  
	   }  
	   return $ret;  
	}
*/

	/**
	 * 获取精确时间
	 * @return 当前时间精确到微秒
	 */
	public function getmicrotime()   
	{   
		list($usec, $sec) = explode(" ",microtime());   
		return ((float)$usec + (float)$sec);   
	}
	
	/**
	 * 获取手机图片地址
	 * @param $source_image 来源图片
	 * @param $size 图片尺寸
	 * @param $qulity 图片质量
	 * @return 返回图片地址
	 */
	function get_mobile_image_address($source_image,$size,$qulity=100)
	{
		return '';
	}

}



?>