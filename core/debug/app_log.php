<?php
/**
 * �� App_Log ʵ����һ���򵥵���־��¼����
 *
 * @author erldy
 */
class App_Log
{
	/**
	 * ���ȼ�
	 */
	const EMERG   = 'EMERG';   // Emergency: system is unusable
	const ALERT   = 'ALERT';   // Alert: action must be taken immediately
	const CRIT    = 'CRIT';    // Critical: critical conditions
	const ERR     = 'ERR';     // Error: error conditions
	const WARN    = 'WARN';    // Warning: warning conditions
	const NOTICE  = 'NOTICE';  // Notice: normal but significant condition
	const INFO    = 'INFO';    // Informational: informational messages
	const DEBUG   = 'DEBUG';   // Debug: debug messages

	/**
	 * ���ڸ�ʽ
	 *
	 * @var string
	 */
	protected $_date_format = 'Y-m-d H:i:s';

    /**
     * Ҫ��¼����־���ȼ�
     *
     * @var array
     */
    protected $_priorities = array(
		self::EMERG  => true,
		self::ALERT  => true,
		self::CRIT   => true,
		self::ERR    => true,
		self::WARN   => true,
		self::NOTICE => true,
		self::INFO   => true,
		self::DEBUG  => true,
    );

	/**
	 * ���������ڼ����־
	 *
	 * @var array
	 */
	protected $_log = array();

	/**
	 * �ѻ�����־���ݵĴ�С
	 *
	 * @var int
	 */
	protected $_cached_size = 0;

	/**
	 * ��־������С
	 *
	 * @var int
	 */
	protected $_cache_chunk_size = 65536;

    /**
     * ��־�ļ���
     *
     * @var string
     */
    protected $_filename;

    /**
     * ��־�����Ƿ��Ѿ�����д��׼��
     *
     * @var boolean
     */
    protected $_writeable = false;

    /**
     * ָʾ�Ƿ��Ѿ���������������
     *
     * @var boolean
     */
    private $_destruct = false;

	/**
	 * ��������
	 */
	function __destruct()
	{
        $this->_destruct = true;
		$this->flush();
	}

	/**
	 * ׷����־����־����
	 *
	 * @param string $msg
	 * @param int $type
	 */
	static function log($msg, $type = self::DEBUG)
	{
		static $instance;

        if (is_null($instance))
        {
			$instance = POCO::singleton('App_Log');
		}
		/* @var $instance App_Log */
		$instance->append($msg, $type);
    }

	/**
	 * ׷����־����־����
	 *
	 * @param string $msg
	 * @param int $type
	 */
	function append($msg, $type = self::DEBUG)
	{
		if (!isset($this->_priorities[$type])) return;

        $this->_log[] = array(time(), $msg, $type);
        $this->_cached_size += strlen($msg);

        if ($this->_cached_size <= $this->_cache_chunk_size)
        {
            $this->flush();
        }
    }

    /**
     * ���������־��Ϣд��ʵ�ʴ洢������ջ���
     */
    function flush()
    {
        if (empty($this->_log)) return;

        // ������־��¼���ȼ�
        $keys = POCO::normalize(POCO::ini('log_priorities'));
        $arr = array();
        foreach ($keys as $key)
        {
            if (!isset($this->_priorities[$key]))
            {
                continue;
            }
            $arr[$key] = true;
        }
        $this->_priorities = $arr;

        // ȷ����־д��Ŀ¼
        $dir = realpath(POCO::ini('log_writer_dir'));

        if ($dir === false || empty($dir))
        {
            $dir = realpath(POCO::ini('runtime_cache_dir'));
            if ($dir === false || empty($dir))
            {
                // LC_MSG: ָ������־�ļ�����Ŀ¼������ "%s".
                if ($this->_destruct)
                {
                    return;
                }
                else
                {
                    throw new App_Exception(POCO::_t('ָ������־�ļ�����Ŀ¼������ "%s".', POCO::ini('log_writer_dir')));
                }
            }
        }

        $filename = POCO::ini('log_writer_filename');

        $this->_filename = rtrim($dir, '/\\') . DS . $filename;

        $chunk_size = intval(POCO::ini('log_cache_chunk_size'));
        if ($chunk_size < 1)
        {
            $chunk_size = 64;
        }
        $this->_cache_chunk_size = $chunk_size * 1024;
        $this->_writeable = true;

        // д����־
        $string = '';

        foreach ($this->_log as $offset => $item)
        {
            list($time, $msg, $type) = $item;
            unset($this->_log[$offset]);
            // ���˵�����Ҫ����־��Ŀ
            if (!isset($this->_priorities[$type]))
            {
            	continue;
            }

            $string .= date('c', $time) . " {$type}: {$msg}\n";
        }

        if ($string)
        {
            $fp = fopen($this->_filename, 'a');
            if ($fp && flock($fp, LOCK_EX))
            {
                fwrite($fp, $string);
                flock($fp, LOCK_UN);
                fclose($fp);
            }
        }

        unset($this->_log);
        $this->_log = array();
        $this->_cached_size = 0;
    }
    
    /**
     * ��¼��־�����ݿ�
     *
     * @param string $app_id  Ӧ����ĿID
     * @param string $model   ģ��
     * @param string $method  ����
     * @param string $details ��־ϸ��
     * 
     * @return bool
     */
    static function recordToDataBase($app_id, $model, $method, $details)
    {
    	$app_log_obj = POCO::singleton('app_log_class');
    	return $app_log_obj->add_log($app_id, $model, $method, $details);
    }
}
