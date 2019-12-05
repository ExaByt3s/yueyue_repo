<?php
/**
 * 自定义一个异常处理类
 *
 * @author ERLDY
 */

class App_Exception extends Exception
{
    /**
     * 构造函数
     *
     * @param string $message 错误消息
     * @param int $code 错误代码
     */
    function __construct($message, $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * 输出异常的详细信息和调用堆栈
     *
     * @code php
     * QException::dump($ex);
     * @endcode
     */
    static function dump(Exception $ex)
    {
        $out = "系统异常：'" . get_class($ex) . "'";
        if ($ex->getMessage() != '')
        {
            $out .= " 发生错误 “{$ex->getMessage()}”";
        }

        $out .= " 在 {$ex->getFile()} : {$ex->getLine()}\n\n";
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