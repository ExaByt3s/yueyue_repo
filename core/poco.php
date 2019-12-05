<?php

/**
 * POCO 基础框架
 *
 * @author ERLDY
 * @package core
 */

/**
 * DIRECTORY_SEPARATOR 的简写
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * POCO 框架基本库所在路径
 */
if (!defined("G_POCO_APP_PATH")) 
{
	define('G_POCO_APP_PATH', realpath(dirname(__FILE__)));
}
if (!defined("G_POCO_APP_URL"))
{
	define("G_POCO_APP_URL", "http://my.poco.cn/apps/");
}

////////////////// POCO底层环境载入部分 开始 //////////////////////////

/**
 * POCO相关include
 */
global $DB;
if( !isset($DB) )
{
	include_once(G_POCO_APP_PATH . '/../../php_common/sources/common.php');
}

/**
 * 分离变量依赖
 */
global $ibforums,$_INPUT;
if( !isset($_INPUT) )
{
	$_INPUT	= $ibforums->input;
}

/**
 * 登录用户
 */
global $login_id,$login_nickname;
if( !isset($login_id) )
{
	$login_id = 0;
	$login_nickname = '';
}

/**
 * 调试输出如果
 */
if( !function_exists("trace") )
{
	function trace($var,$title="",$server_id="")
	{
		global $_debug;
		if( empty($_debug) )
		{
			$_debug = $_REQUEST["_debug"];
		}
		
		if ($_debug)
		{
			echo "【trace {$title} (server_id{$server_id})：";
			var_dump($var);
			echo "】<br />\r\n";
		}
	}
}

/**
 * 替换sql中参数，例子：
 * $sql="SELECT * FROM a WHERE field1=:x_field1";
 * sqlSetParam($sql,"x_field1","值");
 * $sql将会替换成"SELECT * FROM a WHERE field1='值'"
 */
if (!function_exists("sqlSetParam"))
{
	function sqlSetParam(&$sql,$paramName,$paramValue)
	{
		$tmp = &$sql;

		if (get_magic_quotes_gpc())
		{
			$paramValue = stripslashes($paramValue);
		}

		$tmp=str_replace(':'.$paramName, "'".mysql_escape_string($paramValue)."'", $tmp);
		$tmp=str_replace('@'.$paramName, "'".mysql_escape_string($paramValue)."'", $tmp);
	}
}

if (!function_exists("microtime_float"))
{
	function microtime_float()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

/**
* 主数据库操作函数
* 返回格式是二维数组 $ret[$i]["fieldname"]
* 参数$bsingle_record为真时只取一行并且返回一维数组 $ret["fieldname"]
* 参数$server_id为查询数据库的服务器:
* 1 = $DB->query($sql,0,0);
* 2 = $DB->query($sql,0,2);
*/
if (!function_exists("db_simple_getdata"))
{
	function db_simple_getdata($sql,$bsingle_record=false,$server_id=false,$this_query_use_cache=false)
	{
		$this_query_use_cache=$this_query_use_cache*1;
		$_cache_key = __CLASS__."::".__FUNCTION__.serialize(func_get_args());

		$__query_start_time = microtime_float();

		//使用全局db实例
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();

		$server_id=$server_id*1;

		if (defined("G_DB_GET_REALTIME_DATA"))
		{
			if ($server_id*1==0) $server_id=1;
		}
		else if ($this_query_use_cache || defined("G_DB_USE_FULL_QUERYCACHE")) //开全查询cache
		{
			$poco_cache_obj = new poco_cache_class();
			$cache_data = $poco_cache_obj->get_cache($_cache_key);
			if (!empty($cache_data))
			{
				return $cache_data;
			}
		}

		if ($server_id===true || $server_id*1==1)
		{
			$DB->query($sql,0,0);
		}
		else if ($server_id>0) //用制定机
		{
			$DB->query($sql,0,$server_id);
		}
		else
		{
			$DB->query($sql);
		}

		$__query_end_time = microtime_float();
		$__query_time = sprintf("%0.4f",$__query_end_time - $__query_start_time);

		if ($DB->query_count>0)
		{
			while ($row=$DB->fetch_row())
			{
				$__db_simple_getdata_result_ret_arr[] = $row;
			}
		}

		if (count($__db_simple_getdata_result_ret_arr)==1 && $bsingle_record==true)
		{
			$__db_simple_getdata_result_ret_arr=$__db_simple_getdata_result_ret_arr[0];
		}

		//释放内存
		$DB->free_result();

		trace($sql, "{$__query_time}s", $server_id);

		if (!defined("G_DB_GET_REALTIME_DATA") &&
		(defined("G_DB_USE_FULL_QUERYCACHE") || $this_query_use_cache)) //开全查询cache
		{
			$_cache_time = max($this_query_use_cache, G_DB_USE_FULL_QUERYCACHE*1);

			if ($_cache_time<2)
			{
				$_cache_time = 1800; //默认30分钟
			}
			$poco_cache_obj->save_cache($_cache_key, $__db_simple_getdata_result_ret_arr, $_cache_time);
		}

		return $__db_simple_getdata_result_ret_arr;
	}
}

/**
 * db_simple_get_insert_id  取最后的insert_id，需要紧跟查询后调用
 * 例子： $log_id = db_simple_get_insert_id();
 * @return int
 */
if (!function_exists("db_simple_get_insert_id"))
{
	function db_simple_get_insert_id()
	{
		global $DB;
		return $DB->get_insert_id();
	}
}

/**
 * db_simple_get_affected_rows  取操作记录数
 * @return int
 */
if (!function_exists("db_simple_get_affected_rows"))
{
	function db_simple_get_affected_rows()
	{
		global $DB;
		return $DB->get_affected_rows();
	}
}

/**
 * check_limit_str  检查limit 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_limit_str("0,20");
 */
if (!function_exists("check_limit_str"))
{
	function check_limit_str($limitstr,$b_die=true)
	{
		$limitstr=trim($limitstr);
		$limitstr = str_replace(" ","",$limitstr);

		$c = preg_match("/^\d+,\d+$/i",$limitstr);

		if (!$c && $b_die) die(__FUNCTION__." limitstr error: {$limitstr}");

		return $c;
	}
}

/**
 * check_order_by 检查order by 传入的合法性，特别对于页面直接传的可以防止注入
 * 例子： check_order_by("hit_count DESC");
 */
if (!function_exists("check_order_by"))
{
	function check_order_by($orderby,$b_die=true)
	{
		$orderby=trim($orderby);

		$c = preg_match("/^(ORDER BY )?(([ ,A-Za-z0-9_])*( DESC)?)+$/i",$orderby);

		if (!$c && $b_die) die(__FUNCTION__."orderby error: {$orderby}");

		return $c;
	}
}

/**
 * 数组转换成update_sql字符串
 * 数组：array("FIELD1"=>"val1","FIELD2"="val2"') 
 * 转换成字符串：FIELD1='val1', FIELD2='val2'
 */
if (!function_exists("db_arr_to_update_str"))
{
	function db_arr_to_update_str($array)
	{
		$sign = "";
		$sql_str = "";
		foreach ($array as $k=>$v)
		{
			$sql_str.= $sign."{$k}=:x_{$k}";
			sqlSetParam($sql_str, "x_{$k}", $v);
			$sign = ",";
		}
		return $sql_str;
	}
}



/**
* 排序多维数组
*/
if (!function_exists("aasort"))
{
	function aasort(&$array, $args)
	{
		$sort_rule="";
		foreach($args as $arg)
		{
			$order_field = substr($arg, 1, strlen($arg));
			foreach($array as $array_row)
			{
				$sort_array[$order_field][] = $array_row[$order_field];
			}
			$sort_rule .= '$sort_array['.$order_field.'], '.($arg[0] == "+" ? SORT_ASC : SORT_DESC).',';
		}
		eval ("array_multisort($sort_rule".' $array);');
	}
}

////////////////// POCO底层环境载入部分 结束 //////////////////////////

//框架内部类文件
global $G_CLASS_FILES;
if (empty($G_CLASS_FILES))
{
    require G_POCO_APP_PATH . '/config/poco_class_files.php';
}

/**
 * 类 POCO 是为POCO子项目提供的核心基础类，提供了项目运行所需的基本服务
 *
 * 类 POCO 提供框架的基本服务，包括：
 *
 * -  设置的读取和修改；
 * -  类定义文件的搜索和载入；
 * -  对象的单子模式实现，以及对象注册和检索；
 * -  基本工具方法。
 *
 * @author ERLDY <erldy@126.com>
 * @package core
 */
if (!class_exists('POCO')) 
{
	class POCO
	{
	    /**
	     * 对象注册表
	     *
	     * @var array
	     */
	    private static  $_objects = array();
	
	    /**
	     * 类搜索路径
	     *
	     * @var array
	     */
	    private static $_class_path = array();
	
	    /**
	     * 类搜索路径的选项
	     *
	     * @var array
	     */
	    private static $_class_path_options = array();
	
	    /**
	     * 应用程序设置
	     *
	     * @var array
	     */
	    private static $_ini = array();
	
	    /**
	     * 获取指定的设置内容
	     *
	     * $option 参数指定要获取的设置名。
	     * 如果设置中找不到指定的选项，则返回由 $default 参数指定的值。
	     *
	     * @code php
	     * $option_value = POCO::ini('my_option');
	     * @endcode
	     *
	     * 对于层次化的设置信息，可以通过在 $option 中使用“/”符号来指定。
	     *
	     * 例如有一个名为 option_group 的设置项，其中包含三个子项目。
	     * 现在要查询其中的 my_option 设置项的内容。
	     *
	     * @code php
	     * // +--- option_group
	     * //   +-- my_option  = this is my_option
	     * //   +-- my_option2 = this is my_option2
	     * //   \-- my_option3 = this is my_option3
	     *
	     * // 查询 option_group 设置组里面的 my_option 项
	     * // 将会显示 this is my_option
	     * echo POCO::ini('option_group/my_option');
	     * @endcode
	     *
	     * 要读取更深层次的设置项，可以使用更多的“/”符号，但太多层次会导致读取速度变慢。
	     *
	     * 如果要获得所有设置项的内容，将 $option 参数指定为 '/' 即可：
	     *
	     * @code php
	     * // 获取所有设置项的内容
	     * $all = POCO::ini('/');
	     * @endcode
	     *
	     * @param string $option 要获取设置项的名称
	     * @param mixed $default 当设置不存在时要返回的设置默认值
	     *
	     * @return mixed 返回设置项的值
	     */
	    static function ini($option, $default = null)
	    {
	        if ($option == '/') return self::$_ini;
	
	        if (strpos($option, '/') === false)
	        {
	            return array_key_exists($option, self::$_ini)
	                    ? self::$_ini[$option]
	                    : $default;
	        }
	
	        $parts = explode('/', $option);
	        $pos =& self::$_ini;
	        foreach ($parts as $part)
	        {
	            if (!isset($pos[$part])) return $default;
	            $pos =& $pos[$part];
	        }
	        return $pos;
	    }
	
	    /**
	     * 修改指定设置的内容
	     *
	     * 当 $option 参数是字符串时，$option 指定了要修改的设置项。
	     * $data 则是要为该设置项指定的新数据。
	     *
	     * @code php
	     * // 修改一个设置项
	     * POCO::changeIni('option_group/my_option2', 'new value');
	     * @endcode
	     *
	     * 如果 $option 是一个数组，则假定要修改多个设置项。
	     * 那么 $option 则是一个由设置项名称和设置值组成的名值对，或者是一个嵌套数组。
	     *
	     * @code php
	     * // 假设已有的设置为
	     * // +--- option_1 = old value
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = old value
	     * //   \-- option3 = old value
	     *
	     * // 修改多个设置项
	     * $arr = array(
	     *      'option_1' => 'value 1',
	     *      'option_2' => 'value 2',
	     *      'option_group/option2' => 'new value',
	     * );
	     * POCO::changeIni($arr);
	     *
	     * // 修改后
	     * // +--- option_1 = value 1
	     * // +--- option_2 = value 2
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = new value
	     * //   \-- option3 = old value
	     * @endcode
	     *
	     * 上述代码展示了 POCO::changeIni() 的一个重要特性：保持已有设置的层次结构。
	     *
	     * 因此如果要完全替换某个设置项和其子项目，应该使用 POCO::replaceIni() 方法。
	     *
	     * @param string|array $option 要修改的设置项名称，或包含多个设置项目的数组
	     * @param mixed $data 指定设置项的新值
	     */
	    static function changeIni($option, $data = null)
	    {
	        if (is_array($option))
	        {
	            foreach ($option as $key => $value)
	            {
	                self::changeIni($key, $value);
	            }
	            return;
	        }
	
	        if (!is_array($data))
	        {
	            if (strpos($option, '/') === false)
	            {
	                self::$_ini[$option] = $data;
	                return;
	            }
	
	            $parts = explode('/', $option);
	            $max = count($parts) - 1;
	            $pos =& self::$_ini;
	            for ($i = 0; $i <= $max; $i ++)
	            {
	                $part = $parts[$i];
	                if ($i < $max)
	                {
	                    if (!isset($pos[$part])) $pos[$part] = array();
	                    $pos =& $pos[$part];
	                }
	                else
	                {
	                    $pos[$part] = $data;
	                }
	            }
	        }
	        else
	        {
	            foreach ($data as $key => $value)
	            {
	                self::changeIni($option . '/' . $key, $value);
	            }
	        }
	    }
	
	    /**
	     * 替换已有的设置值
	     *
	     * POCO::replaceIni() 表面上看和 POCO::changeIni() 类似。
	     * 但是 POCO::replaceIni() 不会保持已有设置的层次结构，
	     * 而是直接替换到指定的设置项及其子项目。
	     *
	     * @code php
	     * // 假设已有的设置为
	     * // +--- option_1 = old value
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = old value
	     * //   \-- option3 = old value
	     *
	     * // 替换多个设置项
	     * $arr = array(
	     *      'option_1' => 'value 1',
	     *      'option_2' => 'value 2',
	     *      'option_group/option2' => 'new value',
	     * );
	     * POCO::replaceIni($arr);
	     *
	     * // 修改后
	     * // +--- option_1 = value 1
	     * // +--- option_2 = value 2
	     * // +--- option_group
	     * //   +-- option2 = new value
	     * @endcode
	     *
	     * 从上述代码的执行结果可以看出 POCO::replaceIni() 和 POCO::changeIni() 的重要区别。
	     *
	     * 不过由于 POCO::replaceIni() 速度比 POCO::changeIni() 快很多，
	     * 因此应该尽量使用 POCO::replaceIni() 来代替 POCO::changeIni()。
	     *
	     * @param string|array $option 要修改的设置项名称，或包含多个设置项目的数组
	     * @param mixed $data 指定设置项的新值
	     */
	    static function replaceIni($option, $data = null)
	    {
	        if (is_array($option))
	        {
	            self::$_ini = array_merge(self::$_ini, $option);
	        }
	        else
	        {
	            self::$_ini[$option] = $data;
	        }
	    }
	
	    /**
	     * 删除指定的设置
	     *
	     * POCO::cleanIni() 可以删除指定的设置项目及其子项目。
	     *
	     * @param mixed $option 要删除的设置项名称
	     */
	    static function cleanIni($option)
	    {
	    	if ($option == '/') 
	    	{
	    		 self::$_ini = array();
	    	}
	        elseif (strpos($option, '/') === false)
	        {
	            unset(self::$_ini[$option]);
	        }
	        else
	        {
	            $parts = explode('/', $option);
	            $max = count($parts) - 1;
	            $pos =& self::$_ini;
	            for ($i = 0; $i <= $max; $i ++)
	            {
	                $part = $parts[$i];
	                if ($i < $max)
	                {
	                    if (!isset($pos[$part])) $pos[$part] = array();
	                    $pos =& $pos[$part];
	                }
	                else
	                {
	                    unset($pos[$part]);
	                }
	            }
	        }
	    }
	
	    /**
	     * 载入指定类的定义文件，如果载入失败抛出异常
	     *
	     * @code php
	     * POCO::loadClass('app_register_class');
	     * @endcode
	     *
	     * @param string $class_name 要载入的类
	     * @param string|array $dirs 指定载入类的搜索路径
	     * @param boolean $throw 在没有找到指定类时是否抛出异常
	     */
	    static function loadClass($class_name, $dirs = null, $throw = true)
	    {
	        if (class_exists($class_name, false) || interface_exists($class_name, false))
	        {
	            return;
	        }
	
	        global $G_CLASS_FILES;
	        $class_name_l = strtolower($class_name);
	        if (isset($G_CLASS_FILES[$class_name_l]))
	        {
	            require G_POCO_APP_PATH . DS . $G_CLASS_FILES[$class_name_l];
	            return $class_name_l;
	        }
	
	        $filename = "{$class_name}.inc.php";
	        if (!empty($dirs))
	        {
	        	if (!is_array($dirs))
	        	{
	        		$dirs = explode(PATH_SEPARATOR, $dirs);
	        	}
	        }
	        else
	        {
	        	$dirs = self::$_class_path;
	        }
	        
	        return self::loadClassFile($filename, $dirs, $class_name, '', $throw);
	    }
	
	    /**
	     * 添加一个类搜索路径
	     *
	     * 如果要使用 POCO::loadClass() 载入非框架中的类，需要通过 POCO::import() 添加类类搜索路径。
	     *
	     * @code php
	     * POCO::import('/www/app');
	     * @endcode
	     *
	     * @param string $dir 要添加的搜索路径
	     * @param boolean $case_sensitive 在该路径中查找类文件时是否区分文件名大小写
	     */
	    static function import($dir, $case_sensitive = false)
	    {
	        if (!isset(self::$_class_path[$dir]))
	        {
	        	if (DIRECTORY_SEPARATOR == '/') 
	        	{
		            $dir = str_replace('\\', DIRECTORY_SEPARATOR, $dir);
		        } 
		        else 
		        {
		            $dir = str_replace('/', DIRECTORY_SEPARATOR, $dir);
		        }
	        	$dir = rtrim($dir, '/\\');
	        	self::$_class_path[$dir] = $dir;
	        	self::$_class_path_options[$dir] = $case_sensitive;
	        }
	    }
	
	    /**
	     * 载入特定文件，并检查是否包含指定类的定义
	     *
	     * 该方法从 $dirs 参数提供的目录中查找并载入 $filename 参数指定的文件。
	     * 然后检查该文件是否定义了 $class_name 参数指定的类。
	     *
	     * 如果没有找到指定类，则抛出异常。
	     *
	     * @code php
	     * POCO::loadClassFile('Smarty.class.php', $dirs, 'Smarty');
	     * @endcode
	     *
	     * @param string $filename 要载入文件的文件名（含扩展名）
	     * @param string|array $dirs 文件的搜索路径
	     * @param string $class_name 要检查的类
	     * @param string $dirname 是否在查找文件时添加目录前缀
	     * @param string $throw 是否在找不到类时抛出异常
	     */
	    static function loadClassFile($filename, $dirs, $class_name, $dirname = '', $throw = true)
	    {
	        if (!is_array($dirs))
	        {
	            $dirs = explode(PATH_SEPARATOR, $dirs);
	        }
	        if ($dirname)
	        {
	            $filename = rtrim($dirname, '/\\') . DS . $filename;
	        }
	        $filename_l = strtolower($filename);
	
	        foreach ($dirs as $dir)
	        {
	            if (isset(self::$_class_path[$dir]))
	            {
	                $path = $dir . DS . (self::$_class_path_options[$dir] ? $filename : $filename_l);
	            }
	            else
	            {
	                $path = rtrim($dir, '/\\') . DS . $filename;
	            }
	
	            if (is_file($path))
	            {
	                require $path;
	                break;
	            }
	        }
	
	        // 载入文件后判断指定的类或接口是否已经定义
	        if (!class_exists($class_name, false) && ! interface_exists($class_name, false))
	        {
	            if ($throw)
	            {
	                throw new App_Exception(self::_t('%s 类或接口还没定义.', $class_name));
	            }
	            return false;
	        }
	        return $class_name;
	    }
	    
	    /**
	     * 载入指定的文件
	     *
	     * 该方法从 $dirs 参数提供的目录中查找并载入 $filename 参数指定的文件。
	     * 如果文件不存在，则根据 $throw 参数决定是否抛出异常。
	     *
	     * 与 PHP 内置的 require 和 include 相比，POCO::loadFile() 会多处下列特征：
	     *
	     * <ul>
	     *   <li>检查文件名是否包含不安全字符；</li>
	     *   <li>检查文件是否可读；</li>
	     *   <li>文件无法读取时将抛出异常。</li>
	     * </ul>
	     *
	     * @code php
	     * POCO::loadFile('my_file.php', $dirs);
	     * @endcode
	     *
	     * @param string $filename 要载入文件的文件名（含扩展名）
	     * @param array $dirs 文件的搜索路径
	     * @param boolean $throw 在找不到文件时是否抛出异常
	     *
	     * @return mixed
	     */
	    static function loadFile($filename, $dirs = null, $throw = true)
	    {
	        if (preg_match('/[^a-z0-9\-_.]/i', $filename))
	        {
	            throw new App_Exception(self::_t('%s 文件名或路径不正确.', $filename));
	        }
	
	        if (is_null($dirs))
	        {
	            //$dirs = array();
	            $dirs = self::$_class_path;
	        }
	        elseif (is_string($dirs))
	        {
	            $dirs = explode(PATH_SEPARATOR, $dirs);
	        }
	        foreach ($dirs as $dir)
	        {
	            $path = rtrim($dir, '\\/') . DS . $filename;
	            if (is_file($path)) return include $path;
	        }
	
	        if ($throw) throw new App_Exception(self::_t('%s 文件名或路径不正确.', $filename));
	        return false;
	    }
	    
	    /**
	     * 返回指定对象的唯一实例
	     *
	     * POCO::singleton() 完成下列工作：
	     *
	     * <ul>
	     *   <li>在对象注册表中查找指定类名称的对象实例是否存在；</li>
	     *   <li>如果存在，则返回该对象实例。</li>
	     *   <li>如果不存在，则载入类定义文件，并构造一个对象实例；</li>
	     *   <li>将新构造的对象以类名称作为对象名登记到对象注册表；</li>
	     *   <li>返回新构造的对象实例。</li>
	     * </ul>
	     *
	     * 使用 POCO::singleton() 的好处在于多次使用同一个对象时不需要反复构造对象。
	     *
	     * @code php
	     * // 在位置 A 处使用对象 My_Object
	     * $obj = POCO::singleton('My_Object');
	     * ...
	     * ...
	     * // 在位置 B 处使用对象 My_Object
	     * $obj2 = POCO::singleton('My_Object');
	     * // $obj 和 $obj2 都是指向同一个对象实例，避免了多次构造，提高了性能
	     * @endcode
	     *
	     * 当 $persistent 参数为 true 时，对象将被放入持久存储区。
	     * 有关 $persistent 参数的详细说明请参考 POCO::register() 方法。
	     *
	     * @param string $class_name 要获取的对象的类名字     
	     * @param mixed $init_data 初始化值:支持传数组
	     * @param bool  $b_single  是否返回唯一对象，默认是
	     *
	     * @return object 返回对象实例
	     */
	    static function singleton($class_name, $init_data = '', $b_single = true)
	    {  
	        $key = strtolower($class_name);
	        if (isset(self::$_objects[$key]) && $b_single == true)
	        {
	            return self::$_objects[$key];
	        }
	        self::loadClass($class_name);
	       
	        if (!empty($init_data)) 
	        {
	        	if (!is_array($init_data)) 
	        	{
	        		$init_data = array($init_data);
	        	}
	        	
	        	
				//if(in_array($_COOKIE['member_id'], array(7734111,56359494)))
				{
					$class = new ReflectionClass($class_name);
					$obj = $class->newInstanceArgs($init_data);
				}

				/*
				else
				{
					$class_args_str = '';
					foreach ($init_data as $value)
					{
						$class_args_str	.= '$value,';
					}
					$class_args_str	= rtrim($class_args_str, ",");
					eval("\$obj = new $class_name($class_args_str);");
				}
				*/
	        }
	        else 
	        {
	        	$obj = new $class_name;
	        }
	        return self::register($obj, $class_name);
	    }
	
	    /**
	     * 以特定名字在对象注册表中登记一个对象
	     *
	     * 开发者可以将一个对象登记到对象注册表中，以便在应用程序其他位置使用 POCO::registry() 来查询该对象。
	     * 登记时，如果没有为对象指定一个名字，则以对象的类名称作为登记名。
	     *
	     * @code php
	     * // 注册一个对象
	     * POCO::register(new MyObject());
	     * .....
	     * // 稍后取出对象
	     * $obj = POCO::regitry('MyObject');
	     * @endcode
	     *
	     * @code php
	     * if (!POCO::isRegistered('MyObject'))
	     * {
	     *      POCO::registry(new MyObject(), 'MyObject', true);
	     * }
	     * $app = POCO::registry('MyObject');
	     * @endcode
	     *
	     * @param object $obj 要登记的对象
	     * @param string $name 用什么名字登记
	     *
	     * @return object
	     */
	    static function register($obj, $name = null)
	    {
	        if (!is_object($obj))
	        {
	            // '%s 方法的 %s 参数应该是一个对象.
	            throw new App_Exception(self::_t('%s 方法的 %s 参数应该是一个对象.', __METHOD__, $obj));
	        }
	        if (is_null($name)) $name = get_class($obj);
	        $name = strtolower($name);
	        self::$_objects[$name] = $obj;
	        return $obj;
	    }
	
	    /**
	     * 查找指定名字的对象实例，如果指定名字的对象不存在则抛出异常
	     *
	     * @code php
	     * // 注册一个对象
	     * POCO::register(new MyObject(), 'obj1');
	     * .....
	     * // 稍后取出对象
	     * $obj = POCO::regitry('obj1');
	     * @endcode
	     *
	     * @param string $name 要查找对象的名字
	     *
	     * @return object 查找到的对象
	     */
	    static function registry($name)
	    {
	        $name = strtolower($name);
	        if (isset(self::$_objects[$name]))
	        {
	            return self::$_objects[$name];
	        }
	        // 没有注册名为 "%s" 的对象。
	        throw new App_Exception(self::_t('没有注册名为 "%s" 的对象。', $name));
	    }
	
	    /**
	     * 检查指定名字的对象是否已经注册
	     *
	     * @param string $name 要检查的对象名字
	     *
	     * @return boolean 对象是否已经登记
	     */
	    static function isRegistered($name)
	    {
	        $name = strtolower($name);
	        return isset(self::$_objects[$name]);
	    }
	
	    /**
	     * 读取指定的缓存内容，如果内容不存在或已经失效，则返回 false
	     *
	     * @param string $cache_id 缓存的 ID
	     * @param array  $policy 缓存策略
	     * @param string $backend_class 要使用的缓存服务
	     * @return mixed 成功返回缓存内容，失败返回 false
	     */
	    static function getCache($cache_id, array $policy = null, $backend_class = null)
	    {
	        static $obj = null;
	
	        if (is_null($backend_class))
	        {
	            if (is_null($obj))
	            {
	                $obj = self::singleton('POCO_Cache');
	            }
	            return $obj->get($cache_id, $policy);
	        }
	        else
	        {
	            $cache = self::singleton($backend_class);
	            return $cache->get($cache_id, $policy);
	        }
	    }
	    
	    /**
	     * 将变量内容写入缓存，失败抛出异常
	     *
	     * $data 参数指定要缓存的内容。如果 $data 参数不是一个字符串，则必须将缓存策略 serialize 设置为 true。
	     * $policy 参数指定了内容的缓存策略，如果没有提供该参数，则使用缓存服务的默认策略。
	     *
	     * 其他参数同 POCO::setCache()。
	     *
	     * @param string $cache_id 缓存的 ID
	     * @param mixed  $data 要缓存的数据
	     * @param array  $policy 缓存策略
	     * @param string $backend_class 要使用的缓存服务
	     */
	    static function setCache($cache_id, $data, array $policy = null, $backend_class = null)
	    {
	        static $obj = null;
	
	        if (is_null($backend_class))
	        {
	            if (is_null($obj))
	            {
	                $obj = self::singleton('POCO_Cache');
	            }
	            return $obj->set($cache_id, $data, $policy);
	        }
	        else
	        {
	            $cache = self::singleton($backend_class);
	            return $cache->set($cache_id, $data, $policy);
	        }
	    }
	
	    /**
	     * 删除指定的缓存内容
	     *
	     * 通常，失效的缓存数据无需清理。但有时候，希望在某些操作完成后立即清除缓存。
	     * 例如更新数据库记录后，希望删除该记录的缓存文件，以便在下一次读取缓存时重新生成缓存文件。
	     *
	     * @code php
	     * POCO::deleteCache($cache_id);
	     * @endcode
	     *
	     * @param string $id 缓存的 ID
	     * @param array $policy 缓存策略
	     * @param string $backend_class 要使用的缓存服务
	     */
	    static function deleteCache($cache_id, array $policy = null, $backend_class = null)
	    {
	        static $obj = null;
	
	        if (is_null($backend_class))
	        {
	            if (is_null($obj))
	            {
	                $obj = self::singleton('POCO_Cache');
	            }
	            return $obj->delete($cache_id, $policy);
	        }
	        else
	        {
	            $cache = self::singleton($backend_class);
	            return $cache->delete($cache_id, $policy);
	        }
	    }
	    
	    /**
	     * 对字符串或数组进行格式化，返回格式化后的数组
	     *
	     * $input 参数如果是字符串，则首先以“,”为分隔符，将字符串转换为一个数组。
	     * 接下来对数组中每一个项目使用 trim() 方法去掉首尾的空白字符。最后过滤掉空字符串项目。
	     *
	     * 该方法的主要用途是将诸如：“item1, item2, item3” 这样的字符串转换为数组。
	     *
	     * @code php
	     * $input = 'item1, item2, item3';
	     * $output = POCO::normalize($input);
	     * // $output 现在是一个数组，结果如下：
	     * // $output = array(
	     * //   'item1',
	     * //   'item2',
	     * //   'item3',
	     * // );
	     *
	     * $input = 'item1|item2|item3';
	     * // 指定使用什么字符作为分割符
	     * $output = POCO::normalize($input, '|');
	     * @endcode
	     *
	     * @param array|string $input 要格式化的字符串或数组
	     * @param string $delimiter 按照什么字符进行分割
	     *
	     * @return array 格式化结果
	     */
	    static function normalize($input, $delimiter = ',')
	    {
	        if (!is_array($input))
	        {
	            $input = explode($delimiter, $input);
	        }
	        $input = array_map('trim', $input);
	        return array_filter($input, 'strlen');
	    }
	
	    /**
	     * 用于 POCO 的类自动载入
	     *
	     * @param string $class_name
	     */
	    static function autoload($class_name)
	    {
	        self::loadClass($class_name, false);
	    }
	
	    /**
	     * 注册或取消注册一个自动类载入方法
	     *
	     * 该方法参考 Zend Framework。
	     *
	     * @param string $class 提供自动载入服务的类
	     * @param boolean $enabled 启用或禁用该服务
	     */
	    static function registerAutoload($class = 'POCO', $enabled = true)
	    {
	        if (!function_exists('spl_autoload_register'))
	        {
	        	require_once G_POCO_APP_PATH . '/debug/app_exception.php';
	            throw new App_Exception('POCO failed: spl_autoload does not exist in this PHP installation.');
	        }
	
	        if ($enabled === true)
	        {
	            spl_autoload_register(array($class, 'autoload'));
	        }
	        else
	        {
	            spl_autoload_unregister(array($class, 'autoload'));
	        }
	    }
		
	    /**
	     * 输出字符格式化
	     *
	     * @return string
	     */
	    static function _t()
	    {
	    	$args = func_get_args();
	    	return call_user_func_array('sprintf', $args);
	    }
	    
	    /**
		 * 根据调用接口参数生成唯一HASH值用户缓存执行结果
		 *
		 * @param array $api  接口API
		 * @param array $args 参数数组
		 * 
		 * @return int
		 */
		private static function _get_hashcode($api, $args)
		{
			$md5 = md5(urlencode(serialize($api))."::".urlencode(serialize($args)));
			$checksum = crc32($md5);
			return sprintf("%u",$checksum);
		}
		
	    /**
	     * API调用执行入口
	     *
	     * @param array|string $api    调用接口名称：如果接口需要初始化值，需要传入数组
	     * 							   如：array('member.get_friends_list', 30339651) 
	     * 							   第一个值是接口名称“.”前部分是系统模块名称，后部分是具体调用的方法
	     * 							   第二个值是对象初始化需要的值，如果初始化需要传多个参数，请使用数组
	     * @param array|string $args   所需参数：多个参数需要使用数组传入，只有一个参数时候可以直接传入
	     * 							   如：array('xxx','xxx') OR xxx     
	     * @param boolean      $use_cache 是否对执行结果缓存:注意必须是取数据列表的接口才使用，否则会导致意外异常
	     * @param int          $life_time 缓存时间：默认900秒
	     * @param boolean      $log    是否对所调用方法做执行情况的记录
	     * 
	     * @return array|string|boolean 成功返回执行结果
	     */
	    public static function execute($api, $args = array(), $use_cache = false, $life_time = 900, $log = false)
	    {
	    	// 记录程序开始执行时间
	    	$program_start_time = microtime_float();
	    	
	    	if (!empty($api) && is_array($api)) 
	    	{
	    		list($object, $init_data) = $api;
	    		list($model, $method) = explode('.', $object);
	    	}
	    	elseif (!empty($api) && !is_array($api))
	    	{
	    		list($model, $method) = explode('.', $api);
	    		$init_data = null;
	    	}
	    	else 
	    	{
	    		throw new App_Exception('接口不能为空');
	    	}
	    	if (!empty($args) && !is_array($args)) 
	    	{
	    		$args = array($args);
	    	}
	    	
	    	//判断调用的API是否允许调用
	    	$api_arr = self::ini('poco_api');
	    	if (empty($api_arr)) 
	    	{
	    		$api_arr = self::loadFile('poco_api_config.php', G_POCO_APP_PATH.'/config/');
	    	}   	
	    	if (!array_key_exists($method, $api_arr[$model])) 
	    	{
	    		if ($api_arr[$model][$method] === false) 
	    			throw new App_Exception("该API已停用:{$model}.{$method}");
	    		else 
	    			throw new App_Exception("请确认调用的API正确有效:{$model}.{$method}");
	    	}
	    	
	    	// 模块全名
	    	$model_full_name = "app_{$model}_class";
	    	
	    	// 如果对执行结果缓存  	
	    	if ($use_cache == true) 
	    	{
	    		$cache_key = self::_get_hashcode($api, $args);
	    		// 先取缓存，如果没缓存就执行接口，把结果缓存(这里使用POCO默认缓存)
	    		$cache_data  = self::getCache($cache_key, null, 'POCO_Cache');
	    		if (empty($cache_data)) 
	    		{
	    			$obj = empty($init_data) ? self::singleton($model_full_name) : self::singleton($model_full_name, $init_data, false);
	    			$ret = call_user_func_array(array($obj, $method), $args);
	    			// 只有数组的时候才缓存
	    			if (is_array($ret)) 
	    			{
	    				self::setCache($cache_key, $ret, array('life_time'=>$life_time), 'POCO_Cache');
	    			}   			
	    		}
	    		else 
	    		{
	    			$ret = $cache_data;
	    			unset($cache_data);
	    		}  		
	    	}
	    	// 如果不适用缓存就直接执行接口
	    	else 
	    	{
	    		$obj = empty($init_data) ? self::singleton($model_full_name) : self::singleton($model_full_name, $init_data, false);
	    		$ret = call_user_func_array(array($obj, $method), $args);
	    	}
	    	
	    	// 指定要写执行日志
	    	if ($log === true) 
	    	{
	    		// 程序执行结束时间
	    		$program_execte_time = microtime_float()-$program_start_time;
	    		$app_name = self::$_objects['app']->ini('app_config/APP_NAME');
	    		$details = "{$model_full_name} 模块中的方法 {$method} 总执行时间:{$program_execte_time}";
	    		// 记录执行细节到数据库
	    		App_Log::recordToDataBase($app_name, $model_full_name, $method, $details);
	    	}
	    	return $ret;
	    }
	}
}
/**
 * App_Debug::dump() 的简写，用于输出一个变量的内容
 *
 * @param mixed $vars 要输出的变量
 * @param string $label 输出变量时显示的标签
 * @param boolean $return 是否返回输出内容
 *
 * @return string
 */
if (!function_exists(dump))
{
	function dump($vars, $label = null, $return = false)
	{
	    return App_Debug::dump($vars, $label, $return);
	}
}

/**
 * 设置 POCO 内置对象的自动载入
 */
POCO::registerAutoload();

//引入登录类
global $yue_login_id;
if( !defined('G_YUEYUE_CORE_LOGIN_SESSION_AUTHORISE') )
{
	//只做一次authorise，避免把当次请求退出的用户，重新登录。
	define('G_YUEYUE_CORE_LOGIN_SESSION_AUTHORISE', 1);
	include_once(G_PHP_COMMON_ROOT_PATH . '/../pai/include/pai_login_session_class.inc.php');
	$pai_login_session_obj = new pai_login_session_class();
	$pai_login_info = $pai_login_session_obj->authorise();
	$yue_login_id = $pai_login_info['yue_id'];
}
