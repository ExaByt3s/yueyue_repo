<?php
/**
 * @desc:   �̳ǲ��ֲ���
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/10/15
 * @Time:   10:53
 * version: 1.0
 */

include_once('common.inc.php');

/*$str ='{"content":"��ϲ���ɹ�ע�ᣬԼԼΪ��׼���˷ḻ���Ż�������������� �ҵ� �� �Ż�ȯ�ڲ鿴����л���ԼԼ��֧�֡�","media_type":"text","notice_id":2344632,"send_time":1444924862,"send_user_id":"10002","send_user_role":"yueseller","to_user_id":"200137"}';

$str = iconv("GBK","UTF-8",$str);
print_r($str);
$response_arr = json_decode($str,TRUE);
var_dump($response_arr);
exit;*/

$gmclient= new GearmanClient();
#113.107.204.236
$gmclient->addServers("172.18.5.13:9245");
$gmclient->setTimeout(5000); // ���ó�ʱ

do
{
    $req_param['notice_id'] = 2344632;
    $result = $gmclient->do("chatlog_from_nid",json_encode($req_param) );
    //print_r($result);
}
while(false);
//while($gmclient->returnCode() != GEARMAN_SUCCESS);

/*$result = iconv('gbk', 'utf-8', $result);
var_dump($result);
exit;*/
print_r($result);
$result = trim($result);
$response_arr = json_decode($result,TRUE);;
var_dump($response_arr);

exit;
$goods_id = intval($_INPUT['goods_id']);
if($goods_id < 1) die('goods_id ���ڿ�');

$info = get_little_cate_by_goods_id($goods_id);

var_dump($info);


function get_little_cate_by_goods_id($goods_id)
{
    $mall_goods_obj = POCO::singleton('pai_mall_goods_class');//��Ʒ��
    $goods_id = intval($goods_id);
    if($goods_id <1) return '';
    $goods_info = $mall_goods_obj->get_goods_info($goods_id);
    if(is_array($goods_info) && !empty($goods_info))
    {
        $system_data = $goods_info['system_data'];
        if(!is_array($system_data)) $system_data = array();
        foreach($system_data as $system_next)
        {
            foreach($system_next['child_data'] as $cate_type)
            {
                if($cate_type['key'] == $system_next['value']) return $cate_type['name'];

            }
        }
    }
    return '';
}