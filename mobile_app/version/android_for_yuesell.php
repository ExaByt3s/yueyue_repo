<?php
$osver		= $_REQUEST['osver'];
$appver		= $_REQUEST['appver'];
$modelver	= $_REQUEST['modelver'];
$iswifi		= $_REQUEST['iswifi'];

//�汾������ȥ������
$array_str  = explode('_', $appver);
$appver     = $array_str[0];

$latest_version		= '1.2.0';   //���°汾
$latest_down_url	= 'http://c.poco.cn/ys201510301335.apk'; //�汾���ص�ַ
$latest_notice		= '1��ȫ����ҳ������������������ɹ����ף�
2���������������ܣ�����Ʒ������ֻ�����ʱ�༭��
3�������������ģ����Ŷ��ִ����������� ��
4�������ӵ����ģ�����ӵ����ᣬ�������У�';

$lowest_version     = '1.2.0';
$lowest_notice      = '�㻹���Լ���ʹ�ã���������µ����°汾';

$array_result['result']     = "0000";         
$array_result['update']     = 0;

//if($_REQUEST['uid'] == 100008)
{
    if($appver){
        if(version_compare($appver, $lowest_version, '<'))
        {
            //�汾������Ͱ汾��ǿ�Ƹ���
            $array_result['result']     = "0000";
            $array_result['update']     = 2;
            $array_result['version']    = '2.2.1';
            $array_result['app_url']    = $latest_down_url;
            $array_result['detail']     = iconv('GBK', 'UTF-8', $latest_notice);
        }elseif(version_compare($appver, $lowest_version, '>=') && version_compare($appver, $latest_version, '<')){
            //�汾�������°汾��������Ͱ汾���������
            $array_result['result']     = "0000";
            $array_result['update']     = 1;
            $array_result['version']    = '2.2.1';
            $array_result['app_url']    = $latest_down_url;
            $array_result['detail']     = iconv('GBK', 'UTF-8', $lowest_notice);
        }else{
            //����Ҫ����
            $array_result['result']     = "0000";
            $array_result['update']     = 0;
        }
    }
}


echo json_encode($array_result);

?>