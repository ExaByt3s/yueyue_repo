<?php
/**
 *  ��־��
 *  
 * @author henry
 * @copyright 2015-01-27
 */

class pai_log_class
{
    private static $_filepath; //�ļ�·��
    private static $_filename; //��־�ļ���
    private static $_filehandle; //�ļ����
    private static $_requestno = null;	//��־��
	
    /**
     * ��ʼ����¼��,д���¼
     * http://yp.yueus.com/logs/201502/03_info.txt
     * @param array $log_arr
     * @param string $title
     * @param string $prefix
     * @param string $dir
     * @return bool
     */
    public static function add_log($log_arr, $title='', $prefix='info', $dir='')
    {
    	$result = false;

    	//��ǰʱ��
    	$current_time = time();
    	
    	//����ʱ��
    	$request_time = $_SERVER["REQUEST_TIME"]*1;
    	if( is_null(self::$_requestno) ) self::$_requestno = $request_time . rand(10000, 99999);
    	
    	//�ļ�·��
    	if( strlen($dir)>0 )
    	{
    		self::$_filepath = $dir . '/' . date('Ym', $request_time);
    	}
        else 
        {
        	$cur_path = dirname(__FILE__);	//��ǰ·��
        	self::$_filepath = $cur_path . '/../logs/' . date('Ym', $request_time);
        }
        
        $title = trim($title);
        $prefix = trim($prefix);
        
        //�ļ���
        self::$_filename = date('d', $request_time) . '_' . $prefix . '.txt';
		
        //����·���ִ�
        $path = self::get_full_path(self::$_filepath, self::$_filename);
        
        //�ж��Ƿ���ڸ��ļ�
        if (!self::is_exist($path))
        { //������
            //û��·���Ļ���Ĭ��Ϊ��ǰĿ¼
            if ( !empty(self::$_filepath)  )
            {
                //����Ŀ¼
                if (!self::create_dir(self::$_filepath)) 
                { //����Ŀ¼���ɹ��Ĵ���
                    return $result;
                }
            }
            //�����ļ�
            if (!self::create_log_file($path))
            { //�����ļ����ɹ��Ĵ���
                return $result;
            }
        }
        
        //�ͻ���IP
        global $_INPUT;
        $ip_address = trim($_INPUT['IP_ADDRESS']);
        
        //��¼
        if( strlen($title)>0 ) $str[] = "TITLE:" . $title . "\r\n";
        $str[] = "CURRENT_TIME:" . date("Y-m-d H:i:s", $current_time) . "\r\n";
        $str[] = "REQUEST_TIME:" . date("Y-m-d H:i:s", $request_time) . "\r\n";
        $str[] = "REQUEST_NO:" . self::$_requestno . "\r\n";
        $str[] = "IP_ADDRESS:" . $ip_address . "\r\n";
        $str[] = "HTTP_REFERER:" . $_SERVER['HTTP_REFERER'] . "\r\n";
        $str[] = "HTTP_USER_AGENT:" . $_SERVER['HTTP_USER_AGENT'] . "\r\n";
        $str[] = "GET:" . self::get_url() . "\r\n";
        $str[] = "POST:" . self::post_data() . "\r\n";
        $str[] = "HTTP_RAW_POST_DATA:" . $GLOBALS["HTTP_RAW_POST_DATA"] . "\r\n";
        $str[] = "COOKIE:" . self::cookie_data() . "\r\n";
        if (is_array($log_arr))
        {
            foreach ($log_arr as $k => $v)
            {
            	if( is_array($v) ) $v = var_export($v, true);
                $str[] = $k . " : " . $v . "\r\n";
            }
        }
        else
        {
            $str[] = $log_arr . "\r\n";
        }
        $str[] = "|----------------------------------------------------------\r\n\r\n\r\n";
        $str = implode('', $str);
        
        //���ļ�
        self::$_filehandle = fopen($path, "a+");
        
        //д��־
        $result = fwrite(self::$_filehandle, $str);
        
        //�ر��ļ�
        fclose(self::$_filehandle);
        
        return (bool)$result;
    }

    /**
     * �ж��ļ��Ƿ����
     *
     * @param string $path
     * @return bool
     */
    private static function is_exist($path)
    {
        return file_exists($path);
    }

    /**
     * ����Ŀ¼
     *
     * @param string $dir
     * @return bool
     */
    private static function create_dir($dir)
    {
        return is_dir($dir) or (self::create_dir(dirname($dir)) and mkdir($dir, 0777));
    }

    /**
     * ������־�ļ�
     *
     * @param string $path
     * @return bool
     */
    private static function create_log_file($path)
    {
        $handle = fopen($path, "w"); //�����ļ�
        fclose($handle);
        return self::is_exist($path);
    }

    /**
     * ����·��
     *
     * @param string $dir
     * @param string $filename
     * @return string
     */
    private static function get_full_path($dir, $filename)
    {
        if ( empty($dir) )
        {
            return $filename;
        }
        else
        {
            return $dir . "/" . $filename;
        }
    }
    
    /**
     * ��ȡ����URL·��
     *
     * @return string
     */
    private static function get_url()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
        return 'http' . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 's' : '') . '://' . $host . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * ��ȡPOST����
     *
     * @return string
     */
    private static function post_data()
    {
        if(isset($_POST) && count($_POST)>0)
        {  
            $i = 0;
            foreach($_POST as $key=>$val)
            {
            	if( is_array($val) ) $val = var_export($val, true);
                $str[] = $i==0?$key.'='.$val:'&'.$key.'='.$val;
                $i++;
            }
            $str = implode('',$str);
            return $str;
        }
        else
        {
            return '';
        }
    }
    
    /**
     * ��ȡCOOKIE����
     *
     * @return string
     */
    private static function cookie_data()
    {
    	if(isset($_COOKIE) && count($_COOKIE)>0)
    	{
    		$i = 0;
    		foreach($_COOKIE as $key=>$val)
    		{
    			$str[] = $i==0?$key.'='.$val:'&'.$key.'='.$val;
    			$i++;
    		}
    		$str = implode('',$str);
    		return $str;
    	}
    	else
    	{
    		return '';
    	}
    }
}

