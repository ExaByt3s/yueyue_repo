<?php
/**
 * �Զ���һ���쳣������
 *
 * @author ERLDY
 */

class App_Exception extends Exception
{
    /**
     * ���캯��
     *
     * @param string $message ������Ϣ
     * @param int $code �������
     */
    function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * ����쳣����ϸ��Ϣ�͵��ö�ջ
     *
     * @code php
     * QException::dump($ex);
     * @endcode
     */
    static function dump(Exception $ex)
    {
        $out = "ϵͳ�쳣��'" . get_class($ex) . "'";
        if ($ex->getMessage() != '')
        {
            $out .= " �������� ��{$ex->getMessage()}��";
        }

        $out .= " �� {$ex->getFile()} : {$ex->getLine()}\n\n";
        $out .= $ex->getTraceAsString();

        if (ini_get('html_errors'))
        {
            echo nl2br(htmlspecialchars($out));
        }
        else
        {
            echo $out;
        }
    }
}
?>