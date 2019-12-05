<?php
/**
 * @desc:   发送信息类
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/22
 * @Time:   17:06
 * version: 1.0
 */
class pai_mall_message_class extends POCO_TDG
{
    /**
     * 构造函数
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     * 设置发送log
     */
    private function set_mall_message_tbl()
    {
        $this->setTableName ( 'pai_mall_all_message_log' );
    }

    /**
     *保存验证码log
     */
    private function set_mall_code_tbl()
    {
        $this->setTableName ( 'pai_mall_send_tmp_code_log' );
    }

    /**
     * 插入log数据
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_mall_message($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_mall_message_tbl();
        return $this->insert ( $insert_data, "IGNORE" );
    }

    /**
     * 插入验证码
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_mall_code($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':数组不能为空' );
        }
        $this->set_mall_code_tbl();
        return $this->insert ( $insert_data, "REPLACE" );
    }
}