<?php

	/**
	* SmartTemplate Extension 
	*/
	function smarttemplate_extension_sitestat_channel ( $url, $channel_code )
	{
		if (preg_match("/server1.adpolestar.net/",$url,$m)){
			return $url;
		}else{
			$channel_name = "";
			$channel_value = "";
			if(preg_match("/(stat_request_channel=)(\d+)/",$channel_code,$m))
			{

				$channel_name = $m[1];
				$channel_value = $m[2];
			}
			if(preg_match('/'.$channel_code.'/',$url))
			{
				return $url;
			}
			if(preg_match('/(stat_request_channel=[0-9_]+)/',$url,$m))
			{
				$url = str_replace($m[1],$m[1]."_".$channel_value,$url);
				return $url;
			}
			if(preg_match('/&/',$url)||preg_match('/\?/',$url))
			{
				return $url."&".$channel_code;
			}
			else
			{
				return $url."?".$channel_code;
			}
		}
	}

?>