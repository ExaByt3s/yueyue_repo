<?php
/**
 * @desc:   ������Ϣ��
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/22
 * @Time:   17:06
 * version: 1.0
 */
class pai_mall_message_class extends POCO_TDG
{
    /**
     * ���캯��
     */
    public function __construct()
    {
        $this->setServerId( 101 );
        $this->setDBName( 'pai_log_db' );
    }

    /**
     * ���÷���log
     */
    private function set_mall_message_tbl()
    {
        $this->setTableName ( 'pai_mall_all_message_log' );
    }

    /**
     *������֤��log
     */
    private function set_mall_code_tbl()
    {
        $this->setTableName ( 'pai_mall_send_tmp_code_log' );
    }

    /**
     * ����log����
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_mall_message($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $this->set_mall_message_tbl();
        return $this->insert ( $insert_data, "IGNORE" );
    }

    /**
     * ������֤��
     * @param  array $insert_data
     * @return int
     * @throws App_Exception
     */
    private function add_mall_code($insert_data)
    {
        if (empty ( $insert_data ) || !is_array($insert_data))
        {
            throw new App_Exception ( __CLASS__ . '::' . __FUNCTION__ . ':���鲻��Ϊ��' );
        }
        $this->set_mall_code_tbl();
        return $this->insert ( $insert_data, "REPLACE" );
    }
}