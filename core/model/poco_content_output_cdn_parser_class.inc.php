<?
/**
 * �ѷ���cdn���������url���滻��cdn���ӣ�
 * �����ͼƬ http://img.poco.cn/mypoco/myphoto/2005082/zzzzzzzzzz.jpg
 * �滻�� http://image.poco.cn/mypoco/myphoto/2005082/zzzzzzzzzz.jpg
 *
 */
class poco_content_output_cdn_parser_class
{
	/**
	 * Ҫ�滻������ǰ�����ӣ��޷���ȷ����ʱ�䣬����Ҫ��ʵ�ʶ��һ��
	 *
	 * @var int
	 */
	var $date_offset = 0; //�ر�ʱ���ж�

	function poco_content_output_cdn_parser_class()
	{

	}

	/**
	 * ȡ�õ�ǰʱ����΢���ʾ
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
	* ����������
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
			echo "��_trace ".$title."��";
			var_dump($var);
			echo "��<br />\r\n";
		}
	}

	/**
	 * ��������Ҫ�����HTML����
	 *
	 * @param string $content	�ȴ������HTML����
	 * @param string $function	Ҫ���õĺ���
	 * @return string
	 */
	function parse($content)
	{
		/**
		 * ΢�ŵ�iphone�棬׽����cdn��ͼ�����﷢��user_agent�Ͳ��滻cdn��
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
	 * ��������ϴ���ͼƬ
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	function _parser_mypoco_items_img($content)
	{
		if($_REQUEST['_no_cdn'])
		{
			return $content;
		}

		//����˱�������������⴦��img1Ϊimage1-0
		if(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO'))
		$content = str_replace('http://img1.poco.cn','http://image1-0.poco.cn',$content);

		$pattern	=	"/http(?::\/\/|%3A%2F%2F|:\\\\\/\\\\\/)(img|image)(\d*)\.poco\.cn(?:\/|%2F|\\\\\/)/ims";
		$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);
		
		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgdomain = $result[1];		//img����
				$imgx = $result[2];		//img��
				$replace = '';

				if(!in_array($result[0],$has_replace))
				{
					$has_replace[]	=	$result[0];

					//���⴦��
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
	 * ��ԭͼƬΪ��cdn
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	function parser_img_to_nocdn($content)
	{
		$pattern	=	"/http(?::\/\/|%3A%2F%2F)(img|image)(\d*)-c\.poco\.cn(?:\/|%2F)/ims";
		$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgdomain = $result[1];		//img����
				$imgx = $result[2];		//img��
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
	 * ��������ϴ���ͼƬ
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	/*
	function _parser_mypoco_items_img_old($content)
	{
		if($_REQUEST['_no_cdn'])
		{
			return $content;
		}


		//����˱�������������⴦��img1Ϊimage1-0
		if(defined('POCO_CONTENT_OUPUT_CDN_PARSER_ITEM_IMG_PLUS_ZERO'))
		$content = str_replace('http://img1.poco.cn','http://image1-0.poco.cn',$content);

		//$pattern	=	"/http(?::\/\/|%3A%2F%2F)img(\d*)\.poco\.cn(?:\/|%2F)mypoco(?:\/|%2F)(?:myphoto|myphoto_anonymous|myphoto_m)(?:\/|%2F)([0-9]{8})/ims";
		$pattern	=	"/http(?::\/\/|%3A%2F%2F)img(\d*)\.poco\.cn(?:\/|%2F)(?:mypoco(?:\/|%2F))?(?:myphoto|myphoto_anonymous|myphoto_m|best_pocoers)(?:\/|%2F)([0-9]{8})/ims";
		//$pattern	=	"/http(?::\/\/|%3A%2F%2F)(?:img|image)(\d*)\.poco\.cn(?:\/|%2F)(?:mypoco(?:\/|%2F))?(?:myphoto|myphoto_anonymous|myphoto_m|best_pocoers)(?:\/|%2F)([0-9]{8})/ims";

		$date_offset	=	$this->date_offset;
		$time_now	=	time();	//��ǰ������ʱ�䣬����������ܻ���ʱ���
		$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

		preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);


		if(!empty($search_result))
		{
			foreach ($search_result as $result)
			{
				$imgx = $result[1];		//img��
				$time_file = strtotime($result[2]);		//�ļ���ʱ���
				$replace = '';

				if ($time_file>0 && !in_array($imgx,array(2)))	//��Ҫimg2
				{
					if($time_now>($time_file+$date_offset*24*3600) && !in_array($result[0],$has_replace))
					{
						$has_replace[]	=	$result[0];
						//���⴦��
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
	 * ���˰�ͼƬ
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	/*function _parser_best_pocoers($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)img.poco.cn(?:\/|%2F)best_pocoers(?:\/|%2F)([0-9]{8})/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//��ǰ������ʱ�䣬����������ܻ���ʱ���
	$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

	preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

	if(!empty($pattern))
	{
	foreach ($search_result as $result)
	{
	$time_file = strtotime($result[1]);		//�ļ���ʱ���
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
	 * ����pet86_bbs��ƷͼƬ
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	/*function _parser_pet86_bbs($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)pet.poco.cn(?:\/|%2F)bbs(?:\/|%2F)attachments(?:\/|%2F)/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//��ǰ������ʱ�䣬����������ܻ���ʱ���
	$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

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
	 * ����POHTO��ƷͼƬ
	 *
	 * @param string $content	�ȴ������HTML����
	 * @return string
	 */
	/*function _parser_last_photo($content)
	{
	$pattern	=	"/http(?::\/\/|%3A%2F%2F)img.poco.cn(?:\/|%2F)photo(?:\/|%2F)([0-9]{8})/ims";

	$date_offset	=	$this->date_offset;
	$time_now	=	time();	//��ǰ������ʱ�䣬����������ܻ���ʱ���
	$has_replace	=	array();	//�Ѿ��滻���ַ������飬�����ظ��滻

	preg_match_all($pattern,$content,$search_result,PREG_SET_ORDER);

	if(!empty($pattern))
	{
	foreach ($search_result as $result)
	{
	$time_file = strtotime($result[1]);		//�ļ���ʱ���
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