<?php
/**
 * @desc:   ��ҳ������ҳ�����޸�
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/7/20
 * @Time:   9:04
 * version: 2.0
 */
include('common.inc.php');
$cat_list = include_once('cat_config.inc.php');//��������
$versions_list = include_once('versions_config.inc.php');//�汾����
include_once("/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php");//Ƶ��
$cms_db_obj             = POCO::singleton ( 'cms_db_class' );
$cms_system_obj         = POCO::singleton ( 'cms_system_class' );
$rank_event_v2_obj = POCO::singleton( 'pai_rank_event_v2_class' );
$tpl = new SmartTemplate("rank_event_edit.tpl.htm");

$channel_list = $cms_db_obj->get_cms_list("channel_tbl");
$act = trim($_INPUT['act']);
$user_id = intval($yue_login_id);
$versions_id = intval($_INPUT['versions_id']);
$place = trim($_INPUT['place']);
$location_id = intval($_INPUT['location_id']);
$cat_id = intval($_INPUT['cat_id']);
$type = intval($_INPUT['type']);
$url  = trim($_INPUT['url']);
$cms_type = trim($_INPUT['cms_type']);
$rank_id = intval($_INPUT['rank_id']);
$cover_url = trim($_INPUT['cover_url']);
$headtile = trim($_INPUT['headtile']);
$subtitle = trim($_INPUT['subtitle']);
$pid = intval($_INPUT['pid']);
$app_sort = intval($_INPUT['app_sort']);
$rank_desc = trim($_INPUT['rank_desc']);
$sort_order = intval($_INPUT['sort_order']);
$status = intval($_INPUT['status']);

//�Զ���
$selected = "selected='true'";
$setParam = array('act'=>'insert','rank_id'=>0);
if($act == 'insert')//����
{

    if(strlen($place) <1) js_pop_msg_v2('����дλ��');
    if($location_id <1) js_pop_msg_v2('����д����');
    if($place == 'list')
    {
        //if($cat_id <1) js_pop_msg_v2('��ѡ�����');
        $data['cat_id'] = $cat_id;
    }
    $data['versions_id'] = $versions_id;
    $data['place'] = $place;
    $data['location_id'] = $location_id;
    $data['type'] = $type;
    if($type == 1)
    {
        if($rank_id <1) js_pop_msg_v2('����д��');
        if(strlen($cms_type) <1) js_pop_msg_v2('��ѡ�������');
        $data['cms_type'] =  $cms_type;
        $data['rank_id'] =  $rank_id;
    }
    else
    {
        if(strlen($url) <1) js_pop_msg_v2('����дurl');
        $data['url'] = $url;
    }
    $data['cover_url'] = $cover_url;
    $data['headtile'] = $headtile;
    $data['subtitle'] = $subtitle;
    $data['pid'] = $pid;
    $data['app_sort'] = $app_sort;
    $data['rank_desc'] = $rank_desc;
    $data['sort_order'] = $sort_order;
    $data['add_time'] = time();
    $data['add_id']   = $user_id;
    $data['status'] = $status;
    $ret = $rank_event_v2_obj->add_info_and_log($data,'insert');
    $retId = intval($ret['status']);
    if($retId >0)
    {
        js_pop_msg_v2('��ӳɹ�',false,'rank_event_list.php');
    }
    js_pop_msg_v2('���ʧ��');
}
elseif($act == 'edit')//�޸�
{
    $id = intval($id);
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    $ret = $rank_event_v2_obj->get_rank_event_info($id);
    foreach($cat_list as $k => &$v)//����
    {
        $v['selected'] = $ret['cat_id']==$v['cat_id'] ? true : false;
    }
    foreach($versions_list as &$val)
    {
        $val['selected'] = $ret['versions_id']==$val['versions_id'] ? true : false;
    }
    $ret['province']  = substr($ret['location_id'],0,6);
    $setParam['rank_id'] = intval($ret['rank_id']);
    $setParam['act'] = 'update';
    $tpl->assign($ret);

}
elseif($act == 'update')//��������
{
    if(strlen($place) <1) js_pop_msg_v2('����дλ��');
    if($location_id <1) js_pop_msg_v2('����д����');
    $id = intval($id);
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    if($place == 'list')
    {
        //if($cat_id <1) js_pop_msg_v2('��ѡ�����');
        $data['cat_id'] = $cat_id;
    }
    else
    {
        $data['cat_id'] = 0;
    }
    $data['versions_id'] = $versions_id;
    $data['place'] = $place;
    $data['location_id'] = $location_id;
    $data['type'] = $type;
    if($type == 1)
    {
        if($rank_id <1) js_pop_msg_v2('����д��');
        if(strlen($cms_type) <1) js_pop_msg_v2('��ѡ�������');
        $data['rank_id'] =  $rank_id;
        $data['cms_type'] =  $cms_type;
        $data['url'] = '';
    }
    else
    {
        if(strlen($url) <1) js_pop_msg_v2('����дurl');
        $data['rank_id'] = 0;
        $data['cms_type'] = '';
        $data['url'] = $url;
    }
    $data['cover_url'] = $cover_url;
    $data['headtile'] = $headtile;
    $data['subtitle'] = $subtitle;
    $data['pid'] = $pid;
    $data['app_sort'] = $app_sort;
    $data['rank_desc'] = $rank_desc;
    $data['sort_order'] = $sort_order;
    $data['status'] = $status;
    $ret = $rank_event_v2_obj->update_info_and_log($data,$id,'update');
    $retId = intval($ret['status']);
    if($retId >0)
    {
        js_pop_msg_v2('���³ɹ�',false,'rank_event_list.php');
    }
    js_pop_msg_v2('����ʧ��');
}
elseif($act == 'del')
{
    $id = intval($id);
    if($id <1) js_pop_msg_v2('�Ƿ�����');
    $ret = $rank_event_v2_obj->delete_info_and_log($id,'del');
    $retId = intval($ret['status']);
    $url = $_SERVER['REQUEST_URI'];
    if($retId >0)
    {
        echo "<script type='text/javascript'>window.alert('ɾ���ɹ�');location.href=document.referrer;</script>";
        exit;
    }
    echo "<script type='text/javascript'>window.alert('��ɾ��ʧ��');location.href=document.referrer;</script>";
    exit;
}
elseif ($act == 'rank')//��ʾ������ajax��ȡ
{
    $channel_id = intval($_INPUT['channel_id']);
    if ($channel_id <1)
    {
        echo 0;
    }
    $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
    $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");//ȡ��
    if ($rank_list)
    {
        foreach ($rank_list as $key => $vo)
        {
            $rank_list[$key]['rank_name'] = iconv("GBK", "UTF-8" , $vo['rank_name']);
        }
    }
    $arr  = array
    (
        'msg' => 'success' ,
        'ret' => $rank_list
    );
    echo json_encode($arr);
    exit;
}
$rank_info = $cms_system_obj->get_rank_info_by_rank_id($setParam['rank_id']);//��
if ($rank_info)
{
    $channel_id = $rank_info['channel_id'];
    foreach ($channel_list as $key => $vo)
    {
        if ($vo['channel_id'] == $channel_id)
        {
            $channel_list[$key]['channel_selected'] = $selected;
        }
    }
    $channel_id > 0 && $where = 'channel_id = ' . $channel_id;
    $rank_list = $cms_db_obj->get_cms_list("rank_tbl", $where, "*" ,"channel_id, sort_order");
    //ȡ��
    if ($rank_list)
    {
        foreach ($rank_list as $rank_key => $rank_vo)
        {
            if ($rank_vo['rank_id'] == $setParam['rank_id'])
            {
                $rank_list[$rank_key]['rank_selected'] = $selected;
            }
        }
    }
}

$tpl->assign('versions_list',$versions_list);
$tpl->assign('cat_list',$cat_list);
$tpl->assign($setParam);
$tpl->assign('channel_list', $channel_list);
$tpl->assign('rank_list', $rank_list);
$tpl->assign('user_id',$user_id);
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();