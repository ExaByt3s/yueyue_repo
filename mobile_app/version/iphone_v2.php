<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi = $_REQUEST['iswifi'];

$latest_version		= '1.1.0';   //���°汾
$latest_down_url	= 'http://app.yueus.com/'; //�汾���ص�ַ
$latest_notice		= '1����ҳȫ�¸İ棬��Ӱʦ���Է����Լ������ˡ�||2��ģ�ؿ��������Ƽ��Լ������뱨���������ᡣ||3�������Ż���';

$lowest_version     = '1.1.0';
$lowest_notice      = '�㻹���Լ���ʹ�ã���������µ����°汾';

$array_result['result']     = "0000";         
$array_result['update']     = 0;     

if($appver){
	if(version_compare($appver, $lowest_version, '<'))
	{
		//�汾������Ͱ汾��ǿ�Ƹ���
		$array_result['result']     = "0000";         
		$array_result['update']     = 2;            
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);    	
	}elseif(version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')){
		//�汾�������°汾��������Ͱ汾���������
		$array_result['result']     = "0000";         
		$array_result['update']     = 1;            
		$array_result['version']    = $latest_version;
		$array_result['app_url']    = $latest_down_url;
		$array_result['detail']     = iconv('GBK', 'UTF-8', $lowest_notice);  
	}else{
		//����Ҫ����
		$array_result['result']     = "0000";         
		$array_result['update']     = 0;   	
	}
}

echo json_encode($array_result);
?>