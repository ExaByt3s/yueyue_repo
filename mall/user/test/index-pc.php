<?php


// ע�⣺ �˳���Ϊ��ҳʹ�� ���𿽱�
    define("opacity",1);
// ע�⣺ �˳���Ϊ��ҳʹ�� end

$task_templates_root = TASK_TEMPLATES_ROOT;

// �°���ҳ�ı���
// hudw 2015.9.7
if(isset($index_template_root))
{
	$task_templates_root = $index_template_root.'/templates/default/';
}

//****************** pc��ͷ��ͨ�� start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView($task_templates_root.$pc_wap.'/index.tpl.htm');

// ͷ��css���
include_once($task_templates_root.$pc_wap. '/webcontrol/head.php');
// ͷ��bar
include_once($task_templates_root.$pc_wap. '/webcontrol/global-top-bar.php');
// �ײ�
include_once($task_templates_root.$pc_wap. '/webcontrol/footer.php');
// ��������
include_once($task_templates_root.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_ROOT);
// ================== pc��ͷ��ͨ�� end ==================

// ��Ŀ����
$location_id = $_COOKIE['yue_location_id'] ;

$obj = POCO::singleton('pai_home_page_topic_class');
$arr = $obj->get_pc_home_category_3_1_0($location_id);

foreach ( $arr as $k => $val ) 
{
	
    $arr[$k]['url'] = mall_yueyue_app_to_http($val['url']);
	
}

/**
foreach ( $arr  as $k => $val ) 
{
    if ($val['str'] == 'Լ�') 
    {
        $arr[$k]['is_hide'] = 1;
    }
}
**/

// �ؼ�������
$title_key = 'ԼԼ--���Ч��ʱ�����ƽ̨';
$keywords_key ='ԼԼ��ģ�أ���Ӱʦ��Լ�ģ�Լ�㣬����ʱ����̣����ܷ���';
$description_key ='ԼԼ�����Ч��ʱ�����O2Oƽ̨��ͨ��ʱ�����ƽ̨��ÿ���û����������Լ�������ʱ�䣬�ṩ��Ӱ�����ܵ���صķ����������ֵ��';


// ��banner
include_once ('/disk/data/htdocs232/poco/pai/yue_admin/cms/cms_common.inc.php');
$top_banner = new cms_system_class ();
$top_banner_ret = $top_banner->get_record_list_by_issue_id($b_select_conut=false, 36907 , $limit="0,3", $order_by="place_number ASC", $freeze=null, $where_str="");

$type_obj = POCO::singleton('pai_mall_goods_type_class');
$type_data = $type_obj->get_type_cate(0);

$tpl->assign('type_data',mall_output_format_data($type_data));
$tpl->assign('title_key', $title_key);
$tpl->assign('keywords_key', $keywords_key);
$tpl->assign('description_key', $description_key);

$tpl->assign('get_big_category', $get_big_category );
$tpl->assign('resa', $ret);
$tpl->assign('roll', $roll);

$tpl->assign('arr', $arr);
$tpl->assign('top_banner_ret', $top_banner_ret);

$output_arr['input_data']['data'] = array
(

    0 => array(
        'text' => '',
        'place_holder' => '�ؼ���',
        'default_text' => '',
        'default_url' => '',
        'type_id' => '',
        'search_type' => 'goods',
        'show' => true
    ),
    1 => array(
        'text' => '',
        'place_holder' => '��ƷID/�̼�����',
        'default_text' => '',
        'default_url' => '',
        'type_id' => '31',
        'search_type' => 'seller',
    )

);
$output_arr['input_data']['search_type'] = "goods";




// ��ҳ�����Ƽ�

$hot_tuijian  = $ret_index_v2['data']['module_list'][0]['exhibit'];

$tpl->assign('hot_tuijian',$hot_tuijian);

$tpl->assign('page_data',mall_output_format_data($output_arr));

?>