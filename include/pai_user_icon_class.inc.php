<?php
/*
 * 用户头像操作类
 */

class pai_user_icon_class extends POCO_TDG
{
    //CDN
	var $icon_url = 'http://yue-icon-d.yueus.com/';
    var $seller_icon_url = 'http://seller-icon-d.yueus.com/';

    //看自己
    var $icon_url_me = 'http://yue-icon-e.yueus.com/';
    var $seller_icon_url_me = 'http://seller-icon-e.yueus.com/';

	var $icon_size_arr = array (32, 64, 86, 100, 165, 468 );
	
	/**
	 * 构造函数
	 *
	 */
	public function __construct()
	{
		$this->setServerId ( 101 );
		$this->setDBName ( 'pai_db' );
		$this->setTableName ( 'pai_user_icon_tbl' );
	}
	
	/*
	 * 添加或更换头像
	 * 
	 * @param array  $insert_data 
	 * 
	 * return bool 
	 */
	public function replace_user_icon($insert_data)
	{
		$insert_data ['user_id'] = ( int ) $insert_data ['user_id'];
		
		if (empty ( $insert_data ['user_id'] ))
		{
			throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':用户ID不能为空' );
		}
		
		$insert_data ['icon_url'] = yueyue_resize_act_img_url ( $insert_data ['icon_url'] );
		$insert_data ['add_time'] = time ();
		$insert_str = db_arr_to_update_str ( $insert_data );
		
		$sql = "REPLACE INTO pai_db.pai_user_icon_tbl SET " . $insert_str;
		$ret = db_simple_getdata ( $sql, false, 101 );
		
		return $ret;
	
	}
	
	/*
	 * 获取
	 * @param bool $b_select_count
	 * @param string $where_str 查询条件
	 * @param string $order_by 排序
	 * @param string $limit 
	 * @param string $fields 查询字段
	 * 
	 * return array
	 */
	public function get_user_icon_list($b_select_count = false, $where_str = '', $order_by = 'user_id DESC', $limit = '0,10', $fields = '*')
	{
		
		if ($b_select_count == true)
		{
			$ret = $this->findCount ( $where_str );
		} else
		{
			$ret = $this->findAll ( $where_str, $limit, $order_by, $fields );
		}
		return $ret;
	}
	
	public function get_user_icon_info($user_id)
	{
        $user_id = ( int ) $user_id;
		$ret = $this->find ( "user_id={$user_id}" );
		return $ret;
	}

    /*
     * 消费者头像
     * @param int $user_id
     * @param int $size
     * @param bool $force_reflesh 是否强制刷新
     */
	public function get_user_icon($user_id, $size = 86,$force_reflesh=false)
	{
		if ($user_id < 1)
		{
			return $this->icon_url . "default.jpg";
		}
			
		$size = intval ( $size );
		
		if ($size > 0 && ! in_array ( $size, $this->icon_size_arr ))
		{
			die ( "未注册的头象尺寸，允许的是：" . implode ( ",", $this->icon_size_arr ) );
		}
		
		if ($_COOKIE['yue_member_id']==$user_id || $force_reflesh) 
		{
			$resize_icon_path = $this->get_icon_filename($user_id,$size)."?".date('YmdHi');
            return $this->icon_url_me.$resize_icon_path;
		}else
		{
			$resize_icon_path = $this->get_icon_filename($user_id,$size)."?".date('Ymd');
            return $this->icon_url.$resize_icon_path;
		}

	}

    /*
     * 商家头像
     * @param int $user_id
     * @param int $size
     * @param bool $force_reflesh 是否强制刷新
     */
    public function get_seller_user_icon($user_id, $size = 86,$force_reflesh=false)
    {
        if ($user_id < 1)
        {
            return $this->icon_url . "default.jpg";
        }

        $size = intval ( $size );

        if ($size > 0 && ! in_array ( $size, $this->icon_size_arr ))
        {
            die ( "未注册的头象尺寸，允许的是：" . implode ( ",", $this->icon_size_arr ) );
        }

        if ($_COOKIE['yue_member_id']==$user_id || $force_reflesh)
        {
            $resize_icon_path = $this->get_icon_filename($user_id,$size)."?".date('YmdHi');
            return $this->seller_icon_url_me.$resize_icon_path;
        }else
        {
            $resize_icon_path = $this->get_icon_filename($user_id,$size)."?".date('YmdH');
            return $this->seller_icon_url.$resize_icon_path;
        }

    }


	
	
	public function get_icon_filename($user_id,$size)
	{
		$dir	= $this->get_icon_dir($user_id);
		if($size)
		{
			$path	= $dir.'/'.$user_id.'_'.$size.'.jpg';
		}
		else
		{
			$path	= $dir.'/'.$user_id.'.jpg';
		}
		return $path;
	}
	
	public function get_icon_dir($user_id)
	{
		$dir	= intval($user_id/10000);
		return $dir;
	}
	
	public function get_icon_path($user_id)
	{
		$dir	= $this->get_icon_dir($user_id);
		$path	= $dir.'/'.$user_id.'.jpg';
		return $path;
	}
	
	function file_get_contents($url)
	{
		if (preg_match('/^http(s)?:\/\//i', $url))
		{
			if(function_exists('curl_init'))
			{
				//远程
				$ch = curl_init();
				$timeout = 60;
				curl_setopt ($ch, CURLOPT_URL, $url);
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
				$file_contents = curl_exec($ch);
				curl_close($ch);

			}
			else
			{
				$file_contents = $this->__http_post($url);

			}
		}
		else 
		{
			//本地
			$file_contents = file_get_contents($url);

		}

		return $file_contents;
	}
	
	public function http_post($url, $post_data="", $Cookie="")
	{
		$url = parse_url($url);
		if (!$url) return "couldn't parse url";
		if (!isset($url['port'])) { $url['port'] = 80; }
		if (!isset($url['query'])) { $url['query'] = ""; }

		@extract($url);

		if($Cookie=="")
		$cookie_header = "";
		else
		$cookie_header = "Cookie: $Cookie";
		if(is_array($post_data))
		{
			foreach ($post_data as $k=>$v)
			{
				$o.= "$k=".urlencode($v)."&";
			}
			$post_data=substr($o,0,-1);
		}
		$length = strlen($post_data);

		$fp = fsockopen($host,$port,$errno,$errstr,15);
		if (!$fp) {
			return false;
		} else {
			$whole_path = $path.($query?"?" : "").$query;
			$method = $post_data==''?'GET':'POST';
			$str = "$method $whole_path HTTP/1.1\r\n";
			$str.= "Host: $host\r\n";
			$str.= "Content-Type: application/x-www-form-urlencoded\n";
			$str.= "Content-Length: $length\n";
			//$str.= "Connection: Keep-Alive\n";
			$str.= "Cache-Control: no-cache\n";
			$str.= "Accept-Encoding: none\n";
			$str.= "Content-Encoding: text\n";	//不用gzip

			$str.= $cookie_header;
			$str.= "Connection: Close\n\n$post_data";
			fputs ($fp, $str);
			while (!feof($fp)) {
				$strResultValue.=fread ($fp,10240);
			}
			fclose($fp);
			if(strpos($strResultValue,"\r\n"))
				$strResultValue = substr(strstr($strResultValue,"\r\n\r\n"),4);
			else 
				$strResultValue = substr(strstr($strResultValue,"\n\n"),2);
			return $strResultValue;
		}
	}

}

?>