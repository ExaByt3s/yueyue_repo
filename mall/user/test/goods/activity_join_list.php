<?php
/**
 * ���������ҳ��
 *
 * @author ����
 * @version $Id$
 * @copyright , 2015-11-25
 * @package default
 */

/**
 * ��ʼ�������ļ�
 */
include_once 'config.php';


//****************** pc��ͷ��ͨ�� start ******************
$pc_wap = 'pc/';
$tpl = $my_app_pai->getView(TASK_TEMPLATES_ROOT.$pc_wap.'goods/activity_join_list.tpl.htm');

// ͷ��css���
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/head.php');
// ͷ��bar
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/global-top-bar.php');
// �ײ�
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/footer.php');
// ��������
include_once(TASK_TEMPLATES_ROOT.$pc_wap. '/webcontrol/down-app-area.php');

$pc_global_top = _get_wbc_head();
$global_top_bar = _get_wbc_global_top_bar();
$footer = _get_wbc_footer();
$down_app_area = _get_wbc_down_app_area();


$tpl->assign('pc_global_top', $pc_global_top);
$tpl->assign('global_top_bar', $global_top_bar);
$tpl->assign('footer', $footer);
$tpl->assign('down_app_area', $down_app_area);

$tpl->assign('index_url', G_MALL_PROJECT_USER_INDEX_DOMAIN);



$goods_id = $_INPUT['goods_id'];

// �����Ƿ��жϴ���
$index_url_link = G_MALL_PROJECT_USER_ROOT . '/index.php';

if (empty($goods_id))
{
    header("location: ".$index_url_link);
}
// �Ƿ������ĸ
if(preg_match('/[A-Za-z]+/',$goods_id) && !isset($_INPUT['preview']))
{

    header("location: ".$index_url_link);
}


$ret = get_api_result('customer/sell_services.php',array(
    'user_id' => $yue_login_id,
    'goods_id' => $goods_id
));


//��ȡ��������
$order_obj = POCO::singleton('pai_mall_order_class');

$i=0;
$join_list = "";
foreach($ret['data']['roster']["value"] as $key => $value)
{
    $tmp_list = $order_obj->get_order_list_of_paid_by_stage($goods_id, $value["stage_id"],false,"","0,99999");
    foreach($tmp_list as $k => $v)
    {
        $tmp_list[$k]["user_icon"] = get_user_icon($v["buyer_user_id"],64);//��ȡ64��ͷ��
    }

    $join_list[$i]["section_join_list"] = $tmp_list;
    $join_list[$i]["section_name"] = $value["name"];
    $i++;
}





if($_INPUT['print'] == 1)
{
    print_r($ret);
    die();
}


$tpl->assign('resa', $ret);
$tpl->assign('join_list',$join_list);
$tpl->output();



?>
