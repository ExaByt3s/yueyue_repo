<?php
/**
 * APP_Debug Ϊ�������ṩ�˵���Ӧ�ó����һЩ��������
 *
 * Debug �� FirePHP ��� Firefox ����ṩ��֧�֡�
 * FirePHP ����������ʱ�ܷ�������������Ϣ�����������Ϣ���ڡ�
 *
 * Ҫ���� Debug �� FirePHP ��֧�֣����� App_Debug::enableFirePHP() ���ɡ�
 *
 * @package debug
 */
abstract class App_Debug
{
    /**
     * �Ƿ�ʹ�� FirePHP
     *
     * @var boolean
     */
    protected static $_firephp_enabled = false;

    /**
     * ���� Debug �� FirePHP ��֧��
     */
    static function enableFirePHP()
    {
        self::$_firephp_enabled = true;
    }

    /**
     * ���� Debug �� FirePHP ��֧��
     */
    static function disableFirePHP()
    {
        self::$_firephp_enabled = false;
    }

    /**
     * �������������
     *
     * ��������� FirePHP ֧�֣��������������� FirePHP �����У���Ӱ��ҳ�������
     *
     * ����ʹ�� dump() �����д��ʽ��
     *
     * @code php
     * dump($vars, '$vars current values');
     * @endcode
     *
     * @param mixed $vars Ҫ����ı���
     * @param string $label ��ǩ
     * @param boolean $return �Ƿ񷵻��������
     */
    static function dump($vars, $label = null, $return = false)
    {
        if (! $return && self::$_firephp_enabled)
        {
            Debug_FirePHP::dump($vars, $label);
            return null;
        }

        if (ini_get('html_errors'))
        {
            $content = "<pre>\n";
            if ($label !== null && $label !== '')
            {
                $content .= "<strong>{$label} :</strong>\n";
            }
            $content .= htmlspecialchars(print_r($vars, true));
            $content .= "\n</pre>\n";
        }
        else
        {
            $content = "\n";
            if ($label !== null && $label !== '')
            {
                $content .= $label . " :\n";
            }
            $content .= print_r($vars, true) . "\n";
        }
        if ($return)
        {
            return $content;
        }
        if( function_exists('debug_print_backtrace') )
        {
        	echo "<!--\r\n";
        	debug_print_backtrace();
        	echo "-->\r\n";
        }
        echo $content;
        return null;
    }

    /**
     * ��ʾӦ�ó���ִ��·��
     *
     * ��������� FirePHP ֧�֣��������������� FirePHP �����У���Ӱ��ҳ�������
     */
    static function dumpTrace()
    {
        if (self::$_firephp_enabled)
        {
            Debug_FirePHP::dumpTrace();
            return;
        }

        $debug = debug_backtrace();
        $lines = '';
        $index = 0;
        $debug_count = count($debug);
        for ($i = 0; $i < $debug_count; $i ++)
        {
            if ($i == 0)
            {
                continue;
            }
            $file = $debug[$i];
            if (! isset($file['file']))
            {
                $file['file'] = 'eval';
            }
            if (! isset($file['line']))
            {
                $file['line'] = null;
            }
            $line = "#{$index} {$file['file']}({$file['line']}): ";
            if (isset($file['class']))
            {
                $line .= "{$file['class']}{$file['type']}";
            }
            $line .= "{$file['function']}(";
            if (isset($file['args']) && count($file['args']))
            {
                foreach ($file['args'] as $arg)
                {
                    $line .= gettype($arg) . ', ';
                }
                $line = substr($line, 0, - 2);
            }
            $line .= ')';
            $lines .= $line . "\n";
            $index ++;
        } // for

        $lines .= "#{$index} {main}\n";

        if (ini_get('html_errors'))
        {
            echo nl2br(str_replace(' ', '&nbsp;', $lines));
        }
        else
        {
            echo $lines;
        }
    }
}

