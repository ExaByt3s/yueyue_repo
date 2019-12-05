<?php
/**
 * 活动公用方法类
 *
 * @author tom
 * @copyright 2011-1-25
 */



class event_system_class 
{
	/**
	 * 获取缩略图
	 *
	 * @param string $image
	 * @param int $size
	 * @return string $small_image
	 */
	public function get_small_image($image,$size=165)
	{
		$small_image = poco_resize_act_img_url($image, $size);
		/*
		if($this->isExistFile($small_image)){
			return $small_image;
		}else{
			if($size==145)
			{
				$small_image = $this->get_small_image($image,165);
				return $small_image;
			}else{
				return $image;
			}
		}
		*/
		return $small_image;	
	}	
	//判断文件是否存在
	public function isExistFile($url){
		$handle = @fopen($url,"r");  
		if($handle){return true;}  
		return false;
	}
	/**
	 * 根据当天的时间，返回此周末的时间
	 *
	 * @param string $date
	 * @return array $date_arr($Sat,$Sun)
	 */
	public function get_weekend_by_date()
	{
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$nowday = mktime(0,0,0,$month,$day,$year);
		$w=(int)date("w",$nowday);
		if($w==0)
		{
			$Sat = mktime(0,0,0,$month,$day - 1,$year);
			$Sun = mktime(0,0,0,$month,$day + 1,$year);
		}else{
			$t = 6 - $w;
			$Sat = mktime(0,0,0,$month,$day + $t,$year);
			$Sun = mktime(0,0,0,$month,$day + $t + 2,$year);
		}
		return array("Sat"=>$Sat,"Sun"=>$Sun);
	}	
	
	/**
	 * 根据类型名称返回类型数组名称
	 *
	 * @param string $name
	 * @return array $name_arr
	 */
	public function get_status_name_array_by_name($name)
	{
		global $my_app_event;
		$arr = $my_app_event->ini($name);
		$name_arr=array();
		if($name=='type' || $name=='medals')
		{
			foreach ($arr as $item)
			{
				foreach ($item as $key=>$value) 
				{
					$name_arr[$key] = $value[$name.'_name'];
				}
			}
		}elseif($name=='category' || $name=='medals_type')
		{
			foreach ($arr as $key=>$value)
			{
				$name_arr[$key] = $value[$name.'_name'];
			}
		}else{	
			
			foreach ($arr as $item) 
			{
				$name_arr[$item[$name]] = $item[$name.'_name'];
			}		
		}
		return $name_arr;
	}

/**
	 * 根据勋章ID取勋章对应图片
	 *
	 * @param string $medals
	 */
	public function get_medals_img_by_medals($medals)
	{
		global $my_app_event;
		$arr = $my_app_event->ini("medals");
		$img_arr=array();
		
		foreach ($arr as $item)
		{
			foreach ($item as $key=>$value)
			{
				$img_arr[$key] = $value['img_url'];
			}
		}
		
		return $img_arr[$medals];
	}	

	/* 根据活动小类取活动大类
	 *
	 * @param string $type_icon
	 */
	public function get_category_by_type_icon()
	{
		global $my_app_event;
		$arr = $my_app_event->ini("type");
		$new_arr=array();
		
		foreach ($arr as $category=>$type)
		{
			foreach ($type as $key=>$value)
			{
				if(empty($new_arr[$key]))
				{
					$new_arr[$key] = $category;
				}	
				
				
			}
		}
		return $new_arr;
	}	
	
	/**
	 * 功能：如果有传城市 id 则返回此 id 的城市资料，否则通过用户IP得出城市资料数组
	 *
	 * @param string $ip_address, $location_id
	 * @return array $location_arr
	 */
	public function get_location_arr($location_id='')
	{

		if(empty($location_id))
		{
			/*
			//对应ip对应城市
			$poco_member_obj = new poco_member_class();//POCO会员类操作
			global $_INPUT;
			$current_locate_info = $poco_member_obj->get_ip_location_info($_INPUT['IP_ADDRESS']);
			$location_id = (int)$current_locate_info['location_id'];	
			*/
			$location_id = (int)$_COOKIE['session_ip_location'];
		}	
		
		return $location_id;
	}
	
	/**
	 * 功能：取中文星期几
	 * 
	 * @param int $eng_week 
	 * @return string $chn_week
	 */
	public function get_chinese_week($eng_week)
	{
		switch ($eng_week)
		{
			case "0":
				$chn_week = "星期日";
				break;
			case "1":
				$chn_week = "星期一";
				break;
			case "2":
				$chn_week = "星期二";
				break;
			case "3":
				$chn_week = "星期三";
				break;
			case "4":
				$chn_week = "星期四";
				break;
			case "5":
				$chn_week = "星期五";
				break;
			case "6":
				$chn_week = "星期六";
				break;						
		}
		return $chn_week;
	}		
	/**
	 * 功能：通过location_id取级联城市名
	 * @param int location_id 
	 * @return array location_name_arr
	 */
	public function get_city_name_by_location_id($location_id)
	{
		$location_id= (int)$location_id;		
			
		$location_name1="";
		$location_name2="";
		if($location_id!="")	
		{
			$len = strlen($location_id);	
			if($len>=12)
			{
				$location_name = get_poco_location_name_by_location_id($location_id, true, true);
				$location_name1 = $location_name['level_1']['name'];
				$location_name2 = $location_name['level_2']['name'];
			}else{
				if($len>=9)
				{
					$location_name = get_poco_location_name_by_location_id(substr($location_id,0,9), true, true);
					if(!empty($location_name))	$location_name2=$location_name['level_1']['name'];
				}
				$location_name = get_poco_location_name_by_location_id(substr($location_id,0,6), true, true);
				if(!empty($location_name))	$location_name1=$location_name['level_1']['name'];
			}
			if(empty($location_name1) && empty($location_name2))
			{
				$location_name1="全国";
			}			
		}else{
			$location_name1="全国";
		}
		return array($location_name1,$location_name2);
	}	
	
	/**
	 * 功能：取头部城市切换列表
	 *
	 * @return array $city_list
	 */
	public function get_city_list()
	{
		global $my_app_event;
		$city_arr = $my_app_event->ini('city');	
		$city_list = array();
		foreach ($city_arr as $item) 
		{
			$city_list[$item['location_id']] = $item['city'];
		}
		return $city_list;
	}	
	
	/**
	 * 时间格式化
	 *
	 * @param int $timestamp
	 * @return string
	 */
	public function timeformat($timestamp)
	{
		$time = time() - $timestamp;
		if($time > 24*3600) 
		{
			$result = date("y/m/d H:i", $timestamp);
		} 
		elseif ($time > 3600) 
		{
			$result = intval($time/3600).'小时前';
		} 
		elseif ($time > 60) 
		{
			$result = intval($time/60).'分前';
		} 
		elseif ($time > 0) 
		{
			$result = $time.'秒前';
		} 
		else 
		{
			$result = "现在";
		}
		return $result;
	}

	/**
	 * 文本内容过滤
	 *
	 * @param string $html
	 * @return string
	 */
	static public function parse_html2($html)
	{
		$html = trim($html);
		$html = strip_tags($html,"<object> <param> <embed> <b> <marquee> <img> <div> <table> <tr> <td> <i> <font> <strong> <center> <ul> <ol> <li> <a> <span> <br> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <bgsound> <link> <map> <area> <sub> <sup>");
		$html = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $html);
	
		$search = 'abcdefghijklmnopqrstuvwxyz';
		$search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$search .= '1234567890!@#$%^&*()';
		$search .= '~`";:?+/={}[]-_|\'\\';
		for ($i = 0; $i < strlen($search); $i++)
		{
			$html = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $html); // with a ;
			$html = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $html); // with a ;
		}
	
		$ra1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
		$ra2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra = array_merge($ra1, $ra2);
	
		$found = true;
		while ($found == true)
		{
			$html_before = $html;
			for ($i = 0; $i < sizeof($ra); $i++)
			{
				$pattern = '/';
				for ($j = 0; $j < strlen($ra[$i]); $j++)
				{
					if ($j > 0)
					{
						$pattern .= '(';
						$pattern .= '(&#[x|X]0{0,8}([9][a][b]);?)?';
						$pattern .= '|(&#0{0,8}([9][10][13]);?)?';
						$pattern .= ')?';
					}
					$pattern .= $ra[$i][$j];
				}
				$pattern .= '/i';
				$replacement = substr($ra[$i], 0, 2).'<x>'.substr($ra[$i], 2);
				$html = preg_replace($pattern, $replacement, $html);
				if ($html_before == $html)
				{
					$found = false;
				}
			}
		}
	
		return   $html;
	}
		/**
	 * 文本内容过滤
	 *
	 * @param string $html
	 * @return string
	 */
	public function parse_html($html)
	{
		/*
		$html=trim($html);
		$html=strip_tags($html,"<object> <param> <embed> <b> <marquee> <p> <img> <div> <table> <tr> <td> <i> <font> <strong> <center> <ul> <ol> <li> <a> <span> <br> <h1> <h2> <h3> <h4> <h5> <h6> <hr> <bgsound> <link> <map> <area> <sub> <sup>");
		$html= preg_replace("/<\?.*\?>/i","",$html);
		$html= preg_replace("/on(\S+)=/i","",$html);
		$html= preg_replace("/(javascript):/i","\$1：",$html);
		$html= preg_replace("/&#(\d)+;/i","",$html);
		
		//无论何时都为这些加入透明模式，防止被flash覆盖整个页面导致没得救
		$html= preg_replace("/<embed(.*?)>/i","<embed\$1 wmode=\"transparent\">",$html);
		$html= preg_replace("/( wmode=\"transparent\")+/"," wmode=\"transparent\"",$html);
		*/
		
		include_once("/disk/data/htdocs233/mypoco/blog_v2/include/publish_html_filter_class.inc.php");
		$html = publish_html_filter_class::convert_output_html($html);
		
		
		return $html;
	}
	/**
	 * 活动积分，根据总评分转换乘数
	 *
	 * @param float $average_grade	//总评分的平均分
	 * @return float
	 */
	public function change_multiplier($average_grade)
	{
		
		$average_grade = floatval($average_grade);
		if($average_grade>=1 && $average_grade<2)
		{
			$multiplier = 0.5;
		}elseif ($average_grade>=2 && $average_grade<3)
		{
			$multiplier = 0.8;
		}elseif ($average_grade>=3 && $average_grade<4)
		{
			$multiplier = 1;
		}elseif ($average_grade>=4 && $average_grade<5)
		{
			$multiplier = 1.2;
		}elseif ($average_grade==5)
		{
			$multiplier = 1.5;
		}else{
			return false;
		}
		return $multiplier;
	}
	
	/**
	 * 检查是否后台管理员
	 *
	 * @param int $user_id
	 * @return bool
	 */
	public function check_is_admin($user_id)
	{
		$webcheck_patch = "/disk/data/htdocs232/poco/webcheck/";
		include_once $webcheck_patch."admin_function.php";
	
		$is_event_admin = admin_chk("event", "admin",  $user_id); //是否管理员
		
		if($is_event_admin)
		{
			return true;
		}else{
			return false;
		}
	}
	
		/**
	 * 取cmt的js的url
	 *
	 * @param int $event_id
	 * @param string $title
	 * @param int $user_id
	 * @param int $status
	 * @param string $type
	 * @return string $url
	 */
	public function get_cmt_js_url($event_id,$title,$user_id,$status,$category,$type)
	{
		$cmt_url = urlencode("http://event.poco.cn/event_detail.php?event_id=".$event_id);
		$cmt_title = urlencode($title);
		$user_name = html_entities(POCO::execute(array('member.get_user_nickname_by_user_id'), array($user_id)));
		$user_name = urlencode($user_name);
		if($status==0 || $category!=2)	//活动未开始时
		{
			$no_add_to_event_feature = '&no_add_to_event_feature=1';	//不出现“上传图片转为活动花絮”这个勾选框
		}
		//no_user_last_act=1 不出作者最新作品
		if($type=='cmt'){
			$no_cmt_custom_def_msg = urldecode('(暂时没有留言,就等你来留言啦!)');
			$cmt_js_url = "/cmt/?url=$cmt_url&title=$cmt_title&upload_pic=1&limit=0,8&tpl_name=channel_v2&anonymous=1&no_cmt_custom_def_msg=$no_cmt_custom_def_msg&author_id=$user_id&author_name=$user_name".$no_add_to_event_feature;
		}else{
			$no_cmt_custom_def_msg = urldecode('(暂时没有花絮，在活动留言上传图片可发布活动花絮。)');
			$cmt_js_url = "/cmt/?url=$cmt_url&title=$cmt_title&upload_pic=1&limit=0,8&tpl_name=channel_v2&anonymous=1&no_cmt_custom_def_msg=$no_cmt_custom_def_msg&author_id=$user_id&author_name=$user_name&get_specific_data=event_feature";
		}
		
		return $cmt_js_url;
	}
	
	/**
	 * 取cmt的html
	 *
	 * @param int $event_id
	 * @param string $title
	 * @param int $user_id
	 * @param int $category
	 * @param int $status
	 * @return string $url
	 */
	public function get_cmt_html($event_id,$title,$user_id,$category,$status)
	{
		$cmt_html = '';
		$cmt_html .= '
	<div class="wrapboxh_2 clearfix">
		<div class="listtab1_2">
          	<div class="toggles mt15 bgchange">
            	<ul class="toggle clearfix">';
		$cmt_js_url = $this->get_cmt_js_url($event_id,$title,$user_id,$status,$category,'cmt');
		$feature_js_url = $this->get_cmt_js_url($event_id,$title,$user_id,$status,$category,'feature');
		if($category==2)
		{
			if($status==0)	//判断活动是否已经开始，没开始时，默认显示留言，已经开始时，默认显示花絮
			{
				$cmt_class = "currenNow";
				$feature_class = "";
				$js_html = '<script language="javascript" id="cmt_js" src="'.$cmt_js_url.'"></script>';
			}else{ 
				$feature_obj = POCO::singleton('event_feature_class');
				$feature_coount = $feature_obj->get_feature_list_by_event_id($event_id, true, "");
				if($feature_coount>0)
				{
					$cmt_class = "";
					$feature_class = "currenNow";
					$js_html = '<script language="javascript" id="cmt_js" src="'.$feature_js_url.'"></script>';
				}else{
					$cmt_class = "currenNow";
					$feature_class = "";
					$js_html = '<script language="javascript" id="cmt_js" src="'.$cmt_js_url.'"></script>';
				}
			}
		
			$cmt_html .= '
              		<li><a class="'.$cmt_class.'" href="#this" onclick="return change_cmt_js(\'cmt\',\''.$cmt_js_url.'\')" id="cmt_tab">网友留言</a></li>';
			if($status!=0)
			{	
				$cmt_html .= '
	              		<li><a class="'.$feature_class.'" href="#this" onclick="return change_cmt_js(\'feature\',\''.$feature_js_url.'\')" id="feature_tab">活动花絮</a></li>
	            	';
			}
		}else{		//线上活动没有花絮
			$js_html = '<script language="javascript" id="cmt_js" src="'.$cmt_js_url.'"></script>';
			//如果是点评活动贴，某些模块不显示
			if($event_id==33564)
			{
				$cmt_html .= '
              		<li><a class="currenNow" id="cmt_tab">单张作品点评</a></li>
            	';	
			}else{
				$cmt_html .= '
	              		<li><a class="currenNow" id="cmt_tab">网友留言</a></li>
	            ';
			}
		}
		$cmt_html .= '
				</ul>
          	</div>
		  	<!--cmt内容-->
		    <div class="hdly_box mt15">
			    <div id="_cmt_tag_" align="left"></div>
			   	<script language="javascript">
			    	_max_img_w = 750;
			    </script>
		      	'.$js_html.'
	      	</div>
	      	<!--cmt内容-->
		</div>
	</div>  
		';
		
		return $cmt_html;
	}
	/**
	 * 取未开始，进行中，已结束的icon
	 *
	 * @param int $status
	 * @param int $event_id
	 * @param string $review
	 * @return string $html
	 */
	public function get_status_icon_by_status($status,$event_id,$review='',$setting='')
	{
		
		$html='';
		if(!empty($setting))
		{
			$setting_arr = unserialize($setting);
			if(!empty($setting_arr['event_cancel']))
			{
				$html='<span class="hd_ztico"><img src="images/ll_hdgl_icon4.png" /></span>';	//活动取消图标
				return $html;
			}
		}
		if($status==0)
		{
			$html='<span class="hd_ztico"><img src="images/ll_hdgl_ico2.png" /></span>';
		}elseif($status==1){
			$html='<span class="hd_ztico"><img src="images/ll_hdgl_ico1.png" /></span>';
		}else{
			if($review!='')
			{
				$html='<span class="hd_ztico"><a href="review.php?event_id='.$event_id.'" target="_blank"><img src="images/ll_hdgl_ico4.png" /></a></span>';
			}else{
				$html='<span class="hd_ztico"><img src="images/ll_hdgl_ico3.png" /></span>';
			}
		}
		return $html;
	}

    /**
     * 取未开始，进行中，已结束的icon(for新结构有场次的支付版活动)2014-5-4 author 星星
     * @param int $event_status
     * @param int $event_id
     * @param string $review
     * @return string $html
     */
    public function get_status_icon_by_status_v2($event_status,$event_id,$review='',$setting='')
    {

        $html='';
        /*if(!empty($setting))
        {
            $setting_arr = unserialize($setting);
            if(!empty($setting_arr['event_cancel']))
            {
                $html='<span class="hd_ztico"><img src="images/ll_hdgl_icon4.png" /></span>';	//活动取消图标
                return $html;
            }
        }*/
        if($event_status==0)
        {
            $html='<span class="hd_ztico"><img src="images/ll_hdgl_ico2.png" /></span>';
        }
        elseif($event_status==1)
        {
            $html='<span class="hd_ztico"><img src="images/ll_hdgl_ico1.png" /></span>';
        }
        else if($event_status==2)
        {
            if($review!='')
            {
                $html='<span class="hd_ztico"><a href="review.php?event_id='.$event_id.'" target="_blank"><img src="images/ll_hdgl_ico4.png" /></a></span>';
            }
            else
            {
                $html='<span class="hd_ztico"><img src="images/ll_hdgl_ico3.png" /></span>';
            }
        }
        else
        {
            $html='<span class="hd_ztico"><img src="images/ll_hdgl_icon4.png" /></span>';	//活动取消图标
        }

        return $html;

    }


	/**
	 * 取活动免费，大型，官方等图标
	 *
	 * @param string $type
	 * @param int $category
	 * @param int $limit_num
	 * @param int $budget
	 * @param int $is_authority
	 * @param int $is_recommend
	 * @param string $setting
	 * @return string $html
	 */
	public function get_have_icon_by_info($type,$category,$limit_num,$budget,$is_authority,$is_recommend,$setting)
	{
		$have_icon='';
		$setting = unserialize($setting);	
		if(empty($setting['have_icon']))
		{
			$budget = abs($budget);
			$big_budget_icon="";
			$authority_icon="";
			$recommend_icon="";
			if($category==2){		//只有线下才有 大型 和 免费
				if($limit_num==0 || $limit_num>=100)//大型活动图标
				{
					//if($type=='html')
					//$big_budget_icon = '<a class="hd_glico_02 FFFFFFcolor" href="#this" title="大型活动">大型</a>';
					//else
				//	$big_budget_icon = 'big_icon';
					$big_budget_icon = $this->get_have_icon_by_type($type,'big_icon');
				}else{	//没有 大型 时才检查是否 免费
					if( empty($budget))//免费图标
					{
					//	if($type=='html')
					//	$big_budget_icon = '<a href="#this" class="hd_glico_01 FFFFFFcolor" title="免费活动">免费</a>';
					//	else
					//	$big_budget_icon = 'free_icon';
						$big_budget_icon = $this->get_have_icon_by_type($type,'free_icon');
					}else{
						$big_budget_icon = "";
					}
				}
			}
	
			if( $is_authority=="1" ){//官方图标
				//if($type=='html')
				//$authority_icon = '<a class="hd_glico_04 FFFFFFcolor" href="#this" title="官方活动">官方</a>';
				//else
				//$authority_icon = 'au_icon';
				$authority_icon = $this->get_have_icon_by_type($type,'au_icon');
			}elseif( $is_authority=="2" )	//只有摄影外拍时，才判断是否俱乐部站长发起
			{
				//if($type=='html')
				//$authority_icon = '<a class="hd_glico_05 FFFFFFcolor" href="#this" title="俱乐部活动">俱乐部</a>';
				//else
				//$authority_icon = 'club_icon';
				$authority_icon = $this->get_have_icon_by_type($type,'club_icon');
			}else{
				$authority_icon = "";
			}
			if($big_budget_icon!="" && $authority_icon!="")	//上面两个图标其中一个没有内容时，才出推荐
			{}else{
				//推荐图标
				if( $is_recommend==1 )
				{
				//	if($type=='html')
				//	$recommend_icon = '<a class="hd_glico_03 FFFFFFcolor" href="#this" title="推荐活动">推荐</a>';
				//	else
				//	$recommend_icon = 'recommend_icon';
					$recommend_icon = $this->get_have_icon_by_type($type,'recommend_icon');
				}else{
					$recommend_icon = "";
				}
			}
			if($type=='html'){
				$have_icon = $big_budget_icon.$authority_icon.$recommend_icon;
			}else{
				$have_icon = $big_budget_icon;
				if(!empty($authority_icon))
				{
					if(!empty($have_icon))
					{
						$have_icon .= ',';
					}
					$have_icon .= $authority_icon;
				}
				if(!empty($recommend_icon))
				{
					if(!empty($have_icon))
					{
						$have_icon .= ',';
					}
					$have_icon .= $recommend_icon;
				}
			}
		}else{
			foreach ($setting['have_icon'] as $key=>$item)
			{
				$item_icon = $this->get_have_icon_by_type($type,$item);
				if($type=='html'){
					$have_icon .= $item_icon;
				}else{
					if(!empty($item_icon) && $key!=0)
					{
						$have_icon .= ',';
					}
					$have_icon .= $item_icon;
				}
			}
		}
		return $have_icon;
	}
	
	public function get_have_icon_by_type($return_type,$icon_type)
	{
		if($return_type=='html')
		{
			switch ($icon_type)
			{
				case "free_icon":
					$return_code = '<a href="#this" class="hd_glico_01 FFFFFFcolor" title="免费活动">免费</a>';
					break;
				case "big_icon":
					$return_code = '<a class="hd_glico_02 FFFFFFcolor" href="#this" title="大型活动">大型</a>';
					break;
				case "au_icon":
					$return_code = '<a class="hd_glico_04 FFFFFFcolor" href="#this" title="官方活动">官方</a>';
					break;
				case "club_icon":
					$return_code = '<a class="hd_glico_05 FFFFFFcolor" href="#this" title="俱乐部活动">俱乐部</a>';
					break;
				case "recommend_icon":
					$return_code = '<a class="hd_glico_03 FFFFFFcolor" href="#this" title="推荐活动">推荐</a>';
					break;					
			}
		}else{
			switch ($icon_type)
			{
				case "free_icon":
					$return_code = 'free_icon';
					break;
				case "big_icon":
					$return_code = 'big_icon';
					break;
				case "au_icon":
					$return_code = 'au_icon';
					break;
				case "club_icon":
					$return_code = 'club_icon';
					break;
				case "recommend_icon":
					$return_code = 'recommend_icon';
					break;					
			}
		}
		return $return_code;
	}
	
	public function get_join_level_list_by_type_icon($t)
	{
		require_once("/disk/data/htdocs233/mypoco/credit/conf/credit_level.conf.php");
		
		global $credit_level_title_array;
		$level_list = $credit_level_title_array[$t];
		
		$new_level_list = array();
		foreach ($level_list as $key=>$item)
		{
			$info = array("level"=>$key,"name"=>$item['title']);
			$new_level_list[] = $info;
		}
		return $new_level_list;
	}

	function merge_extra_input_data($data,$type_icon,$parameters=array())
	{
	    global $_INPUT;
	    $details_obj  = POCO::singleton('event_details_class');
	    $relate_audit = $details_obj->use_type_obj_and_function($type_icon, "get_related_info_and_audit", $parameters);
	    if(!empty($relate_audit))
	    {
	        
	        $relate_setting 		= $relate_audit['relate_setting'];
	        $data['relate_setting'] = serialize($relate_setting);

	    }
	    return $data;

	}
}
?>