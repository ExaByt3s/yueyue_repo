<?php
/**
 * @desc:      
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/11/17
 * @Time:   11:09
 * version: 1.0
 */
include_once('common.inc.php');
include_once('top.php');
include_once(YUE_PA_CLASS_ROOT.'pai_url_qrcode_class.inc.php');
$pai_url_qrcode_obj = new pai_url_qrcode_class();

$tpl = new SmartTemplate( TEMPLATES_ROOT.'add.tpl.htm' );

$act = trim($_INPUT['act']);
$url = trim($_INPUT['url']);
$setParam = array('button_name'=>'����');

if($act == 'create')
{
    $qrcode_num = intval($_INPUT['qrcode_num']);
    if($qrcode_num <1) $qrcode_num = 1;//Ĭ������һ��
    if(strlen($url) <1) js_pop_msg_v2('ԼԼ���ӵ�ַ����Ϊ��',true);
    if(!filter_var($url, FILTER_VALIDATE_URL)) js_pop_msg_v2('ԼԼ���ӵ�ַ�Ƿ�',true);
    $remark = trim($_INPUT['remark']);
    $pattern = '/yueus.com/is'; //��ֹ������
    preg_match($pattern,$url,$match);
    if(!$match) js_pop_msg_v2('���Ӳ�����yueus.com�Ƿ���ַ',true);
    if($qrcode_num >100) js_pop_msg_v2('���ɸ�������100��');
    /*$count = $pai_url_qrcode_obj->get_url_qrcode_list(true,$yue_login_id,$url);
    if($count >=1) js_pop_msg_v2('���Ѿ����ɹ��ö�ά���ˣ������ٴ�����',true);*/
    $list = array();
    $setParam['url'] = $url;
    $setParam['remark'] = $remark;
    $setParam['qrcode_num'] = $qrcode_num;
    for($i=0;$i <$qrcode_num; $i++)
    {
        $ret = $pai_url_qrcode_obj->create_qrcode_img($url,$remark);
        $list[] = $ret;
        unset($ret);
    }

    //if(!$ret) js_pop_msg_v2('���ɶ�ά��ʧ��');
    $setParam['button_name'] = '��������';
    $tpl->assign('list',$list);
}
$tpl->assign($setParam);
$tpl->assign('YUE_ADMIN_V2_ADMIN_TEST_HEADER',$_YUE_ADMIN_V2_ADMIN_TEST_HEADER);
$tpl->output();