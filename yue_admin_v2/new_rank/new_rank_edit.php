<?php
/**
 * @desc:   �°��޸ĺ���ӡ��������
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/9/2
 * @Time:   11:10
 * version: 1.0
 */
include_once('rank_common.inc.php');
$module_list = include_once('module_onfig.inc.php'); //ģ����
$cms_rank_obj = new pai_cms_rank_class();//����
$tpl = new SmartTemplate( CMS_RANK_TEMPLATES_ROOT.'new_rank_edit.tpl.htm' );
$user_id = intval($yue_login_id);

$act = trim($_INPUT['act']);
$id = intval($_INPUT['id']);
$location_id = intval($_INPUT['location_id']);
$page_type = trim($_INPUT['page_type']);//�����ط�
$type_id = intval($_INPUT['type_id']);
$versions_id = intval($_INPUT['versions_id']);//�汾��
$link = trim($_INPUT['link']);//����
$title = trim($_INPUT['title']);//����
$img_url = trim($_INPUT['img_url']);//ͼƬ����
$remark = trim($_INPUT['remark']);//��ע
$switch = trim($_INPUT['switch']);//����
$order = intval($_INPUT['order']);

$setParam = array('act'=> 'insert');

//���log
$cms_rank_obj->add_info_and_log($id,0,$act);

if($act == 'insert')//����
{
    if(strlen($link)>0) $link = $cms_rank_obj->trimall($link);//���˿ո�
    if($location_id <1) js_pop_msg_v2('����ID����Ϊ��');
    if(strlen($page_type) <1)  js_pop_msg_v2('����λ�ò���Ϊ��');
    if(strlen($module_type)<1) js_pop_msg_v2('ģ�岻��Ϊ��');
    if(strlen($title) <1) js_pop_msg_v2('���ⲻ��Ϊ��');
    if($page_type == 'list' || $page_type == 'category_index')
    {
        if($type_id <1) js_pop_msg_v2('����ID����Ϊ��');
    }
    else
    {
        $type_id = 0;
    }//�����б�ҳʱtype_id��Ҫ
    $ret = $cms_rank_obj->create_main_rank($location_id, $page_type, $module_type,$type_id,$versions_id,$title, $order,$link, $img_url,$remark,$switch);
    print_r($ret);
    $retID = intval($ret['code']);
   if($retID >0) js_pop_msg_v2('��ӳɹ�',false,"new_rank_list.php");
    js_pop_msg_v2('���ʧ��');

}
elseif($act == 'edit')//�޸�
{
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    $ret = $cms_rank_obj->get_main_info_by_id($id);
    foreach($type_list as $k => &$v)//����
    {
        $v['selected'] = $ret['type_id']==$v['type_id'] ? true : false;
    }
    foreach($versions_list as &$val)
    {
        $val['selected'] = $ret['versions_id']==$val['versions_id'] ? true : false;
    }
    foreach($module_list as &$mv)
    {
        $mv['selected'] = $ret['module_type']==$mv['module_type'] ? true : false;
    }
    $ret['province']  = substr($ret['location_id'],0,6);
    $setParam['rank_id'] = intval($ret['rank_id']);
    $setParam['act'] = 'update';
    $tpl->assign($ret);
}
elseif($act == 'update') //����
{
    if(strlen($link)>0) $link = $cms_rank_obj->trimall($link);//���˿ո�
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    if(strlen($page_type) <1)  js_pop_msg_v2('����λ�ò���Ϊ��');
    if(strlen($module_type)<1) js_pop_msg_v2('ģ�岻��Ϊ��');
    if($page_type == 'list' || $page_type == 'category_index')
    {
        if($type_id <1) js_pop_msg_v2('����ID����Ϊ��');
    }
    else
    {
        $type_id = 0;
    }//�����б�ҳʱtype_id��Ҫ
    if(strlen($title) <1) js_pop_msg_v2('���ⲻ��Ϊ��');
    $ret = $cms_rank_obj-> update_main_rank_info($id,$location_id, $page_type, $module_type,$type_id,$versions_id, $title, $order, $link, $img_url,$remark,$switch);
    $retID = intval($ret['code']);
    /*if($yue_login_id == 100293)
    {
        if($retID>0) js_pop_msg_v2('���³ɹ�',false,"?act=edit&id={$id}");
    }*/
    if($retID>0) js_pop_msg_v2('���³ɹ�',false,"?act=edit&id={$id}");
    js_pop_msg_v2('����ʧ��');
}
elseif($act == 'del')//ɾ��
{
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    $ret = $cms_rank_obj->get_rank_info_by_main_id($id);
    if(is_array($ret) && !empty($ret))
    {
        echo "<script type='text/javascript'>window.alert('ɾ��ʧ�ܣ��񵥴�����������ɾ�������ٽ��в�����');location.href=document.referrer;</script>";
        exit;
    }
    $del_ret = $cms_rank_obj->del_main_rank($id);
    echo "<script type='text/javascript'>window.alert('ɾ���񵥳ɹ�');location.href=document.referrer;</script>";
}
$tpl->assign($setParam);
$tpl->assign('type_list',$type_list);
$tpl->assign('versions_list',$versions_list);
$tpl->assign('module_list',$module_list);
$tpl->assign('user_id',$user_id);
$tpl->assign($setParam);
$tpl->output();