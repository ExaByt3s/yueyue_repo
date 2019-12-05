<?php

/**
 * ��Ʒ�������ҳ����ӻ��߱༭��
 *
 * 2015-6-17
 *
 * author  ����
 *
 */
include_once 'common.inc.php';


$pai_mall_goods_obj = POCO::singleton('pai_mall_goods_class');
$user_id = $yue_login_id;
$mall_obj = POCO::singleton('pai_mall_seller_class');

$goods_id = (int)$_INPUT['goods_id'];
$type_id = (int)$_INPUT['type_id'];


if(!empty($goods_id))
{
    $action="edit";
}
else
{
    $action="add";
}


//��ѯ�����ж�ģ��
$type_id_array = array(3,5,12,31,40,41,42,43);
if($action=="add")
{
    $type_id = (int)$_INPUT['type_id'];

}
else if($action=="edit")
{

    //�������Ҫ��ȡ���¸�������-2015-11-24
    $cache_info = $pai_mall_goods_obj->get_goods_info_by_goods_id($goods_id);//��ȡ����
    $type_id = $cache_info['data']['goods_data']['type_id'];
    if($type_id==42)
    {
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id,true);
    }
    else
    {
        $goods_info = $pai_mall_goods_obj->user_get_goods_info($goods_id,$user_id);
    }
    //�������Ҫ��ȡ���¸�������-2015-11-24

    //$type_id = $goods_info['data']['goods_data']['type_id'];
    //print_r($goods_info);

}

//������֤�ж�
$mall_service_check_obj = POCO::singleton('pai_mall_certificate_service_class');
$user_status_list = $mall_service_check_obj->get_service_status_by_user_id($user_id,false,"goods");//��Ӻ��������������ͨ����Լ�Ҳͨ��
foreach($user_status_list as $key => $value)
{
    if($value['can_add_goods']==1)
    {
        $can_edit_type_id_array[] = $value['type_id'];
    }
}
if(!in_array($type_id,$can_edit_type_id_array))
{
    //û�н����̼���֤��
    echo "<script>alert('�÷�����֤��ͨ����û��Ȩ�ޱ༭������Ʒ');window.location.href='./normal_certificate_basic.php';</script>";
    //header("location:./normal_certificate_basic.php");
    exit;
}





if(empty($type_id) || !in_array($type_id,$type_id_array))
{
    $type_id = 3;//Ĭ��
}

//��ȡ���õ�����
$config_array = pai_mall_load_config('goods_status');


$pc_wap = 'pc/';
/*****2015-10-22�༭�����л�ģ��***********/
if($action=="edit" && $type_id==41 && $goods_info['data']['goods_data']['is_show']=="1")
{
    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_edit_'.$type_id.'_special.tpl.htm');
}
else
{
    //����idѡȡ��ͬģ��
    $tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods_edit_'.$type_id.'.tpl.htm');
}
/*****2015-10-22�༭�����л�ģ��***********/

//ͳһ����type_id����ҳ������(2015-10-27)
$page_title = $pai_mall_goods_obj->get_goods_typename_for_type_id($type_id);

if($action=="add")
{
    $type_info = $pai_mall_goods_obj->user_show_goods_data($type_id);
    //print_r($type_info);
    $page_data = $type_info['data'];

}
else if($action=="edit")
{
    //ƥ���Ӧ����,�����ݽ��д���
    $page_data = $goods_info['data'];

    //��ʼ����Ʒ����ͼƬ
    $cover_img = "";
    $cover_img_count = count($page_data['goods_data']['img'])-1;
    foreach($page_data['goods_data']['img'] as $key => $value)
    {
        if($key<$cover_img_count)
        {
            $cover_img .= $value['img_url'].",";
        }
        else
        {

            $cover_img .= $value['img_url'];
        }
    }

    //�۸�ȡ��2015-7-7
    /*if($page_data['default_data']['prices']['value'])
    {
        $page_data['default_data']['prices']['value'] = (int)$page_data['default_data']['prices']['value'];

    }*/



    $tpl->assign("cover_img",$cover_img);

    //����༭���������ַ�
    $page_data['default_data']['content']['value'] = str_replace(array('&lt;','&gt;','&quot;'),array('<','>','"'),$page_data['default_data']['content']['value']);


}


//��������ѡ��ͬ�Ĵ����
$include_once_url = "module/goods_edit_".$type_id.".php";
//��ͬ��������ݴ���
if(file_exists($include_once_url))
{

    include_once $include_once_url;
    //echo "�ļ�����";
    //exit;
}
else
{
    echo "�ļ�������";
    exit();
}




//����ṹ�Ĺ�������
/*
 * //������Ϸ������ݽṹ����Ǯ�ĺ���
 *
 * @param array $source_data //Դ����
 * @param array $type_key //ƥ���key
 *
 */


function price_list_contruct($source_data,$type_key)
{
    foreach($source_data['prices_data'][$type_key]['child_data'] as $key => $value)
    {
        $new_price_list[$key]['id'] = $value['id'];
        $new_price_list[$key]['key'] = $value['key'];
        $new_price_list[$key]['name'] = $value['name'];
        $new_price_list[$key]['value'] = $source_data['prices_data_list'][$key]['value'];
        if($new_price_list[$key]['value']=="0")
        {
            $new_price_list[$key]['value'] = "";
        }
    }
    return $new_price_list;
}
/*
 * //������Ϸ������ݽṹ�ӷ���ĺ���
 *
 * @param array $source_data //Դ����
 * @param array $type_key //ƥ���key
 * @param array $action //����ԭʼ���� edit����add
 *
 */
//
function children_type_contruct($source_data,$type_key,$action)
{
    //�γ̷���
    $show_J_child_title = 1;//Ĭ����ʾ�ӷ���
    $has_choose = false;
    $target_detail_data_arr = array();

    $target_type_arr = $source_data['system_data'][$type_key]['child_data'];//���ͷֲ�����
    $target_type_match_value = $source_data['system_data'][$type_key]['value'];//ƥ���ֵ

    //����������ӷ���ṹ����ʾ��ϵ
    $i=0;
    foreach($target_type_arr as $key => $value)
    {

        //��������ӷ���ƥ��
        if(!empty($value['child_data']))
        {
            $target_detail_data_arr[$i]['child_data'] = $value['child_data'];
            //ƥ���Ӧ��ϵ
            $target_detail_data_arr[$i]['parent_id'] = $target_type_arr[$key]['id'];
            $target_detail_data_arr[$i]['parent_key'] = $target_type_arr[$key]['key'];

            if($action=="edit")
            {
                //ƥ���в������ӷ���
                if($target_type_match_value==$value["key"] && !empty($value['child_data']))
                {
                    $target_detail_data_arr[$i]['match'] = 1;//�����ӷ������ʾ
                }
            }
            else if($action=="add")
            {
                //�жϵ�һ�������û���ӷ���
                if(!empty($target_type_arr[0]['child_data']))
                {
                    $target_detail_data_arr[0]['match'] = 1;
                }
                else
                {
                    $target_detail_data_arr[0]['match'] = 0;
                }


            }
            $i++;
        }

        //ѡ�У�����û���ӷ���
        if($target_type_match_value==$value["key"] && empty($value['child_data']))
        {
            $show_J_child_title = 0;
        }

        //���Ƴ��ӷ����
        if($target_type_match_value==$value["key"])
        {
            $has_choose = true;
        }

    }

    //����ӣ��ж�Ĭ�Ϲ�ѡ���Ƿ����ӷ���
    if($action=="add")
    {
        if(empty($target_type_arr[0]["child_data"]))
        {
            $show_J_child_title = 0;
        }
    }
    //�Ǳ༭����û��ѡ��
    if($action=="edit" && !$has_choose)
    {
        $show_J_child_title = 0;
    }


    $return_array = array($target_detail_data_arr,$show_J_child_title);
    return $return_array;

}


//print_r($photo_detail_data_arr);
//echo $hide_J_child_title;

//���и�ֵ

$tpl->assign("default_data",$page_data['default_data']);
$tpl->assign("system_data",$page_data['system_data']);
$tpl->assign("show_J_child_title",$show_J_child_title);
$tpl->assign("new_price_list",$new_price_list);


// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');

// ������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');

// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ͷ��������ʽ��js����
$pc_global_top = _get_wbc_head();
$tpl->assign('pc_global_top', $pc_global_top);


// ͷ��bar
$global_top_bar = _get_wbc_global_top_bar();
$tpl->assign('global_top_bar', $global_top_bar);


// �ײ�
$footer = _get_wbc_footer();
$tpl->assign('footer', $footer);


$tpl->assign("goods_id",$goods_id);
$tpl->assign("type_id",$type_id);
$tpl->assign("user_id",$user_id);
$tpl->assign("page_title",$page_title);
$tpl->assign("action",$action);
$tpl->assign("hide_system_msg",$hide_system_msg);
$tpl->assign("system_msg",$system_msg);

$tpl->output();
?>