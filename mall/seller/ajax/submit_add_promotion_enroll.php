<?php
/**
 * �ύ����
 * 2015.9.25
 * @author hudw <hudw@poco.cn>
 */
include_once 'config.php';

$price_arr = poco_iconv_arr($_REQUEST['detail'],'UTF-8','GBK');
//$price_arr = $_REQUEST['detail'];
// print_r($price_arr);
$topic_id = intval($_INPUT['topic_id']);

$mall_topic_obj = POCO::singleton('pai_topic_class');

if(empty($topic_id))
{
	$output_arr['msg']  = 'topic_id ����Ϊ��';
	$output_arr['code'] = 0;
	$output_arr['data'] = $ret;
	mall_mobile_output($output_arr,false);
	exit();
}

if(empty($yue_login_id))
{
    $output_arr['msg']  = 'yue_login_id ����Ϊ��';
    $output_arr['code'] = 0;
    $output_arr['data'] = $ret;
    mall_mobile_output($output_arr,false);
    exit();
}
$i=0;
foreach($price_arr as $k=>$p_val)
{
    foreach($p_val['price_arr'] as $vk=>$vp_val)
    {
        if(empty($vp_val['is_select']))
        {

            $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$p_val['goods_id'],$vp_val['type_key'],$vp_val['num'],$vp_val['price_text'],'delete');
        }
        else
        {
            $mall_topic_obj->add_promotion_enroll($topic_id,$yue_login_id,$p_val['goods_id'],$vp_val['type_key'],$vp_val['num'],$vp_val['price_text'],'add');
            $i++;
        }
    }
}
//print_r($price_arr);




// �ύ�ɹ�
$output_arr['msg']  = "��ϲ���ѳɹ����{$i}����������λ�����������ͨ��ԼԼ�ͷ���Ϣ֪ͨ��";
	  
$ret['url'] = './list.php';


$output_arr['code'] = 1;
$output_arr['data'] = $ret;

mall_mobile_output($output_arr,false);


?>