<?php
$osver  = $_REQUEST['osver'];
$appver = $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi = $_REQUEST['iswifi'];

$latest_version		= '1.0.0';   //���°汾
//$latest_down_url	= 'http://itunes.apple.com/cn/app/id935185009?mt=8'; //�汾���ص�ַ
$latest_down_url	= 'http://app.yueus.com/'; 
$latest_notice		= 'ԼԼv1.0.1: ||����2����װ�Σ�����Ƭ������������������iOS8������һ��Ҫ����Ŷ~||��  ���ݹ�+�ʺ�=�λã��������¹�Ч||��  ����Ƭ���ơ���ӡ���񣬰������Ʒ�ͼ������Ƭ��ӡ������|| ��  �Ż�ͼƬ���滭�ʣ�����������ή�ͻ��ʡ�'; 

$lowest_version     = '1.0.0';
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