<?php
/**
 * POCO������
 * 
 * @author  Hai
 * @version 2014-10-09
 */
class POCO_TRAN{

    private static $tran_times_arr;
    /**
     * ����ʼ
     *
     * @param int $server_id
     * @return bool
     */
    public static function begin($server_id){

    	if ( !is_numeric( $server_id ) ) 
        {
        	trace("�Ƿ����� server_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
        }
        if( self::$tran_times_arr[$server_id]<1 ){

        	$sql = " BEGIN;";
    	    db_simple_getdata($sql,false,$server_id);
			define('G_NOT_RECONNECT_MYSQL_DB', 1);	//��������󣬽�ֹ�������ݿ⡣�Է�����ʧ�����Ӷ�ʧ������Ȼ����ִ�С�
        
        }
        self::$tran_times_arr[$server_id]++;
    	return true;

    }

    /**
     * ����ع�
     *
     * @param int $server_id
     * @return bool
     */
    public static function rollback($server_id){

        if ( !is_numeric( $server_id ) ) 
        {
            trace("�Ƿ����� server_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
        }
        if( self::$tran_times_arr[$server_id]<1 ){

            trace("tran_times_arr Ϊ��  ����δ��ʼ",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;   

        }   
        $sql = " ROLLBACK;";
        db_simple_getdata($sql,false,$server_id);
        self::$tran_times_arr[$server_id] = 0;
        return true;

    }

    /**
     * �����ύ
     *
     * @param int $server_id
     * @return bool
     */
    public static function commmit($server_id){

        if ( !is_numeric( $server_id ) ) 
        {
            trace("�Ƿ����� server_id ֻ��Ϊ����",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
            return false;
        }
       	if( self::$tran_times_arr[$server_id]<1 ){

       	   	trace("tran_times_arr Ϊ��  ����δ��ʼ",basename(__FILE__)." ��:".__LINE__." ����:".__METHOD__);
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