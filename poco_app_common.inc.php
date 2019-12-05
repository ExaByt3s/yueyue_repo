<?php
/**
 * 应用框架项目公共文件
 */
define ( 'IGNORE_AMS_PV_POSTER', 1 );
//应用项目对象声明
global $my_app_pai;
//引入应用项目配置信息
$app_pai_config = require (dirname ( __FILE__ ) . '/config/app_config.php');
//引入应用框架主文件
require $app_pai_config ['POCO_APP_DIR'] . '/poco.php';
//引入应用项目程序初始化类
require dirname ( __FILE__ ) . '/include/poco_app_pai.inc.php';
//启动应用程序并返回应用程序对象唯一实例
$my_app_pai = POCO_APP_PAI::instance ( $app_pai_config );

define('G_YUEYUE_ROOT_PATH', realpath(dirname(__FILE__)));

//初始化用户数据
global $yue_login_id;
$__yue_user_info['nickname'] = get_user_nickname_by_user_id($yue_login_id);
$__yue_user_info['user_icon'] = get_user_icon($yue_login_id);
$__yue_user_info['user_id'] = $yue_login_id;
//地区引用
include_once(G_PHP_COMMON_ROOT_PATH . "/poco_location_fun.inc.php");

//cache 的随机数，改变该值清空所有cache
define ( 'CACHE_RAMDOM', 101 );

define( 'EVENT_API_DOMAIN' , 'http://pocoevent.yueus.com');

define( 'EVENT_API_HASH' , 'HSDF@#S!@$');

define ( 'TEST_PAI_USER_ID', '100410,100408,100057,100056,100055,100054,100053,100052,100051,100050,100049,100048,100047,100045,100044,100043,100042,100041,100039,100038,100037,100035,100034,100033,100032,100031,100030,100029,100028,100027,100026,100025,100024,100023,100022,100021,100020,100019,100018,100017,100016,100014,100013,100012,100011,100010,100009,100008,100007,100006,100005,100004,100003,100002,100001,100000,10002' );

define('TEST_COUPON_USER_ID','100000,100002,100003,103511,100021,100261,100020,100028,102710,100165,100410,100005,100006,100408,100832,100382,100036,106067,100007');

/**
 * 系统小秘书-10002信息
 * @param $to_user_id 	接收方的ID
 * @param $content    	接收的内容
 * @param $to_url     	传递的链接
 * @param $role       	接收方的身份（消费者端：yuebuyer;商家端：yueseller，不传默认为全部）
 **/
function send_message_for_10002($to_user_id, $content, $to_url, $role = 'all', $sys_type)
{
	//$url = 'http://172.18.5.211:8080/sendserver.cgi';
	$post_data ['send_user_id'] = ( string ) 10002;
	$post_data ['to_user_id'] = ( string ) $to_user_id;
	$post_data ['content'] = iconv ( 'gbk', 'utf-8', $content );
	if ($to_url)
	{
		$post_data ['link_url'] = 'http://yp.yueus.com' . $to_url;
		$post_data ['wifi_url'] = 'http://yp-wifi.yueus.com' . $to_url;
		$post_data ['media_type'] = 'notify';
		//$post_data ['link_url'] = 'yueyue://goto?type=inner_web&url=' . urlencode($post_data['link_url']) . '&wifi_url=' . urlencode($post_data['wifi_url']);
		//$post_data ['wifi_url'] = $post_data ['link_url'];
	} else
	{
		$post_data ['media_type'] = 'text';
	}

	if($sys_type == 'sys_msg') $post_data ['media_type'] = 'tips';

	$post_data = json_encode ( $post_data );
	$data ['data'] = $post_data;
	
	yueyue_message_base_service($data, $role);
	
	return TRUE;
}

function send_message_for_10005($to_user_id, $content, $to_url)
{
	$url = 'http://172.18.5.211:8080/sendserver.cgi';
	$post_data ['send_user_id'] = ( string ) 10005;
	$post_data ['to_user_id'] = ( string ) $to_user_id;
	$post_data ['content'] = iconv ( 'gbk', 'utf-8', $content );
	if ($to_url)
	{
		$post_data ['link_url'] = 'http://yp.yueus.com' . $to_url;
		$post_data ['wifi_url'] = 'http://yp-wifi.yueus.com' . $to_url;
		$post_data ['media_type'] = 'notify';
	} else
	{
		$post_data ['media_type'] = 'text';
	}
	$post_data = json_encode ( $post_data );
	$data ['data'] = $post_data;
	$matches = '';
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_HEADER, 1 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_COOKIE, $matches[1] );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$result = curl_exec ( $ch );
	curl_close ( $ch );
	
	save_nfc_log_v2 ( $data );
	
	return $data;
}

function send_message_for_10005_v2($to_user_id, $content, $to_url)
{
    $url = 'http://172.18.5.211:8080/sendserver.cgi';
    $post_data ['send_user_id'] = ( string ) 10005;
    $post_data ['to_user_id'] = ( string ) $to_user_id;
    $post_data ['content'] = iconv ( 'GBK', 'UTF-8', $content );
    if ($to_url)
    {
        $post_data ['link_url'] = 'http://yp.yueus.com' . $to_url;
        $post_data ['wifi_url'] = 'http://yp-wifi.yueus.com' . $to_url;
        $post_data ['media_type'] = 'notify';
    } else
    {
        $post_data ['media_type'] = 'text';
    }
    $post_data = json_encode ( $post_data );
    $data ['data'] = $post_data;
	$matches = '';

    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 1 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches[1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );

    save_nfc_log_v2 ( $data );

    return $data;
}


function send_message_for_10006($to_user_id, $content, $to_url)
{
	$url = 'http://172.18.5.211:8080/sendserver.cgi';
	$post_data ['send_user_id'] = ( string ) 10006;
	$post_data ['to_user_id'] = ( string ) $to_user_id;
	$post_data ['content'] = iconv ( 'gbk', 'utf-8', $content );
	if ($to_url)
	{
		$post_data ['link_url'] = 'http://yp.yueus.com' . $to_url;
		$post_data ['wifi_url'] = 'http://yp-wifi.yueus.com' . $to_url;
		$post_data ['media_type'] = 'notify';
	} else
	{
		$post_data ['media_type'] = 'text';
	}
	$post_data = json_encode ( $post_data );
	$data ['data'] = $post_data;
	$matches = '';
	
	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_HEADER, 1 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_COOKIE, $matches[1] );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$result = curl_exec ( $ch );
	curl_close ( $ch );
	
	save_nfc_log_v2 ( $data );
	
	return $result;
}


/*
 * 发送离线信息
 * @param int       $to_user_id  接收的用户ID
 * @param string    $content     接收的推送文案
 * @param string    $jump_url    要跳转的地址
 *
 * $return string   状态返回
 */
function send_offline_message($to_user_id, $content, $jump_url = 'yueyue://goto?type=inner_app&pid=1220079')
{
    $url = 'http://172.18.5.211:8080/sendmessage.cgi';

    $post_data['send_user_id']  = (string)10000;
    $post_data['to_user_id']    = (string)$to_user_id;
    $post_data['media_type']    = 'notify';
    $post_data['from']          = 'web';
    $post_data['link_url']      = $jump_url;
    $post_data['content']       = iconv ( 'gbk', 'utf-8', $content );

    $post_data          = json_encode ( $post_data );
    $data ['data']      = $post_data;
	$matches 			= '';
    $ch = curl_init ();
    curl_setopt ( $ch, CURLOPT_POST, 1 );
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt ( $ch, CURLOPT_COOKIE, $matches[1] );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
    $result = curl_exec ( $ch );
    curl_close ( $ch );

    return $result;
}

function save_nfc_log_v2($data)
{
	$sql_str = "INSERT INTO pai_log_db.nfc_log_v2_tbl(`return_data`) 
                VALUES (:x_data)";
	sqlSetParam ( $sql_str, 'x_data', $data ['data'] );
	//db_simple_getdata ( $sql_str, TRUE, 101 );
}

//活动密码生成
function create_code()
{
	$rndLength = 15;
	$rndLoop = ceil ( $rndLength / 3 );
	$rndString = '';
	for($i = 0; $i < $rndLoop; $i ++)
	{
		$rndString .= ( string ) rand ( 100, 999 );
	}
	$rndString = substr ( $rndString, 0, $rndLength );
	return $rndString;
}

//二维码加密
function qrcode_hash($event_id, $enroll_id, $code)
{
	$hash = 'oUI$%Dees!S^ertSDFG@#@#$2324DFG';
	return substr ( md5 ( $hash . $event_id . $enroll_id . $code ), 0, 20 );
}

function json_msg($code = '', $message = '', $data = array())
{
	if (! is_numeric ( $code ) || ! is_array ( $data ))
		return '';
	
	$result = array ("code" => $code, "message" => iconv ( "gbk", "utf-8", $message ), "data" => $data );

	
	
	echo json_encode ( $result );
}

/**
 * 获取图片问号后面的宽高
 * http://image227-c.poco.cn/mypoco/myphoto/20140804/14/17399207720140804145030025.jpg?318x240_120
 * @param string $img    图片URL
 * @return array
 */
function get_img_width_height($img)
{
	
	preg_match_all ( "#\?(.*)#", $img, $match );
	
	if ($match [1])
	{
		
		$size_str = $match [1] [0];
		preg_match_all ( '#(\d+)x(\d+)_(\d+)#', $size_str, $size_arr );
		
		if ($size_arr [1])
		{
			$width = $size_arr [1] [0];
		} else
		{
			$width = '';
		}
		
		if ($size_arr [2])
		{
			$height = $size_arr [2] [0];
		} else
		{
			$height = '';
		}
	} else
	{
		$width = '';
		$height = '';
	}
	
	$ret ["width"] = $width;
	$ret ["height"] = $height;
	
	return $ret;

}

function yueyue_resize_act_img_url($img_url, $size = '')
{
	if (in_array ( $size, array (32,64,86,100,145,165,260,320,440,468,640 ) ))
	{
		$size_str = '_' . $size;
	} 
	else
	{
		$size_str = '';
	}

	
	if (preg_match ( "/_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i", $img_url ))
	{
		$img_url = preg_replace ( "/_(32|64|86|100|145|165|260|320|440|468|640).(jpg|png|jpeg|gif|bmp)/i", "{$size_str}.$2", $img_url );
	} 
	else
	{ 
		$img_url = preg_replace ( '/.(jpg|png|jpeg|gif|bmp)/i', "{$size_str}.$1", $img_url ); 
	}
	
	
	return $img_url;
}



/**
 * 根据地理位置坐标获取地区信息
 * 
 * @param string $longitude 经度坐标
 * @param string $latitude  纬度坐标
 * @return array
 * <li>address      =>  位置全称</li>
 * <li>business     =>  商业</li>
 * <li>streetNumber =>  街道号</li>
 * <li>street       =>  街道</li>
 * <li>district     =>  地区</li>
 * <li>city         =>  城市</li>
 * <li>province     =>  省份</li>
 */
function yueyue_get_location_by_coordinate($latitude, $longitude)
{
	// 应用百度API接口
	$place_url = "http://api.map.baidu.com/geocoder/v2/?ak=KvVAK0v4Pt5qMBALyVjaGzoD&callback=renderReverse&location={$latitude},{$longitude}&output=xml&pois=0";
	$xml_place = file_get_contents ( $place_url );
	$xml_place = iconv ( "UTF-8", "GB2312//IGNORE", $xml_place );
	
	// 将xml中 utf-8 编码替换为gb2312
	$str = str_replace ( 'utf-8', 'gb2312', $xml_place );
	$objxml = simplexml_load_string ( $str );
	
	// 声明一个数组
	$array = array ();
	$address = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->formatted_address );
	$business = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->business );
	$streetNumber = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->addressComponent->streetNumber );
	$street = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->addressComponent->street );
	$district = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->addressComponent->district );
	$city = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->addressComponent->city );
	$province = iconv ( "UTF-8", "GB2312//IGNORE", $objxml->result->addressComponent->province );
	
	// 将所得地区信息放入到数组中
	$array = array ('address' => $address, 'business' => $business, 'streetNumber' => $streetNumber, 'street' => $street, 'district' => $district, 'city' => $city, 'province' => $province );
	
	// 返回数组
	return $array;
}

/**
 * 错误信息提示，并退出程序
 * 
 * @param string $msg 错误信息
 */
if (! function_exists ( "js_pop_msg" ))
{
	function js_pop_msg($msg, $b_reload = false, $url = NULL)
	{
		echo "<script language='javascript' defer >alert('{$msg}');";
		if ($url)
		{
			echo "if(window.name=='mainFrame')
			{
				window.location = '{$url}';
			}
			else
			{
			window.parent.location = '{$url}';
			}";
		}
		if ($b_reload)
			echo "window.parent.location.reload();";
		echo "</script>";
		exit ();
	}
}
if (! function_exists ( "get_client_ip" ))
{
	function get_client_ip()
	{
		//php获取ip的算法
		if ($_SERVER ["HTTP_X_FORWARDED_FOR"])
		{
			$ip = $_SERVER ["HTTP_X_FORWARDED_FOR"];
		} else if ($_SERVER ["HTTP_CLIENT_IP"])
		{
			$ip = $_SERVER ["HTTP_CLIENT_IP"];
		} else if ($_SERVER ["REMOTE_ADDR"])
		{
			$ip = $_SERVER ["REMOTE_ADDR"];
		} else if (getenv ( "HTTP_X_FORWARDED_FOR" ))
		{
			$ip = getenv ( "HTTP_X_FORWARDED_FOR" );
		} else if (getenv ( "HTTP_CLIENT_IP" ))
		{
			$ip = getenv ( "HTTP_CLIENT_IP" );
		} else if (getenv ( "REMOTE_ADDR" ))
		{
			$ip = getenv ( "REMOTE_ADDR" );
		} else
		{
			$ip = "";
		}
		
		//$ip = preg_replace("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $ip);
		

		if (false !== strpos ( $ip, ', ' ))
		{
			$tmp = explode ( ', ', $ip );
			$ip = $tmp [0];
		}
		return $ip;
	}
}
function get_user_nickname_by_user_id($user_id)
{
	$pai_user_obj = POCO::singleton ( 'pai_user_class' );
	return $pai_user_obj->get_user_nickname_by_user_id ( $user_id );
}

function get_seller_nickname_by_user_id($user_id)
{
    $seller_info = POCO::singleton('pai_mall_seller_class');
    $info=$seller_info->get_seller_profile_icon($user_id);
    //无商家资料默认拿消费者昵称
    if(!$info)
    {
        return get_user_nickname_by_user_id($user_id);
    }
    return $info['name'];
}

function get_relate_poco_id($user_id)
{
	$relate_obj = POCO::singleton ( 'pai_relate_poco_class' );
	return $relate_obj->get_relate_poco_id ( $user_id );
}

function get_relate_yue_id($poco_id)
{
	$relate_obj = POCO::singleton ( 'pai_relate_poco_class' );
	return $relate_obj->get_relate_yue_id ( $poco_id );
}

function get_user_icon($user_id, $size = 86, $force_reflesh=false)
{
	$user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
	return $user_icon_obj->get_user_icon ( $user_id, $size,$force_reflesh );
}

function get_seller_user_icon($user_id, $size = 86, $force_reflesh=false)
{
    $user_icon_obj = POCO::singleton ( 'pai_user_icon_class' );
    return $user_icon_obj->get_seller_user_icon ( $user_id, $size,$force_reflesh );
}

/*
 * 活动码格式转换（每3个数字空一格）
 */		
function activity_code_transfer($code,$split=3){
	$split_arr = str_split($code,$split);
	return implode(" ",$split_arr);
}


function yue_app_out_put($msg, $code)
{
	global $user_id,$app_name,$cp;
	
	$pai_user_obj = POCO::singleton ( 'pai_user_class' );
	
	$info_arr = POCO_APP_PAI::ini ( 'pai_tip' );
	
	$data ['code'] = $code;
	$data ['msg'] = $info_arr [$msg];
	
	if($user_id)
	{
		$user_info = $pai_user_obj->get_user_info($user_id);
		// 获取用户的授权信息
		$access_info = $cp->get_access_info($user_id, $app_name, true, false); //TODO 临时改成不缓存
		// 验证授权是否过期
        if($access_info[expire_time]-time() <= 3600*6)
		{
		  // 刷新授权
		  $access_info = $cp->refresh_access_info(NULL, NULL, NULL, $access_info);
		}
		
		$data['app_access_token'] = $access_info['access_token'];
		$data['app_expire_time']  = $access_info['expire_time'];
		$data['refresh_token']  = $access_info['refresh_token'];
		$data['user_id']  = $user_id;
		$data['nickname']  = $user_info['nickname'];
		$data['location_id']  = $user_info['location_id'];
		$data['role']  = $user_info['role'];
		$data['user_icon']  = get_user_icon($user_id, $size = 86);
		
		//日志
		pai_log_class::add_log($data, 'yue_app_out_put', 'ssl_login');
		
	}

	$options ['data'] = $data;
	$cp->output ( $options );
	exit ();
}


/*
 * 时间转换
 * $time 发布时间 如 1356973323
 * $str 输出格式 如 Y-m-d H:i:s
 * 半年的秒数为15552000，1年为31104000，此处用半年的时间
 */
function yue_time_format_convert($time,$str)
{
    isset($str)?$str:$str='Y-m-d H:i';
    $way = time() - $time;
    $r = '';
    if($way < 60){
        $r = '刚刚';
    }elseif($way >= 60 && $way <3600){
        $r = floor($way/60).'分钟前';
    }elseif($way >=3600 && $way <86400){
        $r = floor($way/3600).'小时前';
    }elseif($way >=86400 && $way <2592000){
        $r = floor($way/86400).'天前';
    }elseif($way >=2592000 && $way <15552000){
        $r = floor($way/2592000).'个月前';
    }else{
        $r = date("$str",$time);
    }
    return $r;
}

/*
 * 数组转编码，输出JSON
 */
function yue_iconv_arr_to_json($data,$from_code='GBK',$to_code='UTF-8')
{
	$arr = poco_iconv_arr($data,$from_code, $to_code);
    return json_encode($arr);
}

function pai_mall_load_config($name)//获取配置文件
{
	$return =array();
	$file = '/disk/data/htdocs232/poco/pai/yue_admin/task/config/'.$name.'_config.php';
	if(is_file($file))
	{
		$return = include($file);
	}
	return $return;
}

function pai_mall_change_str_in($str)
{
	return mysql_escape_string($str);
}

function yueyue_message_base_service($data, $sendserver = 'all')
{

	if(YUEYUE_HEYH_TEST == 1)
	{
		$log_data = serialize($data);
		$sendserver = $sendserver;
		$add_time = date('Y-m-d H:i:s');
		$source = 'poco_app_common:yueyue_message_base_service';

		$sql_str = "INSERT INTO pai_log_db.server_switching_information_log_tbl(log_data, sendserver, add_time, source)
                        VALUES ('{$log_data}', '{$sendserver}', '{$add_time}', '{$source}')";
		db_simple_getdata($sql_str, TRUE, 101);
	}

	/**
	$url = 'http://172.18.5.211:8080/sendserver.cgi';

	$ch = curl_init ();
	curl_setopt ( $ch, CURLOPT_POST, 1 );
	curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
	curl_setopt ( $ch, CURLOPT_URL, $url );
	curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt ( $ch, CURLOPT_COOKIE, $matches [1] );
	curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
	$result = curl_exec ( $ch );
	curl_close ( $ch );
	//var_dump($result);
	 * */

	if($sendserver == 'all' || $sendserver == 'yueseller')
	{
		$url = 'http://172.18.5.211:8080/sendmessage.cgi';
		$post_data = json_decode($data['data'], true);
		$post_data['send_user_role'] = 'yuebuyer';
		$data['data'] = json_encode($post_data);
		$ch = curl_init ();
		$matches = '';
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 10);
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_HEADER, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_COOKIE, $matches[1] );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_exec ( $ch );
		curl_close ( $ch );
	}

	if($sendserver == 'all' || $sendserver == 'yuebuyer') {
		$url = 'http://172.18.5.211:8080/sendmessage.cgi';
		$post_data = json_decode($data['data'], true);
		$post_data['send_user_role'] = 'yueseller';
		$data['data'] = json_encode($post_data);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_COOKIE, $matches[1]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_exec($ch);
		curl_close($ch);
	}
}

/*
 * 设备判断
 */
function mall_get_user_agent_arr()
{
    $is_weixin = stripos($_SERVER['HTTP_USER_AGENT'], 'micromessenger') ? 1 : 0;
    $is_android = stripos($_SERVER['HTTP_USER_AGENT'], 'android') ? 1 : 0;
    $is_iphone = stripos($_SERVER['HTTP_USER_AGENT'], 'iphone') ? 1 : 0;
    $is_ipad = stripos($_SERVER['HTTP_USER_AGENT'], 'ipad') ? 1 : 0;
    $is_yueyue_app = (preg_match('/yue_pai/',$_SERVER['HTTP_USER_AGENT'])) ? 1 : 0;

    if($is_android || $is_iphone || $is_ipad)
    {
        $is_mobile = 1;
        $is_pc = 0;
    }
    else
    {
        $is_mobile = 0;
        $is_pc = 1;
    }

    $result['is_weixin'] = $is_weixin;
    $result['is_android'] = $is_android;
    $result['is_iphone'] = $is_iphone;
    $result['is_yueyue_app'] = $is_yueyue_app;
    $result['is_mobile'] = $is_mobile;
    $result['is_pc'] = $is_pc;


    preg_match_all("/OS (\d+)_(\d+)_?(\d+)?/",$_SERVER['HTTP_USER_AGENT'],$os_match);

    $ios_version = $os_match[1][0];
    if($os_match[2][0])
    {
        $ios_version .= ".".$os_match[2][0];
    }
    if($os_match[3][0])
    {
        $ios_version .= ".".$os_match[3][0];
    }
    $result['ios_version'] = $ios_version;

    return $result;
}


/**
 * CURL调用活动系统接口
 * @param string $class_name 类名
 * @param string $method_name 方法名
 * @param array $param_arr 参数，空表示没参数
 * @param bool $b_static 是否静态方法
 * @param int $timeout 超时秒数
 * @param int $connect_timeout 连接超时秒数
 * @return mixed 如果出错的话就返回null
 */
function curl_event_data($class_name, $method_name, $param_arr=array(), $b_static=false, $timeout=20, $connect_timeout=5)
{
	//检查参数
	$class_name = trim($class_name);
	$method_name = trim($method_name);
	if( !is_array($param_arr) ) $param_arr = array();
	$param_str = serialize($param_arr);
	$b_static = $b_static?1:0;
	$timeout = intval($timeout);
	$connect_timeout = intval($connect_timeout);
	if( strlen($class_name)<1 || strlen($method_name)<1 )
	{
		die(__FUNCTION__ . " error. because {$class_name}::{$method_name}");
	}
	if( $timeout<1 ) $timeout = 20;
	if( $connect_timeout<1 ) $connect_timeout = 5;

	//调试信息
	if( function_exists('trace') )
	{
		trace(array('class_name'=>$class_name, 'method_name'=>$method_name, 'param_arr'=>$param_arr,), 'curl_event_data', ' event');
	}

	$url = 'http://pocoevent.yueus.com/event_curl_api.php';
	$yue_key = 'YUE_PAI_POCO!@#456';
	$hash = md5($class_name . '::' . $method_name . '::' . $param_str . '::' . $b_static . '::' . $yue_key);
	$fields = array(
		'class_name' => $class_name,
		'method_name' => $method_name,
		'param_str' => $param_str,
		'b_static' => $b_static,
		'hash' => $hash,
	);

	//调用远程接口
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
	curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	$response = curl_exec($ch);
	$log_errno = curl_errno($ch);
	$log_error = curl_error($ch);
	$log_getinfo = curl_getinfo($ch);
	curl_close($ch);

	//检查返回结果
	$rst = unserialize(trim($response));
	if( $response===false || $rst===false || !is_array($rst) || $rst['result']!=1 )
	{
		//记录错误日志 http://yp.yueus.com/logs/201509/03_curl_event_error.txt
		$log_arr = array(
			'func_get_args' => func_get_args(),
			'fields' => $fields,
			'curl_errno' => $log_errno,
			'curl_error' => $log_error,
			'curl_getinfo' => $log_getinfo,
			'response' => $response,
			'rst' => $rst,
		);
		pai_log_class::add_log($log_arr, 'curl_event_data', 'curl_event_error');

		return null;
	}

	/*
	 //临时记录日志 http://yp.yueus.com/logs/201509/03_curl_event.txt
	$log_arr = array(
		'func_get_args' => func_get_args(),
		'fields' => $fields,
		'response' => $response,
		'rst' => $rst,
	);
	pai_log_class::add_log($log_arr, 'curl_event_data', 'curl_event');
	*/

	return $rst['data'];
}



/**
 * xss过滤函数
 *
 * @param $string
 * @return string
 */
function mall_remove_xss($string) {
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = Array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');

    $parm2 = Array('onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');

    $parm = array_merge($parm1, $parm2);

    for ($i = 0; $i < sizeof($parm); $i++) {
        $pattern = '/';
        for ($j = 0; $j < strlen($parm[$i]); $j++) {
            if ($j > 0) {
                $pattern .= '(';
                $pattern .= '(&#[x|X]0([9][a][b]);?)?';
                $pattern .= '|(&#0([9][10][13]);?)?';
                $pattern .= ')?';
            }
            $pattern .= $parm[$i][$j];
        }
        $pattern .= '/i';
        $string = preg_replace($pattern, ' ', $string);
    }
    return $string;
}

