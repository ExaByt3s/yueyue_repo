<?php
/**
 * @desc:   �����޸ĺ����ӿ�����
 * @User:   xiao xiao (xiaojm@yueus.com)
 * @Date:   2015/6/9
 * @Time:   13:36
 * version: 1.0
 */

include('common.inc.php');
include_once('/disk/data/htdocs232/poco/pai/yue_admin/common/yue_function.php');

$location_open_obj = POCO::singleton('pai_location_open_class');
$version_open_obj = POCO::singleton('pai_version_open_class');

$tpl = new SmartTemplate("location_open_edit.tpl.htm");

$act = trim($_INPUT['act']);

if($act == 'insert')   //����
{
    $location_id = intval($_INPUT['location_id']);
    $version_id  = intval($_INPUT['version_id']);

    if($location_id <1) js_pop_msg('����ID����Ϊ��');
    if($version_id <1) js_pop_msg('�汾ID����Ϊ��');
    $info = $location_open_obj->add_info($location_id,$version_id);

    if($info) js_pop_msg("��ӳɹ�");

    js_pop_msg("���ʧ��,���ܸð汾�ĵ����Ѿ�����");
}
elseif($act == 'edit') //�޸�
{
    $id = intval($_INPUT['id']);
    if($id <1) js_pop_msg("�Ƿ�����");
    $ret =  $location_open_obj->get_info($id);

    //��ȡlocation_idʡ������
    $prov_id = $province = substr($ret['location_id'], 0,6);
    $prov_ret = location_format($arr_locate_a,$prov_id);

    //��������
    $location_ret = ${'arr_locate_b'.$prov_id};
    $location_ret = location_format($location_ret,$ret['location_id']);

    //�汾
    $version_ret = $version_open_obj->get_list(false,'','',0,'','id DESC','0,99999999','version_num,id');
    $version_ret = format_selcted($version_ret,'id',$ret['version_id']);
    $tpl->assign('prov_ret',$prov_ret);//ʡ����
    $tpl->assign('location_ret',$location_ret);//ʡ����
    $tpl->assign('version_ret',$version_ret);//ʡ����
    $tpl->assign($ret);
    $tpl->assign('act','update');
    $tpl->output();
    exit;
}
elseif($act == 'update') //����
{
    $id = intval($_INPUT['id']);
    $location_id = intval($_INPUT['location_id']);
    $version_id  = intval($_INPUT['version_id']);
    if($id <1)  js_pop_msg("�Ƿ�����");
    if($location_id <1) js_pop_msg("����ID�������0");
    if($version_id <1)  js_pop_msg("�汾ID����Ϊ��");
    $info = $location_open_obj->update_info($id,$location_id,$version_id);
    if($info) js_pop_msg("���³ɹ�");
    js_pop_msg("���³ɹ�");
}
elseif($act == 'del') //ɾ��
{
    $id = intval($_INPUT['id']);
    if($id <1) js_pop_msg("�Ƿ�����");
    $info = $location_open_obj->del_info($id);

    if($info) js_pop_msg("ɾ���ɹ�",'',"location_open.php");
    js_pop_msg("ɾ��ʧ��",'',"location_open.php");
}


//�汾����
$version_ret = $version_open_obj->get_list(false,'','',0,'','id DESC','0,99999999','version_num,id');
$prov_ret = location_format($arr_locate_a);

$tpl->assign('version_ret',$version_ret);
$tpl->assign('prov_ret',$prov_ret);
$tpl->assign('act','insert');
$tpl->output();


/**
 * ��һά�����е�ʡ������ת��Ϊ��ά����
 *
 * @param array $ret  һά�����ʡ����
 * @param int $location_id
 * @return array
 */
function location_format($ret = array(),$location_id = 0)
{
    $ret_arr = array();
    $location_id = intval($location_id);
    if(!is_array($ret)) $ret = array();
    $i = 0;
    foreach($ret as $key=>$val)
    {
       if($location_id !=0 && $key == $location_id) $ret_arr[$i]['select'] = "selected='true'";//ѡ����
       $ret_arr[$i]['id']   = $key;
       $ret_arr[$i]['name'] = $val;
       $i ++;
    }
    return $ret_arr;
}

/**
 * ����һ��������±����ͬ�ıȽ���ͬʱ��ѡ��
 * @param array  $arr
 * @param string $selKey  �����±�
 * @param string $selVal  ֵ
 * @return array
 */
function format_selcted($arr,$selKey,$selVal)
{
    if(!is_array($arr)) $arr = array();
    foreach($arr as $key=>$val)
    {
        if($val[$selKey] == $selVal) $arr[$key]['select'] = "selected='true'";
    }
    return $arr;
}