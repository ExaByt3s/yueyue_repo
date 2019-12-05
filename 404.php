<?php
$__SCRIPT_URL = trim($_SERVER['SCRIPT_URL']);
	$__REDIRECT_SCRIPT_URI = $_SERVER['REDIRECT_SCRIPT_URI'];
	parse_str($_SERVER['REDIRECT_QUERY_STRING'], $__QUERY_GET_PARAM);

	$is_for_mobile = preg_match('/^\/mobile\//', $__SCRIPT_URL);
	$is_for_m = preg_match('/^\/m\//', $__SCRIPT_URL);
	$is_for_mwo = preg_match('/^\/mwo\//', $__SCRIPT_URL);
	$url_info = parse_url($__REDIRECT_SCRIPT_URI);

	/**
	 * 来源跟踪，打cookie标识
	 */
	if(isset($__QUERY_GET_PARAM['_from_source'])) 
	{
		setcookie('_from_source', $__QUERY_GET_PARAM['_from_source'], 0, '/', 'yueus.com');
	}

	// 大外拍 传递分享参数
	// hudw 2015.3.4
	if(isset($__QUERY_GET_PARAM['ph']))
	{
		$share_time = time()+3600*24*30;
		setcookie('share_event_id', $__QUERY_GET_PARAM['share_event_id'], $share_time, '/', 'yueus.com');
		setcookie('share_phone', base_convert($__QUERY_GET_PARAM['ph'],8,10), $share_time, '/', 'yueus.com');

	}

	/**
	 * 判断客户端
	 */
	$__is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? true : false;
	$__is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? true : false;
	$__is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? true : false;  
	$__is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? true : false; 
	$__is_pc = ($__is_android || $__is_iphone)  ? false : true;

	$domain = 'http://yp.yueus.com';
	if($__is_pc)
	{
		$domain = 'http://www.yueus.com';
	}

	$tester_user_id_arr = array(127361,100021,128071,100001,100029,100049,123241,101604,128071,128235,128279,128212,101615,100004,128692,131188,100028);
	//$tester_user_id_arr = array(100001);

	

	if($is_for_mobile)
	{
		header('HTTP/1.0 200 OK');
		include_once("/disk/data/htdocs232/poco/pai/mobile/router.php");
		exit();
	}
	else if($is_for_m)
	{
		$new_wx_url = 'http://yp.yueus.com/mall/user/';
		header("Location:{$new_wx_url}");
		exit();	
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && (preg_match("/^\/(\d+)(\/?)$/", $url_info["path"], $match) || preg_match("/^\/share_card\/(\d+)(\/?)$/", $url_info["path"], $match)))
	{
		$url = "http://www.yueus.com/mall/".$match[1];
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/topic\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		$url = "http://www.yueus.com/topic_v3/".$match[1];
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/topic_v3\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
		{
			//Version 3.0.0 专题
		    $url = $domain."/mall/user/test/topic/index.php?topic_id=".$match[1];	
		}
		else
		{
			//Version 3.0.0 专题
		    $url = $domain."/mall/user/topic/index.php?topic_id=".$match[1];	
		}
		
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/event_v3\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
		{
			//Version 3.0.0 活动
			$url = $domain."/mall/user/test/act/detail.php?event_id=".$match[1];	
		}
		else
		{
			//Version 3.0.0 活动
			$url = $domain."/mall/user/act/detail.php?event_id=".$match[1];	
		}
			
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/mall\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
		{
			//Version 3.0.0 商家主页
			$url = $domain."/mall/user/test/seller/index.php?seller_user_id=".$match[1]."&service_status=on_sell&goods_type=all";		
		}
		else
		{
			//Version 3.0.0 商家主页
			$url = $domain."/mall/user/seller/index.php?seller_user_id=".$match[1]."&service_status=on_sell&goods_type=all";		
		}
		
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/goods\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
		{
			//Version 3.0.0 商品页
			$url = $domain."/mall/user/test/goods/service_detail.php?goods_id=".$match[1]."&pid=1220102";	
		}
		else
		{
			//Version 3.0.0 商品页
			$url = $domain."/mall/user/goods/service_detail.php?goods_id=".$match[1]."&pid=1220102";	
		}
		
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/user_info\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
		{
			//Version 3.0.0 消费者主页
			$url = $domain."/mall/user/test/home/user_info.php?buyer_id=".$match[1]."&pid=1220123";	
		}
		else
		{
			//Version 3.0.0 消费者主页
			$url = $domain."/mall/user/home/user_info.php?buyer_id=".$match[1]."&pid=1220123";
		}
		
	    
	    header("Location:{$url}");
		exit;
	}
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/event\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
		$url = "http://www.yueus.com/event_v3/".$match[1];
	    
	    header("Location:{$url}");
		exit;
	}
	// 分享摄影卡
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/cameraman\/(\d+)(\/?)$/", $url_info["path"], $match))
	{
	  
	    $url = "http://www.yueus.com/user_info/".$match[1];
	    
	    header("Location:{$url}");
	    exit;
	}
	// 用于消费者app列表分享
	elseif (in_array($url_info["host"], array("yueus.com","www.yueus.com")) && preg_match("/^\/list\/(\S+)(\/?)$/", $url_info["path"], $match))
	{		

		$temp_arr = explode('/', $url_info["path"]);
		$return_query = urlencode($temp_arr[2]);
		$pid = $temp_arr[3];
		
		 

		if(preg_match("/^\/list\/(\d+)(\/?)$/", $url_info["path"], $match))
		{
			if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
			{						
				$url = $domain."/mall/user/test/seller/service_list.php?seller_user_id=".$match[1]."";	
			}
			else
			{			
				$url = $domain."/mall/user/seller/service_list.php?seller_user_id=".$match[1]."";	
			}
		}
		elseif(intval($return_query)==0 && intval($pid)>0)
		{
			// 分享服务列表			
				
			
			
			if(in_array($_COOKIE['yue_member_id'],$tester_user_id_arr))
			{
				$page_url_config_arr = include_once("/disk/data/htdocs232/poco/pai/mall/user/test/page_url_config.inc.php");
				
			}
			else
			{
				$page_url_config_arr = include_once("/disk/data/htdocs232/poco/pai/mall/user/page_url_config.inc.php");
			}

			$url = $page_url_config_arr[$pid];

			$temp_arr = parse_url($url);

			$title = mb_convert_encoding('服务列表', 'GBK','UTF-8');

			if($temp_arr['query'])
			{
				$url = $url.'&return_query='.$return_query.'&title='.$title;
			}	
			else
			{
				$url = $url.'?return_query='.$return_query.'&title='.$title;
			}	

			
		}				
	    
	    header("Location:{$url}");
		exit;
	}
	

	else
	{

		header("HTTP/1.1 404 not found");
		exit;
	}

?>