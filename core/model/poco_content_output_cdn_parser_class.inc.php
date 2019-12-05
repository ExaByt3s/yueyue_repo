<?
/**
 * 把符合cdn条件的输出url，替换成cdn链接，
 * 例如把图片 http://img.poco.cn/mypoco/myphoto/2005082/zzzzzzzzzz.jpg
 * 替换成 http://image.poco.cn/mypoco/myphoto/2005082/zzzzzzzzzz.jpg
 *
 */
class poco_content_output_cdn_parser_class
{
	/**
	 * 要替换多少天前的链接，无法精确计算时间，所以要比实际多加一天
	 *
	 * @var int
	 */
	var $date_offset = 0; //关闭时间判断

	function poco_content_output_cdn_parser_class()
	{

	}

	/**
	 * 取得当前时间以微妙表示
	 *
	 * @access private
	 * @return float
	 */
	function _microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}


	/**
	* 调试输出如果
	* @access private
	*/
	function _trace($var,$title="")
	{
		global $_debug;
		if (empty($_debug))
		{
			$_debug = $_REQUEST["_debug"];
		}

		if ($_debug)
		{
			echo "【_trace ".$title."：";
			var_dump($var);
			echo "】<br />\r\n";
		}
	}

	/**
	 * 分析处理要输出的HTML内容
	 *
	 * @param string $content	等待输出的HTML内容
	 * @param string $function	要调用的函数
	 * @return string
	 */
	function parse($content)
	{
		/**
		 * 微信的iphone版，捉不了cdn的图，这里发现user_agent就不替换cdn了
		 */
		if (false!==strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') && false!==strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone')) 
		{
			return $content;
		}
		
		
		$__handle_start_time = $this->_microtime_float();

		$class_methods_arr = get_class_methods($this);

		foreach ($class_methods_arr as $method)
		{
			if ( preg_match("/^_parser_(.*)$/", $method, $matchs) )
			{
				$content=call_user_func(array($this, $method),$content);
			}
		}

		$__handle_end_time = $this->_microtime_float();
		$__handle_time = sprintf("%0.4f",$__handle_end_time - $__handle_start_time);

		//$this->_trace(__CLASS__."::".__FUNCTION__,$__handle_time."s");
		return $content;
	}

	/**
	 * 过滤相册上传的图片
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	function _parser_mypoco_items_img($content)
	{
		if($_REQUEST['_no_cdn'])
		{
			return $content;
		}

		//定义此变量情况下先特殊处理img1为image1-0
		if(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO'))
		$content = str_replace('http://img1.poco.cn','http://image1-0.poco.cn',$content);

		$pattern	=	"/http(?::\/\/|%3A%2F%2F|:\\\\\/\\\\\/)(img|image)(\d*)\.poco\.cn(?:\/|%2F|\\\\\/)/ims";
		$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);
		
		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgdomain = $result[1];		//img域名
				$imgx = $result[2];		//img几
				$replace = '';

				if(!in_array($result[0],$has_replace))
				{
					$has_replace[]	=	$result[0];

					//特殊处理
					if (in_array($imgx,array('16')) && defined('POCO_CONTENT_OUPUT_BGP_PARSER_LINK')) 
					{
						$replace	=	str_replace("{$imgdomain}{$imgx}.poco.cn","image{$imgx}-d.poco.cn",$result[0]);//bgp
					}
					elseif(in_array($imgx,array('208','163','181','2081','165','170','13','14','142','15','226','16','227','17')) && !defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO'))
					{
						$replace	=	str_replace("{$imgdomain}{$imgx}.poco.cn","image{$imgx}-c.poco.cn",$result[0]);//cdn
					}
					elseif(in_array($imgx,array('1')))
					{
						$replace	=	str_replace("img{$imgx}.poco.cn","img{$imgx}-c.poco.cn",$result[0]);//cdn
					}								
					elseif(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO') && in_array($imgx,array('','5','211','215','155')))
					{
						$replace	=	str_replace("{$imgdomain}{$imgx}.poco.cn","image{$imgx}-0.poco.cn",$result[0]);
					}

					if ($replace)
					{
						$content	=	str_replace($result[0],$replace,$content);
					}

				}

			}
		}
		return $content;
	}


	/**
	 * 还原图片为非cdn
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	function parser_img_to_nocdn($content)
	{
		$pattern	=	"/http(?::\/\/|%3A%2F%2F)(img|image)(\d*)-c\.poco\.cn(?:\/|%2F)/ims";
		$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgdomain = $result[1];		//img域名
				$imgx = $result[2];		//img几
				$replace = '';

				if(!in_array($result[0],$has_replace))
				{
					$has_replace[]	=	$result[0];

					if(in_array($imgx,array('1')))
					{
						$replace	=	str_replace("{$imgdomain}{$imgx}-c.poco.cn","img{$imgx}.poco.cn",$result[0]);//cdn
					}
					else
					{
						$replace	=	str_replace("{$imgdomain}{$imgx}-c.poco.cn","image{$imgx}.poco.cn",$result[0]);//cdn
					}
					$content	=	str_replace($result[0],$replace,$content);
				}

			}
		}
		return $content;
	}

	/**
	 * 过滤相册上传的图片
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	/*
	function _parser_mypoco_items_img_old($content)
	{
		if($_REQUEST['_no_cdn'])
		{
			return $content;
		}


		//定义此变量情况下先特殊处理img1为image1-0
		if(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO'))
		$content = str_replace('http://img1.poco.cn','http://image1-0.poco.cn',$content);

		//$pattern	=	"/http(?::\/\/|%3A%2F%2F)img(\d*)\.poco\.cn(?:\/|%2F)mypoco(?:\/|%2F)(?:myphoto|myphoto_anonymous|myphoto_m)(?:\/|%2F)([0-9]{8})/ims";
		$pattern	=	"/http(?::\/\/|%3A%2F%2F)img(\d*)\.poco\.cn(?:\/|%2F)(?:mypoco(?:\/|%2F))?(?:myphoto|myphoto_anonymous|myphoto_m|best_pocoers)(?:\/|%2F)([0-9]{8})/ims";
		//$pattern	=	"/http(?::\/\/|%3A%2F%2F)(?:img|image)(\d*)\.poco\.cn(?:\/|%2F)(?:mypoco(?:\/|%2F))?(?:myphoto|myphoto_anonymous|myphoto_m|best_pocoers)(?:\/|%2F)([0-9]{8})/ims";

		$date_offset	=	$this->date_offset;
		$time_now	=	time();	//当前服务器时间，服务器间可能会有时间差
		$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);


		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgx = $result[1];		//img几
				$time_file = strtotime($result[2]);		//文件的时间戳
				$replace = '';

				if ($time_file>0 && !in_array($imgx,array(2)))	//不要img2
				{
					if($time_now>($time_file+$date_offset*24*3600) && !in_array($result[0],$has_replace))
					{
						$has_replace[]	=	$result[0];
						//特殊处理
						if(in_array($imgx,array('208','163')))
						{
							$replace	=	str_replace("img{$imgx}.poco.cn","image{$imgx}-c.poco.cn",$result[0]);//cdn
						}
						elseif(in_array($imgx,array('1')))
						{
							$replace	=	str_replace("img{$imgx}.poco.cn","img{$imgx}-c.poco.cn",$result[0]);//cdn
						}
						elseif(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO') && in_array($imgx,array('','5','211')))
						{
							$replace	=	str_replace("img{$imgx}.poco.cn","image{$imgx}-0.poco.cn",$result[0]);
						}
						if ($replace)
						{
							$content	=	str_replace($result[0],$replace,$content);
						}
					}
				}
			}
		}
		return $content;
	}
	*/


	/**
	 * 过滤榜单图片
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	/*function _parser_best_pocoers($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)img.poco.cn(?:\/|%2F)best_pocoers(?:\/|%2F)([0-9]{8})/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//当前服务器时间，服务器间可能会有时间差
	$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

	preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

	if(!empty($pattern))
	{
	foreach ($search_result as $result)
	{
	$time_file = strtotime($result[1]);		//文件的时间戳
	if ($time_file>0)
	{
	if($time_now>($time_file+$date_offset*24*3600) && !in_array($result[0],$has_replace))
	{
	$has_replace[]	=	$result[0];
	$replace	=	str_replace('img.poco.cn','image.poco.cn',$result[0]);
	$content	=	str_replace($result[0],$replace,$content);
	}
	}
	}
	}
	return $content;
	}*/


	/**
	 * 过滤pet86_bbs作品图片
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	/*function _parser_pet86_bbs($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)pet.poco.cn(?:\/|%2F)bbs(?:\/|%2F)attachments(?:\/|%2F)/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//当前服务器时间，服务器间可能会有时间差
	$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

	preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

	if(!empty($pattern))
	{
	foreach ($search_result as $result)
	{
	$has_replace[]	=	$result[0];
	$replace	=	str_replace('pet.poco.cn/bbs/attachments','petatts.poco.cn',$result[0]);
	$replace	=	str_replace('pet.poco.cn%2Fbbs%2Fattachments','petatts.poco.cn',$replace);
	$replace	=	str_replace('pet86.poco.cn%2Fbbs%2Fattachments','petatts.poco.cn',$replace);
	$content	=	str_replace($result[0],$replace,$content);
	}
	}
	return $content;
	}*/

	/**
	 * 过滤POHTO作品图片
	 *
	 * @param string $content	等待输出的HTML内容
	 * @return string
	 */
	/*function _parser_last_photo($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)img.poco.cn(?:\/|%2F)photo(?:\/|%2F)([0-9]{8})/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//当前服务器时间，服务器间可能会有时间差
	$has_replace	=	array();	//已经替换的字符串数组，避免重复替换

	preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

	if(!empty($pattern))
	{
	foreach ($search_result as $result)
	{
	$time_file = strtotime($result[1]);		//文件的时间戳
	if ($time_file>0)
	{
	if($time_now>($time_file+$date_offset*24*3600) && !in_array($result[0],$has_replace))
	{
	$has_replace[]	=	$result[0];
	$replace	=	str_replace('img.poco.cn','image.poco.cn',$result[0]);
	$content	=	str_replace($result[0],$replace,$content);
	}
	}
	}
	}
	return $content;
	}*/
}
?>