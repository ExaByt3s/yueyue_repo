<?
/**
 * poco 登陆session类
 * 
 * @author Tony
 */

// Report simple running errors

//需要的类
include_once(dirname(__FILE__) . "/../core/include/poco_tdg.inc.php");
include_once(dirname(__FILE__) . "/pai_user_class.inc.php");
include_once(dirname(__FILE__) . "/pai_log_class.inc.php");

class pai_login_session_class
{

	var $poco_cache_obj = null;

	var $cookie_domain;

	var $cache_hash_prefix = "_vistor_session_";

	var $session_id = '';

	var $session_user_id = '';

	var $session_info = array();

	/**
	 * cookie生存周期，单位：秒
	 *
	 * @var int
	 */
	var $cookie_lifetime = 2147483646; //cookie保存长久些，为了防止客户机时间错乱导致的cookie过期。


	var $b_save_passwd_cookie = true; //是否把密码hash保存进cookie，为了兼容以前的session暂时默认保存先。


	/**
	 * session缓存生存周期，单位：秒
	 *
	 * @var int
	 */
	var $session_lifetime = 7200; //2小时，如果b_save_passwd_cookie=true的时候,session失效可以自动重建


	var $session_lifetime_when_dont_save_passwd_cookie = 31104000; //一年，如果把密码hash保存进cookie的时候session失效了就不会重建，所以session设置保存长些


	/**
	 * 在线状态报活有效时间，单位：秒
	 *
	 * @var int
	 */
	var $online_stat_lifetime = 1200;
	
	/**
	 * 调试内容
	 *
	 * @var string
	 */
	var $_trace_str = '';
	var $_trace_log = false;

	function __construct( $cookie_domain = "yueus.com" )
	{

		$this->cookie_domain = $cookie_domain;
		
		$this->poco_cache_obj = new poco_cache_class();

		if (!$this->b_save_passwd_cookie)
		{
			$this->session_lifetime = $this->session_lifetime_when_dont_save_passwd_cookie;
		}

		$this->session_id = $this->_get_cookie_session_id();
		
	}

	/**
	 * 调试输出如果
	 * @access private
	 */
	function _trace( $var, $title = "" )
	{
		global $_debug;
		if (empty($_debug))
		{
			$_debug = $_REQUEST["_debug"];
		}

		if ($_debug)
		{
			echo "【" . __CLASS__ . "_trace " . $title . "：";
			var_dump($var);
			echo "】<br />\r\n";
		}
		
		$this->_trace_str .= "【{$title}】{$var}\n";
	}
	
	/**
	 * 释构函数，测试登录丢失问题
	 *
	 */
	function __destruct()
	{
//		if (in_array($_COOKIE['member_id'], array(7734111,44261385)) && !defined("__G_SAVED_TEST_LOGIN_SESSION_CLASS_LOG")) 
//		{
//			define("__G_SAVED_TEST_LOGIN_SESSION_CLASS_LOG",true);
//			$sql = "insert into `test`.`__test_login_session` (cookie_member_id, cookie, trace, url, add_time) values (:x_cookie_member_id, :x_cookie, :x_trace, :x_url, :x_add_time);\n";
//			$this->_sqlSetParam($sql, "x_cookie_member_id", $_COOKIE['member_id']);
//			$this->_sqlSetParam($sql, "x_cookie", var_export($_COOKIE,true));
//			$this->_sqlSetParam($sql, "x_trace", $this->_trace_str);
//			$this->_sqlSetParam($sql, "x_url", $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
//			$this->_sqlSetParam($sql, "x_add_time", time());
//			
//			$this->_trace('<font color=red>insert test login log</font>');
//
//			$log_flie_path = "/disk/data/htdocs233/mypoco/member/__set_login_session.log";
//
//			$fp = fopen($log_flie_path, "ab");
//			fwrite($fp, $sql);
//			fclose($fp);
//		}
	}
	
	/**
	* 替换sql中参数，例子：
	* $sql="SELECT * FROM a WHERE field1=:x_field1";
	* _sqlSetParam($sql,"x_field1","值");
	* $sql将会替换成"SELECT * FROM a WHERE field1='值'"
	* @access private
	*/
	function _sqlSetParam(&$sql,$paramName,$paramValue)
	{
		$tmp=&$sql;

		if (get_magic_quotes_gpc())
		{
			$paramValue = stripslashes($paramValue);
		}


		$tmp=str_replace(":".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
		$tmp=str_replace("@".$paramName,"'".mysql_escape_string($paramValue)."'",$tmp);
	}	

	/**
	 * 是否当关闭浏览器时就清除登陆状态
	 * @return boolean
	 */
	function _is_dont_remember_login_state()
	{
		$b_dont_remember_login_state = $_COOKIE['dont_remember_login_state'] * 1;

		return $b_dont_remember_login_state;
	}

	/**
	 * 设置关闭浏览器就清除登陆状态的cookie
	 *
	 * @param boolean $b_dont_remember
	 */
	function _set_cookie_dont_remember_login_state( $b_dont_remember )
	{
		$b_dont_remember = $b_dont_remember * 1;

		$this->_my_setcookie('dont_remember_login_state', $b_dont_remember, true, true);
	}

	/**
	 * 自动处理
	 *
	 */
	function start()
	{
		
		$this->_trace(print_r($_COOKIE,true),'cookie:');
		
		//会话session，仅记录，不作储存用途
		if (empty($_COOKIE["yue_session_id"]))
		{
			$this->_my_setcookie("yue_session_id", $this->_gen_new_session_id(), true);
		}
		

		$session_id = $this->_get_cookie_session_id();
		$cookie_member_id = $this->_get_cookie_member_id();

		if (empty($session_id))
		{
			$this->_trace("you'r new visitor: $session_id;");

			$session_id = $this->_create_guest_session(false);
		}
		
		if ($cookie_member_id > 0)
		{
			$this->_trace("trying to restore session:{$session_id}");

			if ($this->_check_session_info($session_id, $this->session_info))
			{
				$this->_trace("session auth ok.");

				//登记在线
				if (rand(1, 5) == 1)
				{
					$this->set_user_online_stat($this->session_info["yue_user_id"], true);
				}

			}
			else //cookie验证失败
			{
				$this->_trace("session auth err.");


				$this->_create_guest_session(true);

				$this->_trace(var_export($this->_get_session_info($session_id),true), 'cache session info');
			}
		}
		
		return $this->session_id;
	}

	/**
	 * 验证session信息
	 *
	 * @param string $session_id
	 * @param array $out_session_info   //引用返回session_info
	 * @return int 		//返回用户id或false
	 */
	function _check_session_info( $session_id, & $out_session_info )
	{
		if (empty($session_id))
		{			
			pai_log_class::add_log($log_arr, 'session_id is empty', 'login');
					
			return false;
		}

		if (!is_array($out_session_info))
		{
			$out_session_info = array();
		}

		$cookie_member_id = $this->_get_cookie_member_id();
		$cookie_pass_hash = $this->_get_cookie_pass_hash();
		$cookie_nickname = $this->_get_cookie_nickname();
		$cookie_activity_level = $this->_get_cookie_activity_level();
		$cookie_role = $this->_get_cookie_role();
		
		

		if ($cookie_member_id > 0)
		{
			$session_info = $this->_get_session_info($session_id);
			
			$log_arr['member_id'] = $cookie_member_id;
			$log_arr['password'] = $cookie_pass_hash;
			$log_arr['nickname'] = $cookie_nickname;
			$log_arr['session_info'] = $session_info;
			
			if ($session_info["yue_user_id"] == $cookie_member_id)
			{
				$this->_trace('cookie_member_id is ok');
				
				if ($session_info["yue_pwd_hash"] == $cookie_pass_hash || !$this->b_save_passwd_cookie)
				{
					/**
					 * 引用返回
					 */
					$out_session_info = array_merge($out_session_info, $session_info);
					
					return $cookie_member_id;
				}
				else
				{
					pai_log_class::add_log($log_arr, 'session_info pwd auth err', 'login');
						
					$this->_trace($session_info, "session_info pwd auth err");

					return false;
				}
			}
			else
			{
				$this->_trace("get session_info is empty");

				/**
				 * 对比密码
				 */
				if (!empty($cookie_pass_hash))
				{
					$pwd_matched = false;

					/**
					 * 断一下cookie验证hash，一小时内可以不验证pwd
					 */
					$session_auth_hash_expected = md5("poco_session_auth" . date("Y-m-d H") . $cookie_member_id . $cookie_pass_hash . $cookie_nickname);
					$session_auth_hash = $this->_get_cookie_session_auth_hash();
					if ($session_auth_hash_expected == $session_auth_hash)
					{
						$this->_trace("session auth COOKIE check ok!");

						$tmp = array();
						$tmp["nickname"] = $cookie_nickname;
						$tmp["pwd_hash"] = $cookie_pass_hash;
						$tmp["activity_level"] = $cookie_activity_level;
						$tmp["role"] = $cookie_role;

						$pwd_matched = true;
					}

					if ($pwd_matched != true)
					{
						$pai_user_obj = new pai_user_class(true);
						$tmp = $pai_user_obj->get_user_info($cookie_member_id);

						if ($cookie_pass_hash == $tmp["pwd_hash"])
						{
							$pwd_matched = true;

							$this->_set_cookie_session_auth_hash($session_auth_hash_expected);
						}
					}

					if ($pwd_matched == true)
					{
						$session_info = $this->save_login_session($session_id, $cookie_member_id, $tmp["nickname"], $tmp["pwd_hash"], $tmp["role"]);

						/**
						 * 引用返回
						 */
						$this->_trace($session_info, "session_info return by save_login_session");

						$out_session_info = array_merge($out_session_info, $session_info);

						return $cookie_member_id;
					}
					else
					{
						pai_log_class::add_log($log_arr, 'compare pwd from db ERR', 'login');
						
						$this->_trace("compare pwd from db ERR!");
						return false;
					}
				}
				else
				{
					pai_log_class::add_log($log_arr, 'cookie_pass_hash is empty', 'login');
						
					return false;
				}

			}
		}
		else
		{
			pai_log_class::add_log($log_arr, 'cookie_member_id is empty', 'login');
					
			$this->_trace('cookie_member_id is empty');
			return false;
		}
	}

	function save_login_session( $session_id, $user_id, $nickname, $pwd_hash, $role=null, $b_update_login_time = false )
	{
		$this->_trace(func_get_args(), __FUNCTION__);

		if (empty($session_id))
		{
			$session_id = $this->_create_guest_session(false);
		}

		$this->_set_cookie_session_id($session_id);
		$this->_set_cookie_member_id($user_id);
		$this->_set_cookie_nickname($nickname);
		$this->_set_cookie_activity_level($activity_level); 
		$this->_set_cookie_role($role); 

		if ($this->b_save_passwd_cookie)
		{
			$this->_set_cookie_pass_hash($pwd_hash);
		}

		$session_info = array();
		$session_info["yue_id"] = $user_id;
		$session_info["yue_user_id"] = $user_id;
		$session_info["yue_nickname"] = $nickname;
		$session_info["yue_session_id"] = $session_id;
		$session_info["yue_pwd_hash"] = $pwd_hash;
		/*********** 增加更新会员每天的最早登录时间 20090619 ***********/
		if ($b_update_login_time === true) 
		{
			$session_info["yue_last_login_time"] = date("Y-m-d");
		}	
		/*********** 增加更新会员每天的最早登录时间 20090619 ***********/

		////////////兼容ipb
		/*$user_mgroup_vars = $this->get_user_ipb_mgroup_vars($user_id);
		if (!empty($user_mgroup_vars))
		{
		$session_info = array_merge ($session_info, $user_mgroup_vars);
		}*/

		$this->_save_session_info($session_id, $session_info);

		$this->session_info = $session_info;
		return $session_info;

	}

	function _create_guest_session( $b_forcelogout = true )
	{
		$old_session_id = $this->session_id;
		
		if (empty($old_session_id))
		{
			$session_id = $this->_gen_new_session_id();
		}
		else
		{
			$session_id = $old_session_id;
		}

		$this->session_id = $session_id;
		$this->_set_cookie_session_id($session_id);

		if ($b_forcelogout)
		{
			//慢一下
			sleep(1);

			$this->save_login_session($session_id, 0, '', '', '');

			//登记不在线状态
			$cookie_member_id = $this->_get_cookie_member_id();
			if ($cookie_member_id > 0)
			{
				$this->set_user_online_stat($cookie_member_id, false, false, $session_id);
			}

		}


		$this->_trace("new guest session:{$session_id}");

		return $this->session_id;
	}

	function _my_setcookie( $cookie_name, $cookie_value, $b_clean_when_close = false, $set_cookie_var_also = false, $cookie_lifetime=0 )
	{
		$b_clean_when_close = $b_clean_when_close * 1;
		
		
		if ($cookie_lifetime) 
		{
			$exp = time() + $cookie_lifetime;
		}
		else
		{
			$exp = time() + $this->cookie_lifetime;
		}
		

		if ($b_clean_when_close)
		{
			$exp = null;
		}

		@setcookie($cookie_name, $cookie_value, $exp, "/", $this->cookie_domain);

		if ($set_cookie_var_also)
		{
			$_COOKIE[$cookie_name] = $cookie_value;
		}
	}

	function _set_cookie_session_id( $session_id )
	{
		$this->_trace("set_cookie_session_id:{$session_id}");

		$this->_my_setcookie("yue_g_session_id", $session_id,false,true);

		$this->session_id = $session_id;
	}

	function _set_cookie_member_id( $member_id )
	{
		$member_id = $member_id * 1;

		if ($member_id == 0) //如果是清除登陆信息的时候
		{
			$this->_my_setcookie("yue_member_id", $member_id, false);
			$this->_my_setcookie("yue_fav_userid", $member_id, false);
		}
		$this->_my_setcookie("yue_member_id", $member_id, $this->_is_dont_remember_login_state());
		$this->_my_setcookie("yue_fav_userid", $member_id, $this->_is_dont_remember_login_state());
		
		if ($member_id) 
		{
			$this->_my_setcookie("yue_remember_userid", $member_id);
		}
	
		$this->session_user_id = $member_id;
	}

	function _get_cookie_member_id()
	{
		$tmp = $_COOKIE["yue_member_id"];
		if (empty($tmp))
		{
			$tmp = $_COOKIE["yue_fav_userid"];
		}

		return $tmp * 1;
	}

	function _get_cookie_pass_hash()
	{
		$tmp = trim($_COOKIE["yue_pass_hash"]);

		return $tmp;
	}

	function _get_cookie_nickname()
	{
		$tmp = trim($_COOKIE["yue_nickname"]);

		return $tmp;
	}

	function _get_cookie_session_auth_hash()
	{
		$tmp = trim($_COOKIE["yue_session_auth_hash"]);

		return $tmp;
	}

	function _set_cookie_session_auth_hash( $auth_hash )
	{
		$this->_my_setcookie("yue_session_auth_hash", $auth_hash, true);
	}
	
	function _set_cookie_activity_level( $activity_level )
	{
		if (!is_null($activity_level)) $this->_my_setcookie("yue_activity_level", $activity_level, $this->_is_dont_remember_login_state());
	}

	function _set_cookie_role( $role )
	{
		if (!is_null($role)) $this->_my_setcookie("yue_role", $role, $this->_is_dont_remember_login_state());
	}	

	function _set_cookie_nickname( $nickname )
	{
		$this->_my_setcookie("yue_nickname", $nickname, $this->_is_dont_remember_login_state());
		$this->_my_setcookie("yue_fav_username", $nickname, $this->_is_dont_remember_login_state());
	}

	function _set_cookie_pass_hash( $pass_hash )
	{
		$this->_my_setcookie("yue_pass_hash", $pass_hash, $this->_is_dont_remember_login_state());
	}

	function _set_cookie_hide_online( $b_hide_online )
	{
		$b_hide_online = $b_hide_online * 1;

		if ($this->_get_cookie_hide_online() != $b_hide_online)
		{
			$this->_my_setcookie("yue_hide_online", $b_hide_online, $this->_is_dont_remember_login_state());
		}
	}

	function _get_cookie_hide_online()
	{
		return ($_COOKIE["yue_hide_online"] == 1);
	}

	function _get_cookie_session_id()
	{
		$tmp = $_COOKIE["yue_g_session_id"];
		return $tmp;
	}

	function _get_cookie_activity_level()
	{
		$tmp = $_COOKIE["yue_activity_level"];
		return $tmp;
	}	
	
	function _get_cookie_role()
	{
		$tmp = $_COOKIE["yue_role"];
		return $tmp;
	}
	
	function _gen_new_session_id()
	{
		$ret = rand() . uniqid(time() . microtime(), true);
		$ret = md5($ret);
		return $ret;
	}

	function _get_cache_key( $session_id )
	{
		return $this->cookie_domain . "_vistor_session_" . $session_id;
	}

	function _get_online_stat_cache_key( $user_id )
	{
		return $this->cookie_domain . "_user_web_online_stat_" . $user_id;
	}

	function _get_session_info( $session_id )
	{
		if (empty($session_id))
		{
			return false;
		}

		$cache_key = $this->_get_cache_key($session_id);

		$session_info = $this->poco_cache_obj->get_cache($cache_key, true);

		return $session_info;
	}

	function _save_session_info( $session_id, $session_info )
	{
		
		if (empty($session_id))
		{
			return false;
		}

		$cache_key = $this->_get_cache_key($session_id);

		return $this->poco_cache_obj->save_cache($cache_key, $session_info, $this->session_lifetime);
	}

	function _del_session_info( $session_id )
	{
		if (empty($session_id))
		{
			return false;
		}

		$cache_key = $this->_get_cache_key($session_id);

		return $this->poco_cache_obj->delete_cache($cache_key);
	}

	/**
	 * 设置用户在线状态信息
	 *
	 * @param int $user_id			//用户id
	 * @param boolean $b_online		//设置是否在线	
	 * @param boolean $b_hide_online	//设置是否在线状态是否隐藏
	 * @param string $session_id	//session_id，如果不传则用当前的
	 * @return boolean
	 */
	function set_user_online_stat( $user_id, $b_online, $b_hide_online = false, $session_id = null )
	{
		$user_id = $user_id * 1;
		$b_online = $b_online * 1;
		$b_hide_online = $b_hide_online * 1;

		if ($user_id < 1)
		{
			return false;
		}

		if (empty($session_id))
		{
			$session_id = $this->_get_cookie_session_id();
		}

		$cache_key = $this->_get_online_stat_cache_key($user_id);

		$online_info = array();
		$online_info["yue_user_id"] = $user_id;
		$online_info["yue_session_id"] = $session_id;
		$online_info["yue_last_activity_time"] = time();
		$online_info["yue_hide_online"] = $b_hide_online;
		$online_info["yue_current_page_ur"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if ($b_online)
		{
			$ret = $this->poco_cache_obj->save_cache($cache_key, $online_info, $this->online_stat_lifetime);
			$this->_set_cookie_hide_online($b_hide_online);
		}
		else
		{
			$ret = $this->poco_cache_obj->delete_cache($cache_key);
		}

		return $ret;
	}

	/**
	 * 获取用户在线状态信息
	 *
	 * @param int $user_id
	 * @param boolean $b_if_current_user_use_cookie	//如果当登陆是自己是否直接通过cookie的信息返回
	 * @return array
	 */
	function get_online_stat( $user_id, $b_if_current_user_use_cookie = true )
	{
		$user_id = $user_id * 1;

		if ($user_id < 1)
		{
			return false;
		}

		if ($user_id == $this->_get_cookie_member_id() && $b_if_current_user_use_cookie)
		{
			$online_info = array();
			$online_info["yue_user_id"] = $user_id;
			$online_info["yue_session_id"] = $this->_get_cookie_session_id();
			$online_info["yue_last_activity_time"] = time();
			$online_info["yue_hide_online"] = $this->_get_cookie_hide_online();
			$online_info["yue_current_page_ur"] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		else
		{
			$cache_key = $this->_get_online_stat_cache_key($user_id);
			$online_info = $this->poco_cache_obj->get_cache($cache_key, true);
		}

		if (!empty($online_info))
		{
			return $online_info;
		}
		else
		{
			return false;
		}
	}

	function get_client_ip()
	{
		//php获取ip的算法
		if ($_SERVER["HTTP_X_FORWARDED_FOR"])
		{
			$ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		else if ($_SERVER["HTTP_CLIENT_IP"])
		{
			$ip = $_SERVER["HTTP_CLIENT_IP"];
		}
		else if ($_SERVER["REMOTE_ADDR"])
		{
			$ip = $_SERVER["REMOTE_ADDR"];
		}
		else if (getenv("HTTP_X_FORWARDED_FOR"))
		{
			$ip = getenv("HTTP_X_FORWARDED_FOR");
		}
		else if (getenv("HTTP_CLIENT_IP"))
		{
			$ip = getenv("HTTP_CLIENT_IP");
		}
		else if (getenv("REMOTE_ADDR"))
		{
			$ip = getenv("REMOTE_ADDR");
		}
		else
		{
			$ip = "";
		}

		//$ip = preg_replace("/^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})/", "\\1.\\2.\\3.\\4", $ip);

		if (false!==strpos($ip, ', '))
		{
			$tmp = explode(', ',$ip);
			$ip = $tmp[0];
		}
		return $ip;
	}


	/**
	 * 以某个用户身份登陆，适用于注册后
	 *
	 * @param int $member_id	//用户id
	 * @param int $b_hide_online	//是否隐身登陆，默认是根据现有的设置=null，可以是true/false
	 * @return array	//返回用户信息
	 */
	function load_member( $member_id, $b_hide_online = null )
	{
		$member_id = $member_id * 1;

		if (is_null($b_hide_online))
		{
			$b_hide_online = $this->_get_cookie_hide_online();
		}

		$session_id = $this->_create_guest_session(false);

		$pai_user_obj = new pai_user_class();
		$tmp = $pai_user_obj->get_user_info($member_id);

		$this->save_login_session($session_id, $member_id, $tmp["nickname"], $tmp["pwd_hash"], $tmp["role"]);

		//登记在线
		$this->set_user_online_stat($this->session_info["yue_user_id"], true, $b_hide_online, $session_id);

		return $this->session_info;
	}

	function unload_member()
	{
		return $this->_create_guest_session(true);
	}

	/************************************
	* 下面这些函数是为了兼容旧版ipb的session类
	*/
	function authorise( $b_force_get_ipb_mgroup_vars = false )
	{
		$session_id = $this->start();

		$session_info = array();
	
		$session_info = $this->session_info;

		return $session_info;
	}

	function create_guest_session( $forcelogout = true )
	{
		$this->_create_guest_session($forcelogout);
	}

	function update_guest_session()
	{
	}

	function create_member_session()
	{
		return $this->start();
	}

	function update_member_session()
	{
	}

	function get_session_tbl_idx()
	{
		return "1";
	}

	function clean_delete_member_login_session()
	{

		$this->_create_guest_session(true);
	}

	
}

/*
$poco_login_session_obj = new poco_login_session_class();
$poco_login_session_obj->start();
*/
?>