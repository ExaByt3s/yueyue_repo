<?php
/**
 * 字符串处理类
 * @date 2012-4-2
 * @author extory
 * @m1:edit by extory 2012-09-18 添加不过滤标签的参数
 */
class string_handle_class
{

	/**
	 * gb2312 转换成 utf8 
	 * @param string $str 需要转换的字符串
	 * @return string
	 */
	public function gbk_to_utf8( $str )
	{
		return iconv('GBK//IGNORE', 'UTF-8', $str);
	}

	/**
	 * utf8 转换成 gb2312
	 * @param string $str
	 * @return string
	 */
	public function utf8_to_gbk($str)
	{
		return iconv("UTF-8", "GBK//IGNORE", $str);
	}

	/**
	 * 检查输入的编码
	 * @param $param 编码
	 * @return 编码类型
	 */
	public function detect_encode( $param )
	{
		return mb_detect_encoding($param , array('ASCII','GB2312','GBK','UTF-8'));
	}

	/**
	 * 替换参数
	 * @param $string 输入内容
	 * @return 编码后的字符
	 */
	public function utf8_encode_to_gbk( $param )
	{
		$encoded_text_str = $this -> detect_encode( $param );
		//如果遇到gbk字符
		if( in_array( $encoded_text_str , array('UTF-8','CP936') ) )
		{
			return $this -> utf8_to_gbk( $param ); 
		}
		return $param;
	}

	/**
	 * 替换参数(批量)
	 * @params $array 输入内容
	 * @return 编码后的字符
	 */
	public function utf8_encode_to_gbk_array( $params )
	{
		foreach( $params as $k => $v )
		{
			$params[$k] = $this -> utf8_encode_to_gbk( $v );
		}
		return $params;
	}


	/**
	 * 过滤特殊字符串
	 * @param $string 将要过滤的字符串
	 * @return 编码类型
	 */
	public function filter_string($string, $entity = true, $strip_tags = true,$special_handle=array()){
	
		//把一些br字符，转换为回车符
		if( !$special_handle['amp'] )
		{
			$string = str_replace("&amp;", "&", $string);
		}
		if( !$special_handle['nbsp'] )
		{
			$string = str_replace('&nbsp;',' ',$string);
		}
		if( !$special_handle['br'] )
		{
			$string = str_replace("<br rel=auto>","<br />",$string);
		}
		
		//过滤strip_tags后多余<br>和<p>
		$exp  = "/(<br( \/)?>\s?){2}/i";
		$exp1 = "/(<br( \/)?>\s?){2,}/i";
		$exp2 = "/<p[^>]*?>(&nbsp;|\s|<br( \/)?>)+<\/p>/i";
		
		$str = preg_replace($exp,"<br>",$str);
		$str = preg_replace($exp1,"",$str);
		$str = preg_replace($exp2,"",$str);

		$str = str_replace("<br>", "\n", $str);
		$str = str_replace("<p>", "\n", $str);
		$str = str_replace("</p>", "\n", $str);
	
		//先过滤编码
		$string = htmlspecialchars_decode($string);
		//取出标签
		//m1:
		if( $strip_tags )
		{
			$string = strip_tags($string);
		}
		// if( $_COOKIE['test_mode_pocoShareBox'] )
		// {
			// var_dump($entity);
			// echo $string;
		// }
		
		//转换实体字符
		if( $entity )
		{
			$string = html_entity_decode($string , ENT_NOQUOTES, 'UTF-8');
			//再过滤相关符号的编码
			$string = htmlspecialchars($string , ENT_NOQUOTES, 'UTF-8');
		}

		return $this -> trim_special_string($string);
	}

	/**
	 * 转换特殊字符
	 * @params $string 输入内容
	 * @return 编码后的字符
	 */
	public function trim_special_string($string)
	{
		for( $i = 0; $i < strlen($string); $i ++)
		{
			$asciival = ord( $string[$i] );
			if(($asciival <= 32 || $asciival == 127) && $asciival !=10 )
			{
				$string[$i]= @str_replace($string[$i], " ", $string[$i]);
			}
			else 
			{
				if(@in_array($string[$i], $strval))
				{
					$string[$i] = @str_replace($string[$i], " ", $string[$i]);
				}
			}
		}
		return $string;
	}
}
?>