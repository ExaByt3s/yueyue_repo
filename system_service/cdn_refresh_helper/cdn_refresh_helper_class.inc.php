<?
/**
 * 刷新poco的cdn缓存类
 * 
 */

class cdn_refresh_helper_class {
	
	//var $service_url = "http://ccms.chinacache.com/index.jsp?user=poco_refresh&pswd=PoCo@123&ok=ok";
	var $service_url1 = "http://ccms.chinacache.com/index.jsp?user=poco_refresh1&pswd=Poco_refresh1&ok=ok";
	var $service_url2 = "http://cdnmgr.efly.cc/cdn_node_mgr/node_task/recive_task.php?username=poco&password=poco&type=API";
	
	var $poco_squid_cache_host = "14.18.140.4";
	var $poco_squid_cache_port = 80;
	var $_poco_log_obj = array();
	
	var $err_no = 0;
	var $err_str = "";
	
	function cdn_refresh_helper_class() {
		
		if (false)
		{
			$this->_poco_log_obj = new poco_log_class ( 'cdn_refresh_helper' ); //初始化
			$this->_poco_log_obj->create_log (); //创建
		}
	}

	/**
	 * 调试输出如果
	 * @access private
	 */
	function _trace($var, $title = "") {
		global $_debug;
		if (empty ( $_debug )) {
			$_debug = $_REQUEST ["_debug"];
		}
		
		if ($_debug) {
			echo "【_trace " . $title . "：";
			var_dump ( $var );
			echo "】<br />\r\n";
		}
	}
	
	function __http_post($url, $post_data = "", $Cookie = "") {
		$url = parse_url ( $url );
		if (! $url)
			return "couldn't parse url";
		if (! isset ( $url ['port'] )) {
			$url ['port'] = 80;
		}
		if (! isset ( $url ['query'] )) {
			$url ['query'] = "";
		}
		
		@extract ( $url );
		
		if ($Cookie == "")
			$cookie_header = "";
		else
			$cookie_header = "Cookie: $Cookie";
		if (is_array ( $post_data )) {
			foreach ( $post_data as $k => $v ) {
				$o .= "$k=" . urlencode ( $v ) . "&";
			}
			$post_data = substr ( $o, 0, - 1 );
		}
		$length = strlen ( $post_data );
		
		$fp = fsockopen ( $host, $port, $errno, $errstr, 15 );
		if (! $fp) {
			echo "ERROR: $errno - $errstr<br>\n";
		} else {
			$whole_path = $path . ($query ? "?" : "") . $query;
			$str = "GET $whole_path HTTP/1.1\r\n";
			$str .= "Host: $host\r\n";
			$str .= "Content-Type: application/x-www-form-urlencoded\n";
			$str .= "Content-Length: $length\n";
			//$str.= "Connection: Keep-Alive\n";
			$str .= "Cache-Control: no-cache\n";
			
			$str .= $cookie_header;
			$str .= "Connection: Close\n\n$post_data";
			fputs ( $fp, $str );
			while ( ! feof ( $fp ) ) {
				$strResultValue .= fread ( $fp, 10240 );
			}
			fclose ( $fp );
			
			return $strResultValue;
		}
	}
	
	function __clear_poco_squid_cache($url, $squid_host = null, $squid_port = null, $timeout_sec = 5) {

		return true;
		
		if (is_object($this->_poco_log_obj)) 
		{
			$this->_poco_log_obj->write_log ( "{$squid_host}:" . date ( "Y-m-d H:i:s" ) . ":begin:" . "\n" );
		}
		
		if (empty ( $squid_host )) {
			$squid_host = $this->poco_squid_cache_host;
		}
		
		if (empty ( $squid_port )) {
			$squid_port = $this->poco_squid_cache_port;
		}
		
		$err_no = 0;
		$err_str = "";
		$fp = fsockopen ( $squid_host, $squid_port, $err_no, $err_str, $timeout_sec );

		if (! $fp) {
			$this->err_no = $err_no;
			$this->err_str = $err_str;
			return false;
		} else {
			$url_info = parse_url ( $url );
			
			$path_no_host = $url_info ["path"];
			if (! empty ( $url_info ["query"] )) {
				$path_no_host .= "?" . $url_info ["query"];
			}
			if (! empty ( $url_info ["fragment"] )) {
				$path_no_host .= "#" . $url_info ["fragment"];
			}
			
			if (empty ( $path_no_host )) {
				$path_no_host = "/";
			}

			$post_str = "PURGE " . $path_no_host . " HTTP/1.1\r\n";
			$post_str .= "Host: " . $url_info ["host"] . "\r\n";
			$post_str .= "Connection: Close\n\n";
			
			$this->_trace ( $post_str, "clear poco squid cache request" );
			
			fwrite ( $fp, $post_str );
			
			$response = "";
			while ( ! feof ( $fp ) ) {
				$response .= fread ( $fp, 32 );
			}
			
			fclose ( $fp );

			$this->_trace ( $response, "clear poco squid cache response" );
			
			if (preg_match ( "/200 OK/", $response )) {
				$this->err_no = 0;
				return true;
			} else {
				$this->err_str = "$response";
				$this->err_no = 404;
				return false;
			}
		}
		//5
		if (is_object($this->_poco_log_obj)) 
		{
			$this->_poco_log_obj->write_log ( "{$squid_host}:" . date ( "Y-m-d H:i:s" ) . ":over:\n" );
		}
	}
	
	/**
	 * 刷新文件
	 *
	 * @param array $urls_arr
	 * @return boolean
	 */
	function refresh_urls($urls_arr, $b_refresh_cdn = true, $attemper = false) {
		
		return true;
		
		if ($attemper) {
			//include_once ("/disk/data/htdocs233/mypoco/poco_attemper_service/poco_attemper_service_class.inc.php");
			//设重试,生成地址
			$poco_attemper_service_obj = new poco_attemper_service_class ();
			foreach ( $urls_arr as $url_value ) {
				$url_value = urlencode ( $url_value );
				$call_url = "http://www1.poco.cn/cdn_refresh_helper/loca_index.php?urls={$url_value}&b_refresh_cdn={$b_refresh_cdn}";
				$system_command = "wget  --tries=1 {$call_url} -O /dev/stdout -o /dev/stdout";
				$task_id = $poco_attemper_service_obj->add_task_shell ( $system_command );
				//echo "$call_url";
			}
		} else {
			$this->__refresh_urls ( $urls_arr, $b_refresh_cdn );
		}
	}
	
	/**
	 * 强制刷新文件
	 *
	 * @param array $urls_arr
	 * @return boolean
	 */
	function __refresh_urls($urls_arr, $b_refresh_cdn = true) {
		
		return true;
		
		if (empty ( $urls_arr )) {
			return false;
		}
		
		if (! is_array ( $urls_arr )) {
			$urls_arr = array ($urls_arr );
		}
		
		$urls_arr = array_unique ( $urls_arr );
		
		foreach ( $urls_arr as $k => $v ) {
			//清poco的squid缓存
			$this->__clear_poco_squid_cache ( $v );
			//$this->__clear_poco_squid_cache ( $v, "172.18.5.41", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "172.18.5.42", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "172.18.5.43", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "172.18.5.44", 80, 5 );
			//$this->__clear_poco_squid_cache ( $v, "172.18.5.207", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "172.18.5.212", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "113.107.204.206", 80, 5 );

			//$this->__clear_poco_squid_cache ( $v, "61.153.183.43", 80, 5 );
			//$this->__clear_poco_squid_cache($v,"61.153.183.61",80,5);
			//$this->__clear_poco_squid_cache ( $v, "113.108.217.201", 80, 5 );
			//$this->__clear_poco_squid_cache($v,"222.41.52.124",80,5);
			//$this->__clear_poco_squid_cache($v,"221.11.86.146",80,5);
			//$this->__clear_poco_squid_cache ( $v, "121.9.249.23", 80, 5 );
			//$this->__clear_poco_squid_cache ( $v, "183.60.194.67", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "14.18.140.4", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "14.18.140.8", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "14.18.140.9", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "113.107.204.216", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "113.107.204.219", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "113.107.204.218", 80, 5 );
			//$this->__clear_poco_squid_cache ( $v, "183.60.194.83", 80, 5 );
			$this->__clear_poco_squid_cache ( $v, "14.18.140.10", 80, 5 );
			//$this->__clear_poco_squid_cache ( $v, "183.60.194.88", 80, 5 );
			//$this->__clear_poco_squid_cache ( $v, "183.60.210.29", 80, 5 );
        

			$urls_arr [$k] = urlencode ( urldecode ( $v ) );
		}
		
		
		
		if ($b_refresh_cdn) {
			
			if(is_object($this->_poco_log_obj))
			{
				$this->_poco_log_obj->write_log ( "CDN:" . date ( "Y-m-d H:i:s" ) . ":begin:\n" );
			}
			
			//刷南信cdn
			/*
			$get_url = $this->service_url1 . "&urls=" . implode ( "%0D%0A", $urls_arr );
			$response_1 = $this->__http_post ( $get_url, "", "" );
			$this->_trace ( $response_1, "cdnrefresh_response_1" );
			*/

			//刷另一个cdn
			$get_url = $this->service_url2 . "&url=" . implode ( ";", $urls_arr );
			$this->_trace ( $get_url, "cdnrefresh_url_2" );
			$response_2 = $this->__http_post ( $get_url, "", "" );
			$this->_trace ( $response_2, "cdnrefresh_response_2" );
			
			if(is_object($this->_poco_log_obj))
			{
				$this->_poco_log_obj->write_log ( "CDN:" . date ( "Y-m-d H:i:s" ) . ":over:\n" );
			}			
		}
		
		return true;
		
		if (preg_match ( "/whatsup: content=\"succeed\"/i", $response_1 ) && preg_match ( "/success/i", $response_2 )) {
			return true;
		} else {
			return false;
		}
	
	}
	
	/**
	 * 刷新目录
	 *
	 * @param array $dirs_arr
	 * @return boolean
	 */
	function refresh_dirs($dirs_arr) {
		
		return true;
		
		if (empty ( $dirs_arr )) {
			return false;
		}
		
		if (! is_array ( $dirs_arr )) {
			$dirs_arr = array ($dirs_arr );
		}
		
		$dirs_arr = array_unique ( $dirs_arr );
		
		foreach ( $dirs_arr as $k => $v ) {
			$dirs_arr [$k] = urlencode ( urldecode ( $v ) );
		}
		
		$get_url = $this->service_url1 . "&dirs=" . implode ( "%0D%0A", $dirs_arr );
		$this->_trace ( "&dirs=" . implode ( "%0D%0A", $dirs_arr ), "cdnrefresh_request" );
		
		$response = $this->__http_post ( $get_url, "", "" );
		
		$this->_trace ( $response, "cdnrefresh_response" );
		
		return true;
		
		if (preg_match ( "/whatsup: content=\"succeed\"/i", $response )) {
			return true;
		} else {
			return false;
		}
	}
}
?>
