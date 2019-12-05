<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/21
 * @Time:   10:40
 * version: 1.0
 */
class pai_goods_test_class extends POCO_TDG
{
    /**
     * ¹¹Ôìº¯Êý
     *
     */
    public function __construct()
    {
        $this->setServerId( 22 );
        $this->setDBName( 'pai_log_db' );
    }
}