<?php
function mobile_output($output_arr,$include_stat = true,$output_bol = false)
{
	$poco_services_api_obj = POCO::singleton('poco_services_api_class');
	$poco_services_api_obj->output_bol = $output_bol;

	// 将图片换成bgp线路
	define('POCO_CONTENT_OUPUT_BGP_PARSER_LINK', 1);
	
	if($_REQUEST['print']==1)
	{
		var_dump($output_arr);
	}
	
	
	if($include_stat)
	{
		if (class_exists("poco_run_time_class") && !defined("IGNORE_TPL_RUN_TIME_SCRIPT")) 
		{
			$page_totalrun_time = poco_run_time_class::end();
		}

		$real_output_arr['page_run_time'] = $page_totalrun_time;
		
	}

	$real_output_arr['result_data'] = $output_arr;

	$page_type = $output_arr['page_type'];

	//cdn链接替换
	
	$output_str =  $poco_services_api_obj->output($real_output_arr,$format="json",true,"",array('strip_tags' => false , 'entity' => false,'special_handle' => array('&lt;'=>1,'&gt;'=>1) ));
	

	$output_str = POCO::execute('common.content_output_cdn_parser',$output_str);

	/**
	 * 增加页面运行时间的输入	 
	 */
	/*if (class_exists("poco_run_time_class")) 
	{
	   $page_totalrun_time = poco_run_time_class::end();

	   if($page_totalrun_time > 0.8)
	   {
	      //调龙的接口，写log
	      
	      $backtrace = debug_backtrace();

		  $current_file = (string)$backtrace[0]['file'];

	      $ret = qing_app_model('mobile')->webpage_timeout_log($current_file, $page_totalrun_time, 'php_over_time',$page_type);				
	      
	   }			
	}*/
	


	echo header("Access-Control-Allow-Origin:*");
	echo $output_str;
	
	if(extension_loaded("zlib"))
	{
		ob_end_flush(); //输出buffer中的内容
	}
}

?>