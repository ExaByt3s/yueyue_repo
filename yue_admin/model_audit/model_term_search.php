<?php

/*
 * @desc ģ�ؿ����ɸѡ
 * @author xiao xiao
 * @date
 * version 1
*/

ini_set("max_execution_time", 3600);
ini_set('memory_limit', '256M');
include_once 'common.inc.php';
include_once("/disk/data/htdocs232/poco/pai/yue_admin_v2/common/Excel_v2.class.php");//������
//��������
include_once ("/disk/data/htdocs232/poco/php_common/poco_location_fun.inc.php");
include_once 'include/common_function.php';

$time_start = microtime_float();//��ʼʱ��
$page_obj = new show_page ();
$show_count = 20;


$model_add_obj  = POCO::singleton('pai_model_add_class');
//ģ�ؿ�
$model_card_obj  = POCO::singleton('pai_model_card_class');


$tpl  = new SmartTemplate("model_term_search.tpl.htm");


$act       = trim($_INPUT['act']) ? trim($_INPUT['act']) : 'search';

$recycle   = 0;

$name      = trim($_INPUT['name']);
$phone     = trim($_INPUT['phone']);
$app_name  = trim($_INPUT['app_name']);
$uid       = intval($_INPUT['uid']);
$nick_name = trim($_INPUT['nick_name']);

//����
$sort      = trim($_INPUT['sort']) ? trim($_INPUT['sort']) : 'ptime_desc';

$where_str = "1 AND recycle = {$recycle}";
$setParam  = array();

if (strlen($name)>0)
{
    $where_str .= " AND name like '%".mysql_escape_string($name)."%'";
    $setParam['name'] = $name;
}
if (strlen($phone) >0)
{
    $where_str .= " AND phone = '".mysql_escape_string($phone)."'";
    $setParam['phone'] = $phone;
}
if (strlen($app_name)>0)
{
    $where_str .= " AND app_name like '%".mysql_escape_string($app_name)."%'";
    $setParam['app_name'] = $app_name;
}

if ($uid>0)
{
    $where_str .= " AND uid = {$uid}";
    $setParam['uid'] = $uid;
}
//�ǳ�
if (!empty($nick_name))
{
    $where_str .= " AND nick_name like '%".mysql_escape_string($nick_name)."%'";
    $setParam['nick_name'] = $nick_name;
}

//�ж��Ƿ���� ��������Ա
if(strlen($authority_list[0]['location_id']) >0)
{
    $where_str .= " AND location_id IN ({$authority_list[0]['location_id']})";
    //$setParam['aut_location'] = true;
}

//��ѯ
if ($act == 'search')
{
    $total_count = $model_add_obj->get_model_list(true, $where_str);
    $page_obj->setvar ($setParam);

    $page_obj->set ( $show_count, $total_count );
    $list = $model_add_obj->get_model_list(false, $where_str, change_sort($sort), $page_obj->limit(), $fields = '*');

}
//����
elseif ($act == 'export')
{
    $list = $model_add_obj->get_model_list(false, $where_str, change_sort($sort), '0,99999999', $fields = '*');
    $data = array();
    foreach ($list as $key => $vo)
    {
        $prof_data  = $model_add_obj->get_model_profession($vo['uid']);
        //��񼰷��۸�
        $style_data = $model_add_obj->list_style($vo['uid']);
        //������Ϣ
        $other_data = $model_add_obj->get_model_other($vo['uid']);
        //�����Ϣ
        $stature_data       = $model_add_obj->get_model_stature($vo['uid']);
        //�۸���
        $style_data = $model_add_obj->list_style($vo['uid']);
        //ģ�ؿ�
        $model_card_data = $model_card_obj->get_model_card_by_user_id($vo['uid']);

        //if(is_array($))
        $data[$key]['id']          = $key+1;
        $data[$key]['user_id']     = $vo['uid'];
        $data[$key]['name']        = $vo['name'];
        $data[$key]['nickname']    = $vo['nick_name'];
        $data[$key]['app_name']    = $vo['app_name'];
        $data[$key]['phone']       = $vo['phone'];
        $data[$key]['weixin_id']   = $vo['weixin_id'];
        $data[$key]['weixin_name'] = $vo['weixin_name'];
        $data[$key]['qq']          = $vo['qq'];
        $data[$key]['email']       = $vo['email'];
        /*$data[$key]['discuz_name'] = $vo['discuz_name'];
        $data[$key]['poco_name']   = $vo['poco_name'];*/
        $price_str = '';
        $style_str = '';
        if (is_array($style_data))
        {
            foreach ($style_data as $style_key => $style_val)
            {
                if($style_key != 0 && $style_val['style'])
                {
                    $price_str .= ',';
                    $style_str .= ',';
                }
                //���
                $style_str .= change_style_val($style_val['style']);
                $price_str .= isset($style_val['twoh_price']) ? $style_val['twoh_price'].'/2Сʱ,' : '';
                $price_str .= isset($style_val['fourh_price']) ? $style_val['fourh_price'].'/4Сʱ,' : '';
                $price_str .= isset($style_val['addh_price']) ? $style_val['addh_price'].'/��ʱ' : '';
            }
            # code...
        }
        $data[$key]['style']       = $style_str;
        $data[$key]['price']       = $price_str;
        $data[$key]['sex']         = $stature_data['sex'] == 0 ? 'Ů' : '��';
        $age                       = $stature_data['age'] ? date('Y', time()) - substr($stature_data['age'], 0, 4) : '';
        if ($age < 0 || $age > 100)
        {
            $data[$key]['age'] = '';
        }
        else
        {
            $data[$key]['age'] = $age;
        }
        $data[$key]['height'] = $stature_data['height'];
        $data[$key]['weight'] = $stature_data['weight'];
        $data[$key]['cup']    = change_into_text_by_cup_id($stature_data['cup_id']).get_cup_text_by_cup_a_id($stature_data['cup_a']);
        $data[$key]['chest']  = $stature_data['chest'].'/'.$stature_data['waist'].'/'.$stature_data['chest_inch'];
        $data[$key]['intro']  = $model_card_data['intro'];
        $data[$key]['information_sources'] = $other_data['information_sources'];
        $data[$key]['city']         = get_poco_location_name_by_location_id ($vo['location_id']);
        $data[$key]['inputer_name'] = $vo['inputer_name'];
        $data[$key]['inputer_time'] = $vo['inputer_time'];
        $data[$key]['alipay_info']  = $other_data['alipay_info'];
    }
    $fileName = "yueyue_excel";
    $headArr = array("���","ģ��ID","����","�ǳ�","APP�ǳ�","�ֻ�����","΢��","΢������","QQ","����","������","�۸�","�Ա�", "����","���", "����", "�ֱ�", "��Χ","��ע����","��Դ", "��פ����", "¼����", "¼��ʱ��", "֧�����˺�");
    $title = "ģ�ؿ�����";
    Excel_v2::start($headArr,$data,$fileName);
    $time_end = microtime_float();
    $time = $time_end - $time_start;
    if($yue_login_id == 100293) echo  $time;
    exit;
}
//����
switch ($sort) {
    case 'uid_desc':
        $sort_1 = "selected='true'";
        $tpl->assign('sort_1', $sort_1);
        break;
    case 'ptime_asc':
        $sort_2 = "selected='true'";
        $tpl->assign('sort_2', $sort_2);
        break;
    case 'ptime_desc':
        $sort_3 = "selected='true'";
        $tpl->assign('sort_3', $sort_3);
        break;
    default:
        # code...
        break;
}
//��פ�������ƻ�ȡ
if (!empty($list) && is_array($list))
{
    foreach ($list as $key_city => $vo)
    {
        $key_list = get_poco_location_name_by_location_id($vo['location_id']);
        //var_dump($key_list);
        $city_name = '';
        if (!empty($key_list))
        {
            $city_name = $key_list;
        }
        $list[$key_city]['city'] = $city_name;
    }

}
$tpl->assign('list', $list);
$tpl->assign('act', $act);
$tpl->assign($setParam);
$tpl->assign('total_count', $total_count);
$tpl->assign ( "page", $page_obj->output ( 1 ) );
$tpl->assign('MOBILE_ADMIN_TOP', $_POCO_STAT_YUE_ADMIN_REPORT_HEADER);
$tpl->output();

?>