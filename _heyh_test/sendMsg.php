<?php
include_once ('/disk/data/htdocs232/poco/pai/poco_app_common.inc.php');

$str = '��ԼԼ�������͡�1��15�ա�31�� | ԼԼ��Ϊ�����ô󽱢��ڷ�Ů��30�������� 500Ԫ/�ˡ�����������a. ָ��ʱ����������5����Ȼ���׵�ǰ30��ģ�أ�������Ľ��ײ�������㣩��b.ÿ��Լ�ĵ��۲�����100Ԫ/2Сʱ��c.5������������3�������ϲ�ͬ��ӰʦԼ�ģ�������Ů��10������Ʒ��55�������¶��ٵ�����������������ԼԼ��΢��Լ��ҳ���˿��������ǰ10�������Ͻ��������ʵ��������Ա������ʵ�ԡ�����ܰ��ʾ����ԼŮ��΢�ţ�yueusmt���з����Լ���ģ�ؿ�������Ȧ������Լ�ĳɹ��ʣ����׻񽱡�';

$sql = "SELECT user_id FROM pai_db.pai_user_tbl WHERE role='model' AND pwd_hash != 'poco_model_db'";
$result = db_simple_getdata($sql, FALSE, 101);

foreach($result AS $key=>$val)
{
    echo $val['user_id'] . "<BR>";
    send_message_for_10002($val['user_id'],$str);

}

//send_message_for_10002($user_id,$str);



?>