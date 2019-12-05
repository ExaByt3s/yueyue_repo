<?
$GLOBALS['THRIFT_ROOT'] = '/disk/data/htdocs233/mypoco/fulltext_search_helper/lucoservice/';
include_once $GLOBALS['THRIFT_ROOT'] . 'luco_service.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/protocol/TBinaryProtocol.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/transport/TSocket.php';
require_once $GLOBALS['THRIFT_ROOT'] . '/transport/TFramedTransport.php';

class LucoClient extends luco_serviceClient 
{
    private $transport=null;

    function __construct($host, $port) {
		//$this->transport = new TSocket($host, $port);
        
		$socket = new TSocket($host, $port);
		$this->transport = new TFramedTransport($socket);
        $protocol = new TBinaryProtocol($this->transport);
        $this->transport->open();
        parent::__construct($protocol);
    }

    function close() {
        if($this->transport)
            $this->transport->close ();
    }
}

?>
