<?php
/**
 * POCO事务类
 * 
 * @author  Hai
 * @version 2014-10-09
 */
class POCO_TRAN{

    private static $tran_times_arr;
    /**
     * 事务开始
     *
     * @param int $server_id
     * @return bool
     */
    public static function begin($server_id){

    	if ( !is_numeric( $server_id ) ) 
        {
        	trace("非法参数 server_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
        }
        if( self::$tran_times_arr[$server_id]<1 ){

        	$sql = " BEGIN;";
    	    db_simple_getdata($sql,false,$server_id);
			define('G_NOT_RECONNECT_MYSQL_DB', 1);	//启用事务后，禁止重连数据库。以防事务丢失（连接丢失）后，仍然继续执行。
        
        }
        self::$tran_times_arr[$server_id]++;
    	return true;

    }

    /**
     * 事务回滚
     *
     * @param int $server_id
     * @return bool
     */
    public static function rollback($server_id){

        if ( !is_numeric( $server_id ) ) 
        {
            trace("非法参数 server_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
        }
        if( self::$tran_times_arr[$server_id]<1 ){

            trace("tran_times_arr 为空  事务未开始",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;   

        }   
        $sql = " ROLLBACK;";
        db_simple_getdata($sql,false,$server_id);
        self::$tran_times_arr[$server_id] = 0;
        return true;

    }

    /**
     * 事务提交
     *
     * @param int $server_id
     * @return bool
     */
    public static function commmit($server_id){

        if ( !is_numeric( $server_id ) ) 
        {
            trace("非法参数 server_id 只能为整数",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;
        }
       	if( self::$tran_times_arr[$server_id]<1 ){

       	   	trace("tran_times_arr 为空  事务未开始",basename(__FILE__)." 行:".__LINE__." 方法:".__METHOD__);
            return false;	

       	}
        self::$tran_times_arr[$server_id]--;
        if( self::$tran_times_arr[$server_id] == 0 ){

        	$sql = " COMMIT;";
        	db_simple_getdata($sql,false,$server_id);
            self::$tran_times_arr[$server_id] = 0;
        	
        }
        return true;

    }
    
}

?>