<?
	echo date('Y-m-d H:i:s')."<br/>";
	
	$url = 'http://cf.lmobile.cn/submitdata/service.asmx';
	$ci = curl_init();
	curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ci, CURLOPT_TIMEOUT, 30);
	curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ci, CURLOPT_URL, $url);
	$response = curl_exec($ci);
	curl_close($ci);
	
	echo date('Y-m-d H:i:s')."<br/>";
	
 	var_dump($response);

?>