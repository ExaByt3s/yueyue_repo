<?
$GLOBALS['LUCOCLIENT_SERVER_CONFIG'] = array(
'event' => array('host'=>'113.107.204.200','port'=>8810),
'restaurant' => array('host'=>'113.107.204.200','port'=>8810),
'cook' => array('host'=>'113.107.204.200','port'=>8809),//8809
'act' => array('host'=>'113.107.204.200','port'=>8809),//8809
'zip' => array('host'=>'113.107.204.190','port'=>28822),
'weibo' => array('host'=>'113.107.204.200','port'=>8809),//8809
'up_weibo' => array('host'=>'113.107.204.200','port'=>8812),
'info' => array('host'=>'113.107.204.200','port'=>8810),
'foodkey' => array('host'=>'113.107.204.200','port'=>8810),
'infokey' => array('host'=>'113.107.204.200','port'=>8810),
'telephone' => array('host'=>'113.107.204.200','port'=>8810),
'qing' => array('host'=>'113.107.204.200','port'=>8810),
'menber' => array('host'=>'113.107.204.200','port'=>8809),//8809
'quan' => array('host'=>'113.107.204.200','port'=>8810),//8809
'world' => array('host'=>'113.107.204.200','port'=>8810),//8809
//'mall' => array('host'=>'113.107.204.200','port'=>8810),//8809
'mall' => array('host'=>'172.18.5.14','port'=>8808),//8809
//'mall' => array('host'=>'14.29.52.14','port'=>8811),//8809
'test' => array('host'=>'113.107.204.190','port'=>28822),
'mall_test' => array('host'=>'172.18.5.14','port'=>8813),
);
//121.9.211.
//172.18.5.
//113.107.204.
$randid = rand(0,99);
//if($randid <= 5)
//{
	//$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'183.60.210.13','port'=>8811);
//}
//if($randid >= 95)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'183.60.210.12','port'=>8811);
//}
//if($randid >70 && $randid <= 85)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'113.107.204.217','port'=>8811);
//}
//else if($randid >55 && $randid <= 70)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'113.107.204.217','port'=>8813);
//}
//else if($randid >25 && $randid <= 55)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'172.18.5.200','port'=>8811);
//}
//else if($randid >5 && $randid <= 20)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'172.18.5.190','port'=>8811);
//}
//else if($randid <= 5)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['qing'] = array('host'=>'183.60.210.12','port'=>8811);
//}

//if($randid >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['info'] = array('host'=>'172.18.5.200','port'=>8811);
//}
//else if($randid <= 10)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['info'] = array('host'=>'172.18.5.190','port'=>8811);
//}

//if($randid >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['cook'] = array('host'=>'172.18.5.207','port'=>8809);
//}
//else if($randid >= 40 && $randid < 70)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['cook'] = array('host'=>'183.60.210.13','port'=>8811);
//}

//if($randid >= 60)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['restaurant'] = array('host'=>'183.60.210.12','port'=>8811);
//}
//else if($randid >= 60 && $randid < 70)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['restaurant'] = array('host'=>'183.60.210.13','port'=>8811);
//}
//else if($randid <= 30)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['restaurant'] = array('host'=>'172.18.5.190','port'=>8811);
//}

//if($randid >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['act'] = array('host'=>'183.60.210.13','port'=>8811);
//}
//if(rand(0,99) >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['event'] = array('host'=>'172.18.5.200','port'=>8811);
//}
//if(rand(0,99) >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['telephone'] = array('host'=>'172.18.5.200','port'=>8811);
//}

//if(rand(0,99) >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['foodkey'] = array('host'=>'172.18.5.190','port'=>8811);
//}
//if(rand(0,99) >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['infokey'] = array('host'=>'172.18.5.190','port'=>8811);
//}
//if(rand(0,99) >= 50)
//{
//	$GLOBALS['LUCOCLIENT_SERVER_CONFIG']['menber'] = array('host'=>'183.60.210.13','port'=>8811);
//}

$GLOBALS['THRIFT_ROOT'] = G_POCO_APP_PATH . '/include/fulltext_search_helper/lucoservice/';
include_once $GLOBALS['THRIFT_ROOT'] . 'luco_service.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/transport/TFramedTransport.php';

class LucoClient extends thrift_luco_serviceClient 
{
    private $transport=null;

    function __construct($host, $port,$settimeout = 0) {
		$this->transport = new TSocket($host, $port);
		if($settimeout == 0)
		{
			$this->transport->setSendTimeout(1000 * 30);
			$this->transport->setRecvTimeout(1000 * 30);
		}
		else if($settimeout == 1)
		{
			$this->transport->setSendTimeout(1000 * 10);
			$this->transport->setRecvTimeout(1000 * 10);
		}
		else
		{
			$this->transport->setSendTimeout(1000 * 5);
			$this->transport->setRecvTimeout(1000 * 5);
		}
        //$socket = new TSocket($host, $port);
        //$this->transport = new TFramedTransport($socket);
        $protocol = new TBinaryProtocol($this->transport);
        try {
        	
        	if( defined('G_YUEYUE_ROOT_PATH') )
        	{
        		$mtime_1 = microtime();
        		$mtime_1 = explode (' ', $mtime_1);
        		$mtime_1 = $mtime_1[1] + $mtime_1[0];
        	}
        	
            $this->transport->open();
            
            if( defined('G_YUEYUE_ROOT_PATH') )
            {
            	$mtime_2 = microtime();
            	$mtime_2 = explode (' ', $mtime_2);
            	$mtime_2 = $mtime_2[1] + $mtime_2[0];
            	if( $mtime_2-$mtime_1>=0.1 )
            	{
            		//临时日志  http://yp.yueus.com/logs/201509/22_luco_service.txt
            		pai_log_class::add_log(array('mtime_1'=>$mtime_1, 'mtime_2'=>$mtime_2, ), 'open1', 'luco_service');
            	}
            }
            
        }
        catch (Exception $error)
        {
			//$err_conn = mysql_connect("113.107.204.190","dev_java",'!Q@W#E$R');
			//mysql_select_db("test",$err_conn);
			//$charset="GBK";
			//mysql_query("SET character_set_connection=".$charset.", character_set_results=".$charset.", character_set_client=binary");
			//mysql_query("INSERT INTO err_log (onerrip,errmsgcontent,onerrday) VALUES ('".$host."','".$error."',Now())",$err_conn);
			//mysql_close($err_conn);
			//throw new TException($error);
            try {
				$this->transport->open();
				
				if( defined('G_YUEYUE_ROOT_PATH') )
				{
					$mtime_3 = microtime();
					$mtime_3 = explode (' ', $mtime_3);
					$mtime_3 = $mtime_3[1] + $mtime_3[0];
					if( $mtime_3-$mtime_2>=0.1 )
					{
						//临时日志  http://yp.yueus.com/logs/201509/22_luco_service.txt
						pai_log_class::add_log(array('mtime_2'=>$mtime_2, 'mtime_3'=>$mtime_3, ), 'open2', 'luco_service');
					}
				}
			}
			catch (Exception $error)
			{
				//throw new TException($error);
				try {
					$this->transport->open();
					
					if( defined('G_YUEYUE_ROOT_PATH') )
					{
						$mtime_4 = microtime();
						$mtime_4 = explode (' ', $mtime_4);
						$mtime_4 = $mtime_4[1] + $mtime_4[0];
						if( $mtime_4-$mtime_3>=0.1 )
						{
							//临时日志  http://yp.yueus.com/logs/201509/22_luco_service.txt
							pai_log_class::add_log(array('mtime_3'=>$mtime_3, 'mtime_4'=>$mtime_4, ), 'open3', 'luco_service');
						}
					}
				}
				catch (Exception $error)
				{
					
					if( defined('G_YUEYUE_ROOT_PATH') )
					{
						//临时日志  http://yp.yueus.com/logs/201509/22_luco_service.txt
						pai_log_class::add_log(array(), 'exception', 'luco_service');
					}
					
					//$err_conn = mysql_connect("113.107.204.190","dev_java",'!Q@W#E$R');
					//mysql_select_db("test",$err_conn);
					//$charset="GBK";
					//mysql_query("SET character_set_connection=".$charset.", character_set_results=".$charset.", character_set_client=binary");
					//mysql_query("INSERT INTO err_log (onerrip,errmsgcontent,onerrday) VALUES ('".$host."','".$error."',Now())",$err_conn);
					//mysql_close($err_conn);
					throw new TException("失败！请刷新页面！连接IP:".$host);			
				}	
			}
        }
        parent::__construct($protocol);
    }

    function close() {
        if($this->transport)
            $this->transport->close ();
    }
}

?>