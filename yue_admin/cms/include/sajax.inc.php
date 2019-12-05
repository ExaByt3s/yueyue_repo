<?php
/*
sajax v2 modified by Tony，
加入cache控制，加入cookies支持，解决多script冲突问题
*/
include_once "unescape.inc.php";



if (!isset($SAJAX_INCLUDED))
{

	/*
	* GLOBALS AND DEFAULTS
	*
	*/
	$sajax_debug_mode = 0;
	$sajax_export_list = array();
	$sajax_request_type = "GET";
	$sajax_remote_uri = "";
	$sajax_no_cache = true;
	
	$sajax_script_rand_id = rand().rand();
	/*
	* CODE
	*
	*/
	function sajax_init()
	{
	}

	$sajax_post_cookie = array();
	///加入cookies支持
	
	
	//需要在sajax_show_javascript()前设置
	function sajax_apply_cookie($cookiearr)
	{
		global $sajax_post_cookie;

		$sajax_post_cookie = $cookiearr;
	}

	//需要在页的顶调用
	function sajax_handle_cookie()
	{
		global $_REQUEST,$_COOKIE,$HTTP_COOKIE_VARS;
		$cookie = $_REQUEST["__cookie"];
		if (!empty($cookie))
		{
			$cookie= unserialize(strrev(base64_decode(strrev($cookie))));
			foreach ($cookie as $k=>$v)
			{
				setcookie($k,$v);
				$HTTP_COOKIE_VARS[$k]=$v;
				$_COOKIE[$k]=$v;
			}
		}
	}


	function sajax_get_my_uri()
	{
		global $REQUEST_URI,$_SERVER;

		if (empty($REQUEST_URI)) $REQUEST_URI = $_SERVER['REQUEST_URI'];

		return $REQUEST_URI;
	}

	$sajax_remote_uri = sajax_get_my_uri();

	function sajax_handle_client_request()
	{
		global $sajax_export_list;
		global $sajax_no_cache;

		$mode = "";

		if (! empty($_GET["rs"]))
		$mode = "get";

		if (!empty($_POST["rs"]))
		$mode = "post";

		if (empty($mode))
		return;

		if ($mode == "get")
		{
			if ($sajax_no_cache)
			{
				// Bust cache in the head
				header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");    // Date in the past
				header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
				// always modified
				header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
				header ("Pragma: no-cache");                          // HTTP/1.0
			}
			else
			{
				header ("Expires: " . gmdate("D, d M Y H:i:s",time()+3600*2) . " GMT");
			}
			$func_name = $_GET["rs"];
			if (! empty($_GET["rsargs"]))
			$args = $_GET["rsargs"];
			else
			$args = array();
		}
		else
		{
			$func_name = $_POST["rs"];
			if (! empty($_POST["rsargs"]))
			$args = $_POST["rsargs"];
			else
			$args = array();
		}


		for ($i=0;$i<count($args);$i++)
		{
			$args[$i] = unescape($args[$i]);
		}
		//print_r($sajax_export_list);
		//print_r($args);
		if (! in_array($func_name, $sajax_export_list))
		echo "-:$func_name not callable";
		else {
			echo "+:";
			$result = call_user_func_array($func_name, $args);
			echo $result;
		}
		exit;
	}

	function sajax_get_common_js()
	{
		global $sajax_debug_mode;
		global $sajax_request_type;
		global $sajax_remote_uri;
		global $sajax_no_cache;
		global $sajax_script_rand_id;

		$t = strtoupper($sajax_request_type);
		if ($t != "GET" && $t != "POST")
		return "// Invalid type: $t.. \n\n";

		ob_start();
		?>
		// remote scripting library
		// (c) copyright 2005 modernmethod, inc
		// v2 modified by Tony    poco.cn
		var sajax_debug_mode_<?php echo $sajax_script_rand_id;?> = <?php echo $sajax_debug_mode ? "true" : "false"; ?>;
		var sajax_request_type_<?php echo $sajax_script_rand_id;?> = "<?php echo $t; ?>";
		
		function sajax_debug_<?php echo $sajax_script_rand_id;?>(text) 
		{
			if (sajax_debug_mode_<?php echo $sajax_script_rand_id;?>)
				alert("RSD: " + text)
		}
 		function sajax_init_object() 
 		{
 			sajax_debug_<?php echo $sajax_script_rand_id;?>("sajax_init_object() called..")
 			
 			var A;
			try {
				A=new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e) {
				try {
					A=new ActiveXObject("Microsoft.XMLHTTP");
				} catch (oc) {
					A=null;
				}
			}
			if(!A && typeof XMLHttpRequest != "undefined")
				A = new XMLHttpRequest();
			if (!A)
				sajax_debug_<?php echo $sajax_script_rand_id;?>("Could not create connection object.");
			return A;
		}
		function sajax_do_call_<?php echo $sajax_script_rand_id;?>(func_name, args) 
		{
			var i, x, n;
			var uri;
			var post_data;
			
			uri = "<?php echo $sajax_remote_uri; ?>";
			if (sajax_request_type_<?php echo $sajax_script_rand_id;?> == "GET") {
				if (uri.indexOf("?") == -1) 
					uri = uri + "?rs=" + escape(func_name);
				else
					uri = uri + "&rs=" + escape(func_name);
				for (i = 0; i < args.length-1; i++) 
					uri = uri + "&rsargs[]=" + escape(args[i]);
				<?php echo $sajax_no_cache ? "uri = uri + \"&rsrnd=\" + new Date().getTime();" : ""; ?>
				post_data = null;
			} else {
				post_data = "rs=" + escape(func_name);
				for (i = 0; i < args.length-1; i++) 
					post_data = post_data + "&rsargs[]=" + escape(args[i]);
			}
			<?
			global $sajax_post_cookie;
			if (!empty($sajax_post_cookie))
			{
				if (is_array($sajax_post_cookie))
				{
					$cookie_str = strrev( base64_encode( strrev( serialize($sajax_post_cookie))));
					echo "
					if (sajax_request_type_{$sajax_script_rand_id} == 'GET') {
					uri = uri + '&__cookie={$cookie_str}';
					}
					else
					{
						post_data = post_data + '&__cookie={$cookie_str}';
					}
					";
				}
			}

			?>
			x = sajax_init_object();
			x.open(sajax_request_type_<?php echo $sajax_script_rand_id;?>, uri, true);
			if (sajax_request_type_<?php echo $sajax_script_rand_id;?> == "POST") {
				x.setRequestHeader("Method", "POST " + uri + " HTTP/1.1");
				x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			}
			x.onreadystatechange = function() {
				if (x.readyState != 4) 
					return;
				sajax_debug_<?php echo $sajax_script_rand_id;?>("received " + x.responseText);
				
				var status;
				var data;
				status = x.responseText.charAt(0);
				data = x.responseText.substring(2);
				if (status == "-") 
					alert("Error: " + data);
				else  
					args[args.length-1](data);
			}
			x.send(post_data);
			sajax_debug_<?php echo $sajax_script_rand_id;?>(func_name + " uri = " + uri + "/post = " + post_data);
			sajax_debug_<?php echo $sajax_script_rand_id;?>(func_name + " waiting..");
			delete x;
		}
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function sajax_show_common_js()
	{
		echo sajax_get_common_js();
	}

	// javascript escape a value
	function sajax_esc($val)
	{
		return str_replace('"', '\\\\"', $val);
	}

	function sajax_get_one_stub($func_name)
	{
		global $sajax_script_rand_id;
		
		ob_start();
		?>
		
		// wrapper for <?php echo $func_name; ?>
		
		function x_<?php echo $func_name; ?>() {
			sajax_do_call_<?php echo $sajax_script_rand_id;?>("<?php echo $func_name; ?>",
				x_<?php echo $func_name; ?>.arguments);
		}
		
		<?php
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}

	function sajax_show_one_stub($func_name)
	{
		echo sajax_get_one_stub($func_name);
	}

	function sajax_export()
	{
		global $sajax_export_list;

		$n = func_num_args();
		for ($i = 0; $i < $n; $i++) {
			$sajax_export_list[] = func_get_arg($i);
		}
	}

	$sajax_js_has_been_shown = 0;
	function sajax_get_javascript()
	{
		global $sajax_js_has_been_shown;
		global $sajax_export_list;
		
		$html = "";
		if (! $sajax_js_has_been_shown) {
			$html .= sajax_get_common_js();
			$sajax_js_has_been_shown = 1;
		}
		foreach ($sajax_export_list as $func) {
			$html .= sajax_get_one_stub($func);
		}
		return $html;
	}

	function sajax_show_javascript()
	{
		echo sajax_get_javascript();
	}



	$SAJAX_INCLUDED = 1;
}
?>
