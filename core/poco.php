<?php

/**
 * POCO �������
 *
 * @author ERLDY
 * @package core
 */

/**
 * DIRECTORY_SEPARATOR �ļ�д
 */
define('DS', DIRECTORY_SEPARATOR);

/**
 * POCO ��ܻ���������·��
 */
if (!defined("G_POCO_APP_PATH")) 
{
	define('G_POCO_APP_PATH', realpath(dirname(__FILE__)));
}
if (!defined("G_POCO_APP_URL"))
{
	define("G_POCO_APP_URL", "http://my.poco.cn/apps/");
}

////////////////// POCO�ײ㻷�����벿�� ��ʼ //////////////////////////

/**
 * POCO���include
 */
global $DB;
if( !isset($DB) )
{
	include_once(G_POCO_APP_PATH . '/../../php_common/sources/common.php');
}

/**
 * �����������
 */
global $ibforums,$_INPUT;
if( !isset($_INPUT) )
{
	$_INPUT	= $ibforums->input;
}

/**
 * ��¼�û�
 */
global $login_id,$login_nickname;
if( !isset($login_id) )
{
	$login_id = 0;
	$login_nickname = '';
}

/**
 * ����������
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
			echo "��trace {$title} (server_id{$server_id})��";
			var_dump($var);
			echo "��<br />\r\n";
		}
	}
}

/**
 * �滻sql�в��������ӣ�
 * $sql="SELECT * FROM a WHERE field1=:x_field1";
 * sqlSetParam($sql,"x_field1","ֵ");
 * $sql�����滻��"SELECT * FROM a WHERE field1='ֵ'"
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
* �����ݿ��������
* ���ظ�ʽ�Ƕ�ά���� $ret[$i]["fieldname"]
* ����$bsingle_recordΪ��ʱֻȡһ�в��ҷ���һά���� $ret["fieldname"]
* ����$server_idΪ��ѯ���ݿ�ķ�����:
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

		//ʹ��ȫ��dbʵ��
		global $DB;

		global $__db_simple_getdata_result_ret_arr;
		$__db_simple_getdata_result_ret_arr=array();

		$server_id=$server_id*1;

		if (defined("G_DB_GET_REALTIME_DATA"))
		{
			if ($server_id*1==0) $server_id=1;
		}
		else if ($this_query_use_cache || defined("G_DB_USE_FULL_QUERYCACHE")) //��ȫ��ѯcache
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
		else if ($server_id>0) //���ƶ���
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

		//�ͷ��ڴ�
		$DB->free_result();

		trace($sql, "{$__query_time}s", $server_id);

		if (!defined("G_DB_GET_REALTIME_DATA") &&
		(defined("G_DB_USE_FULL_QUERYCACHE") || $this_query_use_cache)) //��ȫ��ѯcache
		{
			$_cache_time = max($this_query_use_cache, G_DB_USE_FULL_QUERYCACHE*1);

			if ($_cache_time<2)
			{
				$_cache_time = 1800; //Ĭ��30����
			}
			$poco_cache_obj->save_cache($_cache_key, $__db_simple_getdata_result_ret_arr, $_cache_time);
		}

		return $__db_simple_getdata_result_ret_arr;
	}
}

/**
 * db_simple_get_insert_id  ȡ����insert_id����Ҫ������ѯ�����
 * ���ӣ� $log_id = db_simple_get_insert_id();
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
 * db_simple_get_affected_rows  ȡ������¼��
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
 * check_limit_str  ���limit ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
 * ���ӣ� check_limit_str("0,20");
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
 * check_order_by ���order by ����ĺϷ��ԣ��ر����ҳ��ֱ�Ӵ��Ŀ��Է�ֹע��
 * ���ӣ� check_order_by("hit_count DESC");
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
 * ����ת����update_sql�ַ���
 * ���飺array("FIELD1"=>"val1","FIELD2"="val2"') 
 * ת�����ַ�����FIELD1='val1', FIELD2='val2'
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
* �����ά����
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

////////////////// POCO�ײ㻷�����벿�� ���� //////////////////////////

//����ڲ����ļ�
global $G_CLASS_FILES;
if (empty($G_CLASS_FILES))
{
    require G_POCO_APP_PATH . '/config/poco_class_files.php';
}

/**
 * �� POCO ��ΪPOCO����Ŀ�ṩ�ĺ��Ļ����࣬�ṩ����Ŀ��������Ļ�������
 *
 * �� POCO �ṩ��ܵĻ������񣬰�����
 *
 * -  ���õĶ�ȡ���޸ģ�
 * -  �ඨ���ļ������������룻
 * -  ����ĵ���ģʽʵ�֣��Լ�����ע��ͼ�����
 * -  �������߷�����
 *
 * @author ERLDY <erldy@126.com>
 * @package core
 */
if (!class_exists('POCO')) 
{
	class POCO
	{
	    /**
	     * ����ע���
	     *
	     * @var array
	     */
	    private static  $_objects = array();
	
	    /**
	     * ������·��
	     *
	     * @var array
	     */
	    private static $_class_path = array();
	
	    /**
	     * ������·����ѡ��
	     *
	     * @var array
	     */
	    private static $_class_path_options = array();
	
	    /**
	     * Ӧ�ó�������
	     *
	     * @var array
	     */
	    private static $_ini = array();
	
	    /**
	     * ��ȡָ������������
	     *
	     * $option ����ָ��Ҫ��ȡ����������
	     * ����������Ҳ���ָ����ѡ��򷵻��� $default ����ָ����ֵ��
	     *
	     * @code php
	     * $option_value = POCO::ini('my_option');
	     * @endcode
	     *
	     * ���ڲ�λ���������Ϣ������ͨ���� $option ��ʹ�á�/��������ָ����
	     *
	     * ������һ����Ϊ option_group ����������а�����������Ŀ��
	     * ����Ҫ��ѯ���е� my_option ����������ݡ�
	     *
	     * @code php
	     * // +--- option_group
	     * //   +-- my_option  = this is my_option
	     * //   +-- my_option2 = this is my_option2
	     * //   \-- my_option3 = this is my_option3
	     *
	     * // ��ѯ option_group ����������� my_option ��
	     * // ������ʾ this is my_option
	     * echo POCO::ini('option_group/my_option');
	     * @endcode
	     *
	     * Ҫ��ȡ�����ε����������ʹ�ø���ġ�/�����ţ���̫���λᵼ�¶�ȡ�ٶȱ�����
	     *
	     * ���Ҫ�����������������ݣ��� $option ����ָ��Ϊ '/' ���ɣ�
	     *
	     * @code php
	     * // ��ȡ���������������
	     * $all = POCO::ini('/');
	     * @endcode
	     *
	     * @param string $option Ҫ��ȡ�����������
	     * @param mixed $default �����ò�����ʱҪ���ص�����Ĭ��ֵ
	     *
	     * @return mixed �����������ֵ
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
	     * �޸�ָ�����õ�����
	     *
	     * �� $option �������ַ���ʱ��$option ָ����Ҫ�޸ĵ������
	     * $data ����ҪΪ��������ָ���������ݡ�
	     *
	     * @code php
	     * // �޸�һ��������
	     * POCO::changeIni('option_group/my_option2', 'new value');
	     * @endcode
	     *
	     * ��� $option ��һ�����飬��ٶ�Ҫ�޸Ķ�������
	     * ��ô $option ����һ�������������ƺ�����ֵ��ɵ���ֵ�ԣ�������һ��Ƕ�����顣
	     *
	     * @code php
	     * // �������е�����Ϊ
	     * // +--- option_1 = old value
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = old value
	     * //   \-- option3 = old value
	     *
	     * // �޸Ķ��������
	     * $arr = array(
	     *      'option_1' => 'value 1',
	     *      'option_2' => 'value 2',
	     *      'option_group/option2' => 'new value',
	     * );
	     * POCO::changeIni($arr);
	     *
	     * // �޸ĺ�
	     * // +--- option_1 = value 1
	     * // +--- option_2 = value 2
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = new value
	     * //   \-- option3 = old value
	     * @endcode
	     *
	     * ��������չʾ�� POCO::changeIni() ��һ����Ҫ���ԣ������������õĲ�νṹ��
	     *
	     * ������Ҫ��ȫ�滻ĳ���������������Ŀ��Ӧ��ʹ�� POCO::replaceIni() ������
	     *
	     * @param string|array $option Ҫ�޸ĵ����������ƣ���������������Ŀ������
	     * @param mixed $data ָ�����������ֵ
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
	     * �滻���е�����ֵ
	     *
	     * POCO::replaceIni() �����Ͽ��� POCO::changeIni() ���ơ�
	     * ���� POCO::replaceIni() ���ᱣ���������õĲ�νṹ��
	     * ����ֱ���滻��ָ���������������Ŀ��
	     *
	     * @code php
	     * // �������е�����Ϊ
	     * // +--- option_1 = old value
	     * // +--- option_group
	     * //   +-- option1 = old value
	     * //   +-- option2 = old value
	     * //   \-- option3 = old value
	     *
	     * // �滻���������
	     * $arr = array(
	     *      'option_1' => 'value 1',
	     *      'option_2' => 'value 2',
	     *      'option_group/option2' => 'new value',
	     * );
	     * POCO::replaceIni($arr);
	     *
	     * // �޸ĺ�
	     * // +--- option_1 = value 1
	     * // +--- option_2 = value 2
	     * // +--- option_group
	     * //   +-- option2 = new value
	     * @endcode
	     *
	     * �����������ִ�н�����Կ��� POCO::replaceIni() �� POCO::changeIni() ����Ҫ����
	     *
	     * �������� POCO::replaceIni() �ٶȱ� POCO::changeIni() ��ܶ࣬
	     * ���Ӧ�þ���ʹ�� POCO::replaceIni() ������ POCO::changeIni()��
	     *
	     * @param string|array $option Ҫ�޸ĵ����������ƣ���������������Ŀ������
	     * @param mixed $data ָ�����������ֵ
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
	     * ɾ��ָ��������
	     *
	     * POCO::cleanIni() ����ɾ��ָ����������Ŀ��������Ŀ��
	     *
	     * @param mixed $option Ҫɾ��������������
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
	     * ����ָ����Ķ����ļ����������ʧ���׳��쳣
	     *
	     * @code php
	     * POCO::loadClass('app_register_class');
	     * @endcode
	     *
	     * @param string $class_name Ҫ�������
	     * @param string|array $dirs ָ�������������·��
	     * @param boolean $throw ��û���ҵ�ָ����ʱ�Ƿ��׳��쳣
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
	     * ���һ��������·��
	     *
	     * ���Ҫʹ�� POCO::loadClass() ����ǿ���е��࣬��Ҫͨ�� POCO::import() �����������·����
	     *
	     * @code php
	     * POCO::import('/www/app');
	     * @endcode
	     *
	     * @param string $dir Ҫ��ӵ�����·��
	     * @param boolean $case_sensitive �ڸ�·���в������ļ�ʱ�Ƿ������ļ�����Сд
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
	     * �����ض��ļ���������Ƿ����ָ����Ķ���
	     *
	     * �÷����� $dirs �����ṩ��Ŀ¼�в��Ҳ����� $filename ����ָ�����ļ���
	     * Ȼ������ļ��Ƿ����� $class_name ����ָ�����ࡣ
	     *
	     * ���û���ҵ�ָ���࣬���׳��쳣��
	     *
	     * @code php
	     * POCO::loadClassFile('Smarty.class.php', $dirs, 'Smarty');
	     * @endcode
	     *
	     * @param string $filename Ҫ�����ļ����ļ���������չ����
	     * @param string|array $dirs �ļ�������·��
	     * @param string $class_name Ҫ������
	     * @param string $dirname �Ƿ��ڲ����ļ�ʱ���Ŀ¼ǰ׺
	     * @param string $throw �Ƿ����Ҳ�����ʱ�׳��쳣
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
	
	        // �����ļ����ж�ָ�������ӿ��Ƿ��Ѿ�����
	        if (!class_exists($class_name, false) && ! interface_exists($class_name, false))
	        {
	            if ($throw)
	            {
	                throw new App_Exception(self::_t('%s ���ӿڻ�û����.', $class_name));
	            }
	            return false;
	        }
	        return $class_name;
	    }
	    
	    /**
	     * ����ָ�����ļ�
	     *
	     * �÷����� $dirs �����ṩ��Ŀ¼�в��Ҳ����� $filename ����ָ�����ļ���
	     * ����ļ������ڣ������ $throw ���������Ƿ��׳��쳣��
	     *
	     * �� PHP ���õ� require �� include ��ȣ�POCO::loadFile() ��ദ����������
	     *
	     * <ul>
	     *   <li>����ļ����Ƿ��������ȫ�ַ���</li>
	     *   <li>����ļ��Ƿ�ɶ���</li>
	     *   <li>�ļ��޷���ȡʱ���׳��쳣��</li>
	     * </ul>
	     *
	     * @code php
	     * POCO::loadFile('my_file.php', $dirs);
	     * @endcode
	     *
	     * @param string $filename Ҫ�����ļ����ļ���������չ����
	     * @param array $dirs �ļ�������·��
	     * @param boolean $throw ���Ҳ����ļ�ʱ�Ƿ��׳��쳣
	     *
	     * @return mixed
	     */
	    static function loadFile($filename, $dirs = null, $throw = true)
	    {
	        if (preg_match('/[^a-z0-9\-_.]/i', $filename))
	        {
	            throw new App_Exception(self::_t('%s �ļ�����·������ȷ.', $filename));
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
	
	        if ($throw) throw new App_Exception(self::_t('%s �ļ�����·������ȷ.', $filename));
	        return false;
	    }
	    
	    /**
	     * ����ָ�������Ψһʵ��
	     *
	     * POCO::singleton() ������й�����
	     *
	     * <ul>
	     *   <li>�ڶ���ע����в���ָ�������ƵĶ���ʵ���Ƿ���ڣ�</li>
	     *   <li>������ڣ��򷵻ظö���ʵ����</li>
	     *   <li>��������ڣ��������ඨ���ļ���������һ������ʵ����</li>
	     *   <li>���¹���Ķ�������������Ϊ�������Ǽǵ�����ע���</li>
	     *   <li>�����¹���Ķ���ʵ����</li>
	     * </ul>
	     *
	     * ʹ�� POCO::singleton() �ĺô����ڶ��ʹ��ͬһ������ʱ����Ҫ�����������
	     *
	     * @code php
	     * // ��λ�� A ��ʹ�ö��� My_Object
	     * $obj = POCO::singleton('My_Object');
	     * ...
	     * ...
	     * // ��λ�� B ��ʹ�ö��� My_Object
	     * $obj2 = POCO::singleton('My_Object');
	     * // $obj �� $obj2 ����ָ��ͬһ������ʵ���������˶�ι��죬���������
	     * @endcode
	     *
	     * �� $persistent ����Ϊ true ʱ�����󽫱�����־ô洢����
	     * �й� $persistent ��������ϸ˵����ο� POCO::register() ������
	     *
	     * @param string $class_name Ҫ��ȡ�Ķ����������     
	     * @param mixed $init_data ��ʼ��ֵ:֧�ִ�����
	     * @param bool  $b_single  �Ƿ񷵻�Ψһ����Ĭ����
	     *
	     * @return object ���ض���ʵ��
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
	     * ���ض������ڶ���ע����еǼ�һ������
	     *
	     * �����߿��Խ�һ������Ǽǵ�����ע����У��Ա���Ӧ�ó�������λ��ʹ�� POCO::registry() ����ѯ�ö���
	     * �Ǽ�ʱ�����û��Ϊ����ָ��һ�����֣����Զ������������Ϊ�Ǽ�����
	     *
	     * @code php
	     * // ע��һ������
	     * POCO::register(new MyObject());
	     * .....
	     * // �Ժ�ȡ������
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
	     * @param object $obj Ҫ�ǼǵĶ���
	     * @param string $name ��ʲô���ֵǼ�
	     *
	     * @return object
	     */
	    static function register($obj, $name = null)
	    {
	        if (!is_object($obj))
	        {
	            // '%s ������ %s ����Ӧ����һ������.
	            throw new App_Exception(self::_t('%s ������ %s ����Ӧ����һ������.', __METHOD__, $obj));
	        }
	        if (is_null($name)) $name = get_class($obj);
	        $name = strtolower($name);
	        self::$_objects[$name] = $obj;
	        return $obj;
	    }
	
	    /**
	     * ����ָ�����ֵĶ���ʵ�������ָ�����ֵĶ��󲻴������׳��쳣
	     *
	     * @code php
	     * // ע��һ������
	     * POCO::register(new MyObject(), 'obj1');
	     * .....
	     * // �Ժ�ȡ������
	     * $obj = POCO::regitry('obj1');
	     * @endcode
	     *
	     * @param string $name Ҫ���Ҷ��������
	     *
	     * @return object ���ҵ��Ķ���
	     */
	    static function registry($name)
	    {
	        $name = strtolower($name);
	        if (isset(self::$_objects[$name]))
	        {
	            return self::$_objects[$name];
	        }
	        // û��ע����Ϊ "%s" �Ķ���
	        throw new App_Exception(self::_t('û��ע����Ϊ "%s" �Ķ���', $name));
	    }
	
	    /**
	     * ���ָ�����ֵĶ����Ƿ��Ѿ�ע��
	     *
	     * @param string $name Ҫ���Ķ�������
	     *
	     * @return boolean �����Ƿ��Ѿ��Ǽ�
	     */
	    static function isRegistered($name)
	    {
	        $name = strtolower($name);
	        return isset(self::$_objects[$name]);
	    }
	
	    /**
	     * ��ȡָ���Ļ������ݣ�������ݲ����ڻ��Ѿ�ʧЧ���򷵻� false
	     *
	     * @param string $cache_id ����� ID
	     * @param array  $policy �������
	     * @param string $backend_class Ҫʹ�õĻ������
	     * @return mixed �ɹ����ػ������ݣ�ʧ�ܷ��� false
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
	     * ����������д�뻺�棬ʧ���׳��쳣
	     *
	     * $data ����ָ��Ҫ��������ݡ���� $data ��������һ���ַ���������뽫������� serialize ����Ϊ true��
	     * $policy ����ָ�������ݵĻ�����ԣ����û���ṩ�ò�������ʹ�û�������Ĭ�ϲ��ԡ�
	     *
	     * ��������ͬ POCO::setCache()��
	     *
	     * @param string $cache_id ����� ID
	     * @param mixed  $data Ҫ���������
	     * @param array  $policy �������
	     * @param string $backend_class Ҫʹ�õĻ������
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
	     * ɾ��ָ���Ļ�������
	     *
	     * ͨ����ʧЧ�Ļ�������������������ʱ��ϣ����ĳЩ������ɺ�����������档
	     * ����������ݿ��¼��ϣ��ɾ���ü�¼�Ļ����ļ����Ա�����һ�ζ�ȡ����ʱ�������ɻ����ļ���
	     *
	     * @code php
	     * POCO::deleteCache($cache_id);
	     * @endcode
	     *
	     * @param string $id ����� ID
	     * @param array $policy �������
	     * @param string $backend_class Ҫʹ�õĻ������
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
	     * ���ַ�����������и�ʽ�������ظ�ʽ���������
	     *
	     * $input ����������ַ������������ԡ�,��Ϊ�ָ��������ַ���ת��Ϊһ�����顣
	     * ��������������ÿһ����Ŀʹ�� trim() ����ȥ����β�Ŀհ��ַ��������˵����ַ�����Ŀ��
	     *
	     * �÷�������Ҫ��;�ǽ����磺��item1, item2, item3�� �������ַ���ת��Ϊ���顣
	     *
	     * @code php
	     * $input = 'item1, item2, item3';
	     * $output = POCO::normalize($input);
	     * // $output ������һ�����飬������£�
	     * // $output = array(
	     * //   'item1',
	     * //   'item2',
	     * //   'item3',
	     * // );
	     *
	     * $input = 'item1|item2|item3';
	     * // ָ��ʹ��ʲô�ַ���Ϊ�ָ��
	     * $output = POCO::normalize($input, '|');
	     * @endcode
	     *
	     * @param array|string $input Ҫ��ʽ�����ַ���������
	     * @param string $delimiter ����ʲô�ַ����зָ�
	     *
	     * @return array ��ʽ�����
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
	     * ���� POCO �����Զ�����
	     *
	     * @param string $class_name
	     */
	    static function autoload($class_name)
	    {
	        self::loadClass($class_name, false);
	    }
	
	    /**
	     * ע���ȡ��ע��һ���Զ������뷽��
	     *
	     * �÷����ο� Zend Framework��
	     *
	     * @param string $class �ṩ�Զ�����������
	     * @param boolean $enabled ���û���ø÷���
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
	     * ����ַ���ʽ��
	     *
	     * @return string
	     */
	    static function _t()
	    {
	    	$args = func_get_args();
	    	return call_user_func_array('sprintf', $args);
	    }
	    
	    /**
		 * ���ݵ��ýӿڲ�������ΨһHASHֵ�û�����ִ�н��
		 *
		 * @param array $api  �ӿ�API
		 * @param array $args ��������
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
	     * API����ִ�����
	     *
	     * @param array|string $api    ���ýӿ����ƣ�����ӿ���Ҫ��ʼ��ֵ����Ҫ��������
	     * 							   �磺array('member.get_friends_list', 30339651) 
	     * 							   ��һ��ֵ�ǽӿ����ơ�.��ǰ������ϵͳģ�����ƣ��󲿷��Ǿ�����õķ���
	     * 							   �ڶ���ֵ�Ƕ����ʼ����Ҫ��ֵ�������ʼ����Ҫ�������������ʹ������
	     * @param array|string $args   ������������������Ҫʹ�����鴫�룬ֻ��һ������ʱ�����ֱ�Ӵ���
	     * 							   �磺array('xxx','xxx') OR xxx     
	     * @param boolean      $use_cache �Ƿ��ִ�н������:ע�������ȡ�����б�Ľӿڲ�ʹ�ã�����ᵼ�������쳣
	     * @param int          $life_time ����ʱ�䣺Ĭ��900��
	     * @param boolean      $log    �Ƿ�������÷�����ִ������ļ�¼
	     * 
	     * @return array|string|boolean �ɹ�����ִ�н��
	     */
	    public static function execute($api, $args = array(), $use_cache = false, $life_time = 900, $log = false)
	    {
	    	// ��¼����ʼִ��ʱ��
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
	    		throw new App_Exception('�ӿڲ���Ϊ��');
	    	}
	    	if (!empty($args) && !is_array($args)) 
	    	{
	    		$args = array($args);
	    	}
	    	
	    	//�жϵ��õ�API�Ƿ��������
	    	$api_arr = self::ini('poco_api');
	    	if (empty($api_arr)) 
	    	{
	    		$api_arr = self::loadFile('poco_api_config.php', G_POCO_APP_PATH.'/config/');
	    	}   	
	    	if (!array_key_exists($method, $api_arr[$model])) 
	    	{
	    		if ($api_arr[$model][$method] === false) 
	    			throw new App_Exception("��API��ͣ��:{$model}.{$method}");
	    		else 
	    			throw new App_Exception("��ȷ�ϵ��õ�API��ȷ��Ч:{$model}.{$method}");
	    	}
	    	
	    	// ģ��ȫ��
	    	$model_full_name = "app_{$model}_class";
	    	
	    	// �����ִ�н������  	
	    	if ($use_cache == true) 
	    	{
	    		$cache_key = self::_get_hashcode($api, $args);
	    		// ��ȡ���棬���û�����ִ�нӿڣ��ѽ������(����ʹ��POCOĬ�ϻ���)
	    		$cache_data  = self::getCache($cache_key, null, 'POCO_Cache');
	    		if (empty($cache_data)) 
	    		{
	    			$obj = empty($init_data) ? self::singleton($model_full_name) : self::singleton($model_full_name, $init_data, false);
	    			$ret = call_user_func_array(array($obj, $method), $args);
	    			// ֻ�������ʱ��Ż���
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
	    	// ��������û����ֱ��ִ�нӿ�
	    	else 
	    	{
	    		$obj = empty($init_data) ? self::singleton($model_full_name) : self::singleton($model_full_name, $init_data, false);
	    		$ret = call_user_func_array(array($obj, $method), $args);
	    	}
	    	
	    	// ָ��Ҫдִ����־
	    	if ($log === true) 
	    	{
	    		// ����ִ�н���ʱ��
	    		$program_execte_time = microtime_float()-$program_start_time;
	    		$app_name = self::$_objects['app']->ini('app_config/APP_NAME');
	    		$details = "{$model_full_name} ģ���еķ��� {$method} ��ִ��ʱ��:{$program_execte_time}";
	    		// ��¼ִ��ϸ�ڵ����ݿ�
	    		App_Log::recordToDataBase($app_name, $model_full_name, $method, $details);
	    	}
	    	return $ret;
	    }
	}
}
/**
 * App_Debug::dump() �ļ�д���������һ������������
 *
 * @param mixed $vars Ҫ����ı���
 * @param string $label �������ʱ��ʾ�ı�ǩ
 * @param boolean $return �Ƿ񷵻��������
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
 * ���� POCO ���ö�����Զ�����
 */
POCO::registerAutoload();

//�����¼��
global $yue_login_id;
if( !defined('G_YUEYUE_CORE_LOGIN_SESSION_AUTHORISE') )
{
	//ֻ��һ��authorise������ѵ��������˳����û������µ�¼��
	define('G_YUEYUE_CORE_LOGIN_SESSION_AUTHORISE', 1);
	include_once(G_PHP_COMMON_ROOT_PATH . '/../pai/include/pai_login_session_class.inc.php');
	$pai_login_session_obj = new pai_login_session_class();
	$pai_login_info = $pai_login_session_obj->authorise();
	$yue_login_id = $pai_login_info['yue_id'];
}
